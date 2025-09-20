<?php

use App\Database;
use Slim\Views\PhpRenderer;


return [

    Database::class => function() {

        return new Database(host: 'localhost',
            name: 'product_db',
            user: 'root',
            password: 'Sboy1379!#&(');
    },
    PhpRenderer::class => function() {

        $renderer = new PhpRenderer(__DIR__ . '/../views');
        $renderer->setLayout('layout.php');
        return $renderer;
    }
];
