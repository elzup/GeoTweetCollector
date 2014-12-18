<?php

class JobController {

    public function collectGeo($rule_id) {
        $tm = new TwitterModel();
        $dao = new TweetDBModel();
        $rule = $dao->load_rule($rule_id);

        $min_id = $dao->get_old_id($rule_id);
        $res = $tm->getGeoTweets($rule, $min_id);
        $statuses = $res->statuses;
        for ($i = 0; $i < 15; $i++) {
            $min_id = TwitterModel::get_min_id($res->statuses);
            $params = array(
                'max_id' => $min_id - 1,
            );
            $res = $tm->continueRequest($params);
            $statuses = array_merge($statuses, $res->statuses);
            var_dump($statuses);
        }
        echo PHP_EOL;
        echo count($statuses);
        if (!count($statuses)) {
            return;
        }
        $dao->insert_tweets($statuses, $rule_id);
    }

    public function testCollectGeo() {
        echo '<pre>';
        $tm = new TwitterModel();
        $dao = new TweetDBModel();
        $r = new Rule();
        $r->lat = '35.749412';
        $r->lon = '139.805108';
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

    public function submit() {
        $label = $_POST['label'];
        $date_text = $_POST['date'];
        $lat = $_POST['lat'];
        $lon = $_POST['lon'];
        $radius = $_POST['rad'];
        $rule = new Rule();
        $rule->set($label, $lat, $lon, $radius, $date_text);
        $dao = new TweetDBModel();
        $dao->insert_rule($rule);
    }
}
