<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        //config connect database
        'db' =>[
            'host' => 'localhost',
            'dbname' => 'stockrestdb',
            'username' => 'root',
            'password' => 'root'
        ],
        "jwt" => [
            'secret' => 'kimhun55081557570sfsdfsdfsdf62020'
        ],
    ],
];
