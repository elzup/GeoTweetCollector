<?php

/* composer modules */
require_once('./vendor/autoload.php');

/* classes */
require_once('./controllers/page_controller.php');

$app = new \Slim\Slim(array(
    'debug'              => true,
    'log.level'          => \Slim\Log::DEBUG,
    'log.enabled'        => true,
    'cookies.encrypt'    => true,    //cookie
));

// Web views
$app->get('/', '\PageController:showIndex');

$app->run();
