<?php

class PageController {
    public function showIndex() {
        $dao = new TweetDBModel();
        $rules = $dao->load_rules();
        require('./views/toppage.php');
    }
}
