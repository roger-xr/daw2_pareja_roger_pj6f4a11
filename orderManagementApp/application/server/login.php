<?php

require_once __DIR__ . "/helpers.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = [
        "username" => $_POST["user"] ?? "",
        "password" => $_POST["password"] ?? ""
    ];

    if ($user["username"] === "" || $user["password"] === "") {
        sendTextResponse(401, "Missing data");
    }

    $users = readUsers();

    if ($users === null) {
        sendTextResponse(500, "Users file not available");
    }

    $storedUser = findUser($users, $user["username"]);

    if ($storedUser !== null && password_verify($user["password"], $storedUser["password"])) {
        sendTextResponse(200, "Authentication successful");
    } else {
        sendTextResponse(401, "Authentication error");
    }

} else {
    sendTextResponse(405, "Method not allowed");
}
