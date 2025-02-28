<?php
// failure.php

$errorMessage = isset($data['error_message']) ? $data['error_message'] : 'An error occurred during the payment process.';
$reason = isset($data['reason']) ? $data['reason'] : 'No specific reason provided.';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failure</title>
</head>
<body>
    <h1>Payment Failed</h1>
    <p><?php echo htmlspecialchars($errorMessage); ?></p>
    <p>Reason: <?php echo htmlspecialchars($reason); ?></p>
    <a href="/checkout.php">Try Again</a>
    <p>If the problem persists, please <a href="/contact-support.php">contact support</a>.</p>
    <p>Return to the <a href="/index.php">homepage</a>.</p>
</body>
</html>
