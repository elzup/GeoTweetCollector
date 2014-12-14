<?php

/* composer modules */
require_once('./vendor/autoload.php');

/* configs */
require_once('./config/keys.php');
require_once('./config/constants.php');

/* classes */
require_once('./classes/rule.php');
/* MVC */
require_once('./controllers/page_controller.php');
require_once('./controllers/job_controller.php');
require_once('./models/twitter_model.php');
require_once('./models/tweeet_db_model.php');

$app = new \Slim\Slim(array(
    'debug'              => true,
    'log.level'          => \Slim\Log::DEBUG,
    'log.enabled'        => true,
    'cookies.encrypt'    => true,    //cookie
));

// Web views
$app->get('/', '\PageController:showIndex');
$app->get('/job', '\JobController:collectGeo');

$app->run();
