<?php
require_once __DIR__ . "/helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    sendTextResponse(405, "Method not allowed");
}

$orders = loadOrdersOrFail();

renderPage(200, "All orders",
    "<h1>All orders</h1>" . renderOrdersTable($orders) . renderNavButtons());
