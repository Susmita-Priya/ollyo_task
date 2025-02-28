<?php
ini_set('memory_limit', '1G');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// index.php - Entry Point
require 'vendor/autoload.php';
require 'helper.php';

use App\Controllers\PaymentController;
use App\Views\View;

$paymentController = new PaymentController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentController->processPayment();
} else {
    echo view('checkout', []);
}

// helper.php
if (!function_exists('baseUrl')) {
    function baseUrl() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);

        return rtrim($protocol . "://" . $host . $scriptDir, '/');
    }
}

if (!function_exists('view')) {
    function view(string $name, array $data) {
        $file = __DIR__ . "/src/Views/" . $name . ".php";
        if (!file_exists($file)) {
            throw new Exception("View file not found: " . $file);
        }
        extract($data); // Extract data into variables
        ob_start();
        include $file;
        $content = ob_get_clean();
        return $content;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path = '/') {
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $redirectUrl = $path;
        } else {
            $redirectUrl = baseUrl() . '/' . ltrim($path, '/');
        }
        header('Location: ' . $redirectUrl);
        exit;
    }
}

if (!function_exists('jsonResponse')) {
    function jsonResponse(array $data, int $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
