<?php

class JobController {

    public function stream() {
        echo '<pre>';

        $positions[0] = '35.6472441';
        $positions[1] = '139.8098934';
        $positions[2] = '35.658124';
        $positions[3] = '139.824828';
        // TODO:
//        $dao = new TweetDBModel();

        $tokens = unserialize(AP_TWITTER_TOKENS);
        $token = $tokens[0];

        $consumer_key = $token['CONSUMER_KEY'];
        $consumer_secret = $token['CONSUMER_SECRET'];
        $oauth_token = $token['ACCESS_TOKEN'];
        $oauth_token_secret = $token['ACCESS_TOKEN_SCRET'];

        // APIのURL
        $url = 'https://stream.twitter.com/1.1/statuses/filter.json';
        // リクエストのメソッド
        $method = 'GET';

        // パラメータ
        $post_parameters = array(
        );
        $get_parameters = array(
//            'locations' => implode(',', $positions),
//            'locations' => '132.2,29.9,146.2,39.0,138.4,33.5,146.1,46.20',
            'locations' => '-122.75,36.8,-121.75,37.8,-74,40,-73,41',
        );
        $oauth_parameters = array(
            'oauth_consumer_key' => $consumer_key,
            'oauth_nonce' => microtime(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_token' => $oauth_token,
            'oauth_version' => '1.0',
        );

        // 署名を作る
        $a = array_merge($oauth_parameters, $post_parameters, $get_parameters);
        ksort($a);
        $base_string = implode('&', array(
            rawurlencode($method),
            rawurlencode($url),
            rawurlencode(http_build_query($a, '', '&', PHP_QUERY_RFC3986))
        ));
        $key = implode('&', array(rawurlencode($consumer_secret), rawurlencode($oauth_token_secret)));
        $oauth_parameters['oauth_signature'] = base64_encode(hash_hmac('sha1', $base_string, $key, true));


        // 接続＆データ取得
        // $fp = stream_socket_client("ssl://stream.twitter.com:443/"); でもよい
        $fp = fsockopen("ssl://stream.twitter.com", 443);
        if ($fp) {
//            fwrite($fp, "GET " . $url . ($get_parameters ? '?' . http_build_query($get_parameters) : '') . " HTTP/1.0\r\n"
            fwrite($fp, "GET " . $url . ($get_parameters ? '?' . http_build_query($get_parameters) : '') . " HTTP/1.0\r\n"
                . "Host: stream.twitter.com\r\n"
                . 'Authorization: OAuth ' . http_build_query($oauth_parameters, '', ',', PHP_QUERY_RFC3986) . "\r\n"
                . "\r\n");
            while (!feof($fp)) {
                var_dump(fgets($fp));
            }
            fclose($fp);
        }
    }

    public function collectGeo($rule_id) {
        $tm = new TwitterModel();
        $dao = new TweetDBModel();
        $rule = $dao->load_rule($rule_id);
//        https://www.google.co.jp/maps/place/東京都/@35.673343,139.710388,11z/data=!3m1!4b1!4m2!3m1!1s0x605d1b87f02e57e7:0x2e01618b22571b89?hl=ja
        $min_id = $dao->get_old_id($rule_id);
        echo $min_id[0];
        $res = $tm->getGeoTweets($rule, $min_id[0]);
        $statuses = $res->statuses;
        $is_end = FALSE;
        $dao->insert_tweets($statuses, $rule_id);
        exit;
        for ($i = 0; $i < 1; $i++) {
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
