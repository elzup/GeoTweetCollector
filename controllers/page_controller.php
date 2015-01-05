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
}
