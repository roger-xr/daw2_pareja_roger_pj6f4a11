<?php
require_once __DIR__ . "/helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    sendTextResponse(405, "Method not allowed");
}

$order = buildOrder($_POST);
if ($order === null) {
    sendTextResponse(400, "Invalid or missing order data");
}

$orders = file_exists(ordersPath()) ? readOrders() : [];
if ($orders === null) {
    renderError(404, "The orders file could not be opened.");
}

if (findOrderById($orders, $order["orderId"]) !== null) {
    sendTextResponse(409, "This order code already exists");
}

$orders[] = $order;

if (!writeJsonFile(ordersPath(), $orders)) {
    sendTextResponse(500, "The order could not be saved");
}

sendTextResponse(200, number_format($order["totalWithVat"], 2, ".", ""));
