<?php

function readJsonFile($path) {
    if (is_file($path) && is_readable($path)) {
        $jsonData = json_decode(file_get_contents($path), true);
    } else {
        $jsonData = null; 
    }
    return is_array($jsonData) ? $jsonData : null;
}