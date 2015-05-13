<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

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
require_once('./models/cluster_model.php');

require_once('./helper/functions.php');

$app = new \Slim\Slim(array(
    'debug'              => true,
    'log.level'          => \Slim\Log::DEBUG,
    'log.enabled'        => true,
    'cookies.encrypt'    => true,    //cookie
));

// Web views
$app->get('/', '\PageController:eventIndex');
// $app->get('/', '\PageController:areaIndex');
// $app->get('/', '\PageController:showIndex');
$app->get('/t/:id', function ($id) use ($app) {
    $p = new \PageController();
    $p->areaTime($id);
});
$app->post('/job/submit',  '\JobController:submit');
$app->get('/test',  '\JobController:ps_test');
$app->get('/ps/:id', function ($id) {
    ini_set('memory_limit', '1024M');
    ini_set("max_execution_time",300);
    $jc = new \JobController();
    $jc->collectGeo($id);
});

$app->run();
