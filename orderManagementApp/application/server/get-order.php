<?php
require_once __DIR__ . "/helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    sendTextResponse(405, "Method not allowed");
}

$orderId = trim((string)($_GET["id"] ?? ""));
if ($orderId === "") {
    renderError(400, "Please enter an order code.");
}

$orders = loadOrdersOrFail();

$order = findOrderById($orders, $orderId);
if ($order === null) {
    renderError(404, "There is no order with the code " . $orderId . ".");
}

renderPage(200, "Order " . $orderId,
    "<h1>Order " . h($orderId) . "</h1>" . renderOrdersTable([$order]) . renderNavButtons());
