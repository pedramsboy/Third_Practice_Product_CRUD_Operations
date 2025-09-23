<?php

declare(strict_types=1);

use App\Controllers\ProductIndex;
use App\Controllers\Products;
use App\Middleware\GetProduct;
use Slim\Routing\RouteCollectorProxy;
use App\Middleware\RequireAPIKey;
use App\Controllers\Home;
use App\Middleware\AddJsonResponseHeader;
use App\Controllers\Signup;
use App\Controllers\Login;
use App\Middleware\ActivateSession;
use App\Controllers\Profile;
use App\Middleware\RequireLogin;
use App\Controllers\GoogleAuthentication;
use App\Middleware\ParsePatchFormData;

$app->group('', function (RouteCollectorProxy $group) {

    $group->get('/', Home::class);
    $group->get('/signup', [Signup::class, 'new']);
    $group->post('/signup', [Signup::class, 'create']);
    $group->get('/signup/success', [Signup::class, 'success']);
    $group->get('/login', [Login::class, 'new']);
    $group->post('/login', [Login::class, 'create']);
    $group->get('/logout', [Login::class, 'destroy']);
    $group->get('/profile', [Profile::class, 'show'])
    ->add(RequireLogin::class);

    $group->get('/auth/google', [GoogleAuthentication::class , 'redirect']);
    $group->get('/auth/google/callback', [GoogleAuthentication::class , 'callback']);

})->add(ActivateSession::class);

 $app->group('/api', function (RouteCollectorProxy $group) {

    $group->get('/products', ProductIndex::class);

    $group->post('/products', Products::class . ':create');

    $group->group('', function (RouteCollectorProxy $group) {

        $group->get('/products/{id:[0-9]+}', Products::class . ':show');

        // Update product data (without file) - PATCH
        $group->patch('/products/{id:[0-9]+}', Products::class . ':update')
        ->add(ParsePatchFormData::class);

        // Update product file - POST
        $group->post('/products/{id:[0-9]+}/file', Products::class . ':updateFile');

        $group->delete('/products/{id:[0-9]+}', Products::class . ':delete');

    })->add(GetProduct::class);

})->add(RequireAPIKey::class)
 ->add(AddJsonResponseHeader::class);

$app->get('/api/products/{id:[0-9]+}/file', Products::class . ':getFile')
    ->add(GetProduct::class)
    ->add(RequireAPIKey::class)
;