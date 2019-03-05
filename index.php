<?php
require '/Composer/vendor/autoload.php';
require '/controllers/controller.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$app->get('/', controller::class .':home');
$app->run();