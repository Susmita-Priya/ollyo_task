<h1>Order Confirmation</h1>

<p>Thank you for your order!</p>

<h2>Order Details</h2>
<ul>
    <li><strong>Transaction ID:</strong> <?php echo htmlspecialchars($transactionId); ?></li>
    <li><strong>Order Summary:</strong></li>
    <ul>
        <?php foreach ($orderDetails as $item): ?>
            <li><?php echo htmlspecialchars($item['name']); ?> - Quantity: <?php echo htmlspecialchars($item['quantity']); ?> - Price: <?php echo htmlspecialchars(number_format($item['price'], 2)); ?></li>
        <?php endforeach; ?>
    </ul>
</ul>

<p>Your order has been successfully processed. We appreciate your business!</p>
<p><a href="/order-history.php">View Order History</a></p>
<p>Return to the <a href="/index.php">homepage</a>.</p>