<?php

use App\Database;
use Slim\Views\PhpRenderer;
use App\Repositories\GoogleAuth;
use Google\Client;
use App\Repositories\UserRepository;
use App\Controllers\GoogleAuthentication;
use Psr\Container\ContainerInterface;
use App\Repositories\FileService;
use App\Repositories\LoggerService;


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
    },
    Client::class => function() {
        return new Client();
    },

    GoogleAuth::class => function(ContainerInterface $c) {
        return new GoogleAuth(
            $c->get(Client::class),
            $c->get(UserRepository::class)
        );
    },

    GoogleAuthentication::class => function(ContainerInterface $c) {
        return new GoogleAuthentication(
            $c->get(GoogleAuth::class)
        );
    },

    FileService::class => function() {
        return new FileService();
    },
    LoggerService::class => function(ContainerInterface $c) {
        return new LoggerService($c->get(Database::class));
    }
];
