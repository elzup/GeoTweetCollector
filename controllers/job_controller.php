<?php

class JobController {

    public function collectGeo($rule_id) {
        $tm = new TwitterModel();
        $dao = new TweetDBModel();
        echo $rule_id;
        var_dump($dao);
        $rule = $dao->load_rule($rule_id);

        $min_id = $dao->get_old_id($rule_id);
        $res = $tm->getGeoTweets($rule, $min_id);
        $statuses = $res->statuses;
        $is_end = FALSE;
        for ($i = 0; $i < 15; $i++) {
            $min_id = TwitterModel::get_min_id($res->statuses);
            $params = array(
                'max_id' => $min_id - 1,
            );
            $res = $tm->continueRequest($params);
            if (count($res->statuses) == 0) {
                $is_end = TRUE;
                $dao->update_rule_disactive($rule_id);
                break;
            }
            $statuses = array_merge($statuses, $res->statuses);
        }
        if (count($statuses)) {
            $dao->insert_tweets($statuses, $rule_id);
        }
        return !$is_end;
    }

    public function testCollectGeo() {
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
        $rule_id = $dao->insert_rule($rule);
        exec("sudo nohup php ./process.php 2 {$rule_id} &");
        header('Location: ' . URL_ROOT);
        exit;
    }

    public function ps_test() {
        for ($i = 0; $i < 60 * 5; $i++) {
            sleep(1);
            echo $i;
        }
    }
}
