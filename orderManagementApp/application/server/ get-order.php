<?php

function readJsonFile($path) {
    if (is_file($path) || is_readable($path)) {
        $json = json_decode(file_get_contents($path), true);
    } else {
        $json = null; 
    }
    return $json;
}