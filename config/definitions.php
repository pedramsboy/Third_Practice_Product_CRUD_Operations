<?php
use App\Database;

return [

    Database::class => function() {

        return new Database(host: 'localhost',
            name: 'product_db',
            user: 'root',
            password: 'Sboy1379!#&(');
    }
];
