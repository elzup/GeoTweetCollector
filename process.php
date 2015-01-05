<?php

set_time_limit(0);

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

switch (@$argv[1]) {
case 1:
    $jc = new \JobController();
    $jc->ps_test();
    break;
case 2:
    if (!$id = @$argv[2]) {
        break;
    }
    $jc = new \JobController();
    while($jc->collectGeo($id)) {
        sleep(60 * 15);
    }
    break;
case 3:
    $jc = new \JobController();
    $jc->stream();
    break;
default:
    echo 'no args';
    break;
}
