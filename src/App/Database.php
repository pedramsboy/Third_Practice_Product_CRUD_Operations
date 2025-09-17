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
    public function getConnection(): PDO
    {
        $dsn="mysql:host=$this->host;dbname=$this->name;charset=utf8mb4";
        $username = "$this->user";
        $password = "$this->password";

        $pdo = new PDO($dsn, $username, $password,[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        return $pdo;
    }
}