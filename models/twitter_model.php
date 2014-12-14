<?php

class TwitterModel {
    /** @var TwistOAuth */
    public $to;

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
        $params = array(
            'q' => '',
            'geocode' => $rule->getGeocode(),
            'until' => $rule->date_text,
            'count' => 100,
        );
        return $this->to->get('search/tweets', $params);
    }
}
