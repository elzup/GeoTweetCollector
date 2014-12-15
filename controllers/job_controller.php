<?php

class JobController {

    public function collectGeo() {
        echo '<pre>';
        $tm = new TwitterModel();
        $dao = new TweetDBModel();
        $r = new Rule();
        $r->lat = '35.749412';
        $r->lng = '139.805108';
        $r->radius = '2';
        $r->date_text = '2014-12-13';
        $res = $tm->getGeoTweets($r);
        $statuses = $res->statuses;
        $min_id = TwitterModel::get_min_id($res->statuses);
        $params = array(
            'max_id' => $min_id - 1,
        );
        $res = $tm->continueRequest($params);
        $statuses = array_merge($statuses, $res->statuses);
        echo PHP_EOL;
        echo count($statuses);
        $dao->insert_tweets($statuses);
    }

    public function testCollectGeo() {
        echo '<pre>';
        $tm = new TwitterModel();
        $r = new Rule();
        $r->lat = '35.749412';
        $r->lng = '139.805108';
        $r->radius = '2';
        $r->date_text = '2014-12-13';
        var_dump($tweets);
        $tweets = $tm->getGeoTweets($r);
    }
}
