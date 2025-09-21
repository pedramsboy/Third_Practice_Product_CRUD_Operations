<?php

declare(strict_types=1);

namespace App;

use PDO;

class Database
{

    public  function __construct(private string $host,
                                 private string $user,
                                 private string $name,
                                 private string $password
                                 )
    {

    }
//    public function getConnection(): PDO
//    {
//        $dsn="mysql:host=$this->host;dbname=$this->name;charset=utf8mb4";
//        $username = "$this->user";
//        $password = "$this->password";
//
//        $pdo = new PDO($dsn, $username, $password,[
//            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
//        ]);
//
//        return $pdo;
//    }

    public function getConnection(): PDO
    {
        $dsn = "mysql:host=$this->host;dbname=$this->name;charset=utf8mb4";

        $pdo = new PDO($dsn, $this->user, $this->password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_STRINGIFY_FETCHES => false, // Don't convert integers to strings
            PDO::ATTR_EMULATE_PREPARES => false   // Use native prepared statements
        ]);

        // Alternatively, you can set this after connection:
        // $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        // $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $pdo;
    }
}