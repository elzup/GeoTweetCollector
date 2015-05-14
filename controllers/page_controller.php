<?php

class PageController {
    public function area() {
        $dao = new TweetDBModel();
        $tweets = $dao->load_tweets_recet(1000);
        require('./views/area.php');
    }

    public function showIndex() {
        $dao = new TweetDBModel();
        $rules = $dao->load_rules();
        require('./views/toppage.php');
    }

    public function areaTime($time) {
        $dao = new TweetDBModel();
        $tweets = $dao->load_tweets_recet(200, date("Y-m-d {$time}:00:00"));
        require('./views/area.php');
    }

    public function eventIndex($date = '2015-05-10') {
        $dao = new ClusterModel($date);
        $datas = $dao->get_values();
        require('./views/event.php');
    }

}
