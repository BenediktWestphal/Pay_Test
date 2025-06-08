<?php
require_once __DIR__ . '/vendor/autoload.php';

$apiKey = trim(getenv('MOLLIE_API_KEY'), "'= ");

if (!$apiKey || strlen($apiKey) < 30) {
    die("Ungültiger oder fehlender MOLLIE_API_KEY: " . htmlspecialchars($apiKey));
}

$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey($apiKey);

// Aktuelle Domain ermitteln
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . $host . dirname($_SERVER['PHP_SELF']);

// Zahlung erstellen
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "EUR",
        "value" => "10.00"
    ],
    "description" => "Testzahlung",
    "redirectUrl" => $baseUrl . "/thanks.html",
    "webhookUrl"  => $baseUrl . "/webhook.php", // optional
    "metadata" => [
        "order_id" => "12345"
    ],
]);

header("Location: " . $payment->getCheckoutUrl(), true, 303);
