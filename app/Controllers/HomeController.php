<?php

class HomeController {
    public function index() {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/Bookbox/resources/views/dashboard.php';
        
        if (file_exists($path)) {
            require_once $path;
        } else {
            echo "Arquivo não encontrado: " . $path . "<br>";
        }
    }
}

