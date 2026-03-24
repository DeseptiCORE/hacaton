<?php
namespace config;

class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            self::$connection = mysqli_connect(
                '127.0.0.1:3306',
                'root',
                '',
                'baza'
            );
        }
        return self::$connection;
    }
}