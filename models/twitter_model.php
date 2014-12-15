<?php

class TwitterModel {
    /** @var TwistOAuth */
    public $to;

    // 前回のリクエストを参照するため
    public $url;
    public $params;

    public function __construct() {
        $tokens = unserialize(AP_TWITTER_TOKENS);
        $token = $tokens[0];
        try {
            $this->to = new TwistOAuth($token['CONSUMER_KEY'], $token['CONSUMER_SECRET'], $token['ACCESS_TOKEN'], $token['ACCESS_TOKEN_SCRET']);
        } catch (TwistException $e) {
            $error = $e->getMessage();
        }
    }

    public function getGeoTweets(Rule $rule) {
        $this->params = array(
            'q' => '',
            'geocode' => $rule->getGeocode(),
            'until' => $rule->date_text,
            'count' => 2,
            'result_type' => 'recent',
        );
        $this->url = $this->last_url = 'search/tweets';
        return $this->to->get($this->url, $this->params);
    }

    public function continueRequest($update_params) {
        if (!isset($this->url)) {
            return NULL;
        }
        $this->params = array_merge($this->params, $update_params);
//        var_dump($this->params);
        return $this->to->get($this->url, $this->params);
    }

    public static function get_min_id($statuses) {
        $min = NULL;
        foreach ($statuses as $st) {
            if (!isset($min)) {
                $min = $st->id;
                continue;
            }
            $min = min($min, $st->id);
        }
        return $min;
    }
}
