<?php

use Ollyo\Task\Routes;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helper.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

define('BASE_PATH', dirname(__FILE__));
define('BASE_URL', baseUrl());

$products = [
    [
        'name' => 'Minimalist Leather Backpack',
        'image' => BASE_URL . '/resources/images/backpack.webp',
        'qty' => 1,
        'price' => 120,
    ],
    [
        'name' => 'Wireless Noise-Canceling Headphones',
        'image' => BASE_URL . '/resources/images/headphone.jpg',
        'qty' => 1,
        'price' => 250,
    ],
    [
        'name' => 'Smart Fitness Watch',
        'image' => BASE_URL . '/resources/images/watch.webp', 
        'qty' => 1,
        'price' => 199,
    ],
    [
        'name' => 'Portable Bluetooth Speaker',
        'image' => BASE_URL . '/resources/images/speaker.webp',
        'qty' => 1,
        'price' => 89,
    ],
];
$shippingCost = 10;

$data = [
    'products' => $products,
    'shipping_cost' => $shippingCost,
    'address' => [
        'name' => 'Sherlock Holmes',
        'email' => 'sherlock@example.com',
        'address' => '221B Baker Street, London, England',
        'city' => 'London',
        'post_code' => 'NW16XE',
    ]
];

Routes::get('/', function () {
    return view('app', []);
});

Routes::get('/checkout', function () use ($data) {
    return view('checkout', $data);
});

Routes::post('/checkout', function ($request) use ($data) {
    // Initialize PayPal API client with credentials
    $clientId =  'AZAPp9yt5jI5W_jKesvIFKSYZQfUFjt_LoYYCE_oGmLQZmnVMwA5RMtsMqyrla99SLJLWGfIR81ldaDc';
    $clientSecret = 'EG_Ql2TNyN2lYpydmFvGY-H0Pcp6pyWn3pCZHxs1iBVmj2bpQXDo8naSbIK2cLeYdLa4trvOFJoRyVmk';
    $environment = new \PayPalCheckoutSdk\Core\SandboxEnvironment($clientId, $clientSecret);
    $client = new \PayPalCheckoutSdk\Core\PayPalHttpClient($environment);

    // Create order
    $order = new \PayPalCheckoutSdk\Orders\OrdersCreateRequest();
    $order->prefer('return=representation');
    $order->body = [
        'intent' => 'CAPTURE',
        'purchase_units' => [[
            'amount' => [
                'currency_code' => 'USD',
                'value' => array_reduce($data['products'], function ($sum, $product) {
                    return $sum + $product['price'] * $product['qty'];
                }, $data['shipping_cost'])
            ]
        ]],
        'application_context' => [
            'cancel_url' => BASE_URL . '/checkout/cancel',
            'return_url' => BASE_URL . '/checkout/complete'
        ]
    ];

    try {
        $response = $client->execute($order);
        header('Location: ' . $response->result->links[1]->href);
        exit;
    } catch (\PayPalHttp\HttpException $ex) {
        echo $ex->statusCode;
        print_r($ex->getMessage());
    }
});

Routes::get('/checkout/complete', function () {
    return view('thank_you', []);
});

Routes::get('/checkout/cancel', function () {
    return view('payment_failed', ['message' => 'Payment was cancelled.']);
});

$route = Routes::getInstance();
$route->dispatch();
?>
