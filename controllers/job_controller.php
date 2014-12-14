<?php

class JobController {

    public function collectGeo() {
        echo '<pre>';
        $tm = new TwitterModel();
        $r = new Rule();
        $r->lat = '35.749412';
        $r->lng = '139.805108';
        $r->radius = '2';
        $r->date_text = '2014-12-13';
        $tweets = $tm->getGeoTweets($r);
        var_dump($tweets);
    }
}
