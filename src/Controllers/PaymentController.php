<?php

namespace App\Controllers;

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

class PaymentController
{
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                getenv('PAYPAL_CLIENT_ID'),
                getenv('PAYPAL_SECRET')
            )
        );
        $this->apiContext->setConfig([
            'mode' => getenv('PAYPAL_MODE'), // Change to 'live' for production
            'log.LogEnabled' => true,
            'log.FileName' => __DIR__ . '/../logs/PayPal.log',
            'log.LogLevel' => getenv('PAYPAL_LOG_LEVEL'),
            'cache.enabled' => true,
        ]);
    }

    public function createPayment(array $cartItems, float $totalAmount)
    {
        try {
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $items = [];
            foreach ($cartItems as $item) {
                $payPalItem = new Item();
                $payPalItem->setName($item['name'])
                    ->setCurrency('USD')
                    ->setQuantity($item['qty']) // Fix key from 'quantity' to 'qty'
                    ->setPrice($item['price']);
                $items[] = $payPalItem;
            }

            $itemList = new ItemList();
            $itemList->setItems($items);

            $amount = new Amount();
            $amount->setCurrency('USD')
                ->setTotal($totalAmount);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($itemList)
                ->setDescription('Payment for your order');

            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl(getenv('APP_URL') . '/payment-success')
                ->setCancelUrl(getenv('APP_URL') . '/payment-failure');

            $payment = new Payment();
            $payment->setIntent('sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirectUrls)
                ->setTransactions([$transaction]);

            $payment->create($this->apiContext);

            // Store payment info in session before redirecting
            setSession('paypal_payment_id', $payment->getId());
            setSession('order_data', ['cart' => $cartItems, 'total' => $totalAmount]);

            header('Location: ' . $payment->getApprovalLink());
            exit;
        } catch (\Exception $e) {
            logMessage('Error creating PayPal payment: ' . $e->getMessage());
            header('Location: /payment-failure?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function processPayment()
    {
        if (!isset($_GET['paymentId'], $_GET['PayerID'])) {
            $this->handleFailure('Invalid payment request.');
            return;
        }

        $paymentId = $_GET['paymentId'];
        $payerId = $_GET['PayerID'];

        try {
            $payment = Payment::get($paymentId, $this->apiContext);

            $execution = new PaymentExecution();
            $execution->setPayerId($payerId);

            $result = $payment->execute($execution, $this->apiContext);

            if ($result->getState() === 'approved') {
                $this->handleSuccess($result);
            } else {
                $this->handleFailure('Payment not approved.');
            }
        } catch (\Exception $e) {
            $this->handleFailure('Error processing payment: ' . $e->getMessage());
        }
    }

    public function handleSuccess($payment)
    {
        $transaction = $payment->getTransactions()[0];
        $amount = $transaction->getAmount()->getTotal();
        $transactionId = $payment->getId();

        // Retrieve stored order data
        $orderData = getSession('order_data');

        logMessage("Payment successful! Transaction ID: $transactionId, Amount: $$amount");

        // Clear session after successful payment
        clearSession();

        return view('success', ['transaction_id' => $transactionId, 'amount' => $amount, 'order' => $orderData]);
    }

    public function handleFailure($error)
    {
        logMessage("Payment failed: $error");
        return view('failure', ['error' => $error]);
    }

    // Add a method for handling payment cancellations
    public function handleCancel()
    {
        logMessage("Payment was cancelled by the user.");
        return view('failure', ['error' => 'Payment was cancelled by the user.']);
    }
}

// Define session handling functions
function setSession($key, $value)
{
    $_SESSION[$key] = $value;
}

function getSession($key)
{
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
}

function clearSession()
{
    session_unset();
}

// Define logging function
function logMessage($message)
{
    error_log($message);
}

// Define view rendering function
function view($template, $data = [])
{
    extract($data);
    include __DIR__ . "/../views/{$template}.php";
}
