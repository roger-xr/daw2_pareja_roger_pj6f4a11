<?php

const VAT_RATE = 0.21;

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function sendTextResponse($code, $text) {
    http_response_code($code);
    header("Content-Type: text/plain; charset=utf-8");
    echo $text;
    exit;
}

function usersPath()  { return __DIR__ . "/../../users/users.json"; }
function ordersPath() { return __DIR__ . "/../../orders/orders.json"; }

function readJsonFile($path) {
    if (!is_file($path) || !is_readable($path)) {
        return null;
    }
    $data = json_decode(file_get_contents($path), true);
    return is_array($data) ? $data : null;
}

function writeJsonFile($path, $data) {
    $directory = dirname($path);
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        return false;
    }
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($path, $json, LOCK_EX) !== false;
}


function readUsers()  { return readJsonFile(usersPath()); }
function readOrders() { return readJsonFile(ordersPath()); }

function findUser($users, $username) {
    foreach ($users as $user) {
        if (($user["user"] ?? "") === $username) {
            return $user;
        }
    }
    return null;
}

function getCatalog() {
    return [
        "sofa"         => ["name" => "Sofa",         "price" => 599.00],
        "dining_table" => ["name" => "Dining table", "price" => 349.00],
        "wardrobe"     => ["name" => "Wardrobe",     "price" => 429.00],
        "bed"          => ["name" => "Double bed",   "price" => 279.00],
    ];
}

function buildOrderProducts($quantities) {
    $products = [];
    foreach (getCatalog() as $key => $product) {
        $quantity = (int)($quantities[$key] ?? 0);
        if ($quantity > 0) {
            $products[$key] = [
                "name"      => $product["name"],
                "unitPrice" => $product["price"],
                "quantity"  => $quantity,
                "subtotal"  => round($product["price"] * $quantity, 2),
            ];
        }
    }
    return $products;
}

function calculateTotalWithVat($products) {
    $total = 0;
    foreach ($products as $product) {
        $total += $product["subtotal"];
    }
    return round($total * (1 + VAT_RATE), 2);
}

function getTextField($data, $key) {
    return trim((string)($data[$key] ?? ""));
}

function buildOrder($data) {
    $orderId = getTextField($data, "orderId");
    $customer = [
        "fullName" => getTextField($data, "fullName"),
        "address"  => getTextField($data, "address"),
        "email"    => getTextField($data, "email"),
        "phone"    => getTextField($data, "phone"),
    ];
    $quantities = is_array($data["quantities"] ?? null) ? $data["quantities"] : [];
    $products = buildOrderProducts($quantities);

    if ($orderId === "" || in_array("", $customer, true)
        || !filter_var($customer["email"], FILTER_VALIDATE_EMAIL)
        || count($products) === 0) {
        return null;
    }

    return [
        "orderId"      => $orderId,
        "customer"     => $customer,
        "products"     => $products,
        "totalWithVat" => calculateTotalWithVat($products),
    ];
}

function findOrderById($orders, $orderId) {
    foreach ($orders as $order) {
        if ((string)($order["orderId"] ?? "") === (string)$orderId) {
            return $order;
        }
    }
    return null;
}

function renderNavButtons() {
    return '<div class="button-group">'
        . '<a class="button button-secondary" href="../client/view-orders-menu.html">Back to view orders</a>'
        . '<a class="button button-secondary" href="../client/menu.html">Back to operations menu</a>'
        . '<a class="button button-secondary" href="../client/index.html">Back to start</a>'
        . '</div>';
}

function renderPage($status, $title, $content) {
    http_response_code($status);
    header("Content-Type: text/html; charset=utf-8");
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">'
        . '<meta name="viewport" content="width=device-width, initial-scale=1.0">'
        . '<title>' . h($title) . ' - Furniture Order Manager</title>'
        . '<link rel="stylesheet" href="../client/css/style.css"></head>'
        . '<body><main class="card">' . $content . '</main></body></html>';
    exit;
}

function renderError($status, $message) {
    renderPage($status, "Error",
        '<h1>Error</h1><p class="message error">' . h($message) . '</p>' . renderNavButtons());
}

function loadOrdersOrFail() {
    $orders = readOrders();
    if ($orders === null) {
        renderError(404, "The orders file could not be found or opened.");
    }
    return $orders;
}

function renderProductsCell($products) {
    $lines = [];
    foreach ($products as $product) {
        $lines[] = h($product["name"]) . " x " . (int)$product["quantity"]
            . " (" . number_format($product["subtotal"], 2, ".", "") . " EUR)";
    }
    return implode("<br>", $lines);
}

function renderOrderRow($order) {
    return "<tr>"
        . "<td>" . h($order["orderId"]) . "</td>"
        . "<td>" . h($order["customer"]["fullName"]) . "</td>"
        . "<td>" . h($order["customer"]["address"]) . "</td>"
        . "<td>" . h($order["customer"]["email"]) . "</td>"
        . "<td>" . h($order["customer"]["phone"]) . "</td>"
        . "<td>" . renderProductsCell($order["products"]) . "</td>"
        . "<td>" . number_format($order["totalWithVat"], 2, ".", "") . " EUR</td>"
        . "</tr>";
}

function renderOrdersTable($orders) {
    if (count($orders) === 0) {
        return "<p>There are no orders yet.</p>";
    }
    $html = "<table><thead><tr><th>Order code</th><th>Customer full name</th>"
        . "<th>Address</th><th>Email</th><th>Phone number</th>"
        . "<th>Products</th><th>Total price with 21% VAT</th></tr></thead><tbody>";
    foreach ($orders as $order) {
        $html .= renderOrderRow($order);
    }
    return $html . "</tbody></table>";
}
