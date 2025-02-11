<?php

class Database {
    private static $connection;

    public static function connect() {
        if (!self::$connection) {
            self::$connection = new PDO("mysql:host=localhost;dbname=dbbookbox", "root", "");
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }
}