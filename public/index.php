<?php
session_start(); 

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../routes/web.php';

spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
