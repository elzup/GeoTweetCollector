<?php

// DB constants
define('DB_TN_PREFIX', 'gc_');

define('DB_TN_TWEETS'               , DB_TN_PREFIX . 'tweets');
define('DB_CN_TWEETS_ID'            , 'id');
define('DB_CN_TWEETS_TWEET_ID'      , 'tweet_id');
define('DB_CN_TWEETS_TWEET_USER_ID' , 'tweet_user_id');
define('DB_CN_TWEETS_TEXT'          , 'text');
define('DB_CN_TWEETS_LATLNG'        , 'latlong');
define('DB_CN_TWEETS_RULES_ID'      , 'rule_id');
define('DB_CN_TWEETS_TIMESTAMP'     , 'timestamp');

define('DB_TN_RULES'           , DB_TN_PREFIX . 'rules');
define('DB_CN_RULES_ID'        , 'id');
define('DB_CN_RULES_LABEL'     , 'label');
define('DB_CN_RULES_DATE'      , 'date');
define('DB_CN_RULES_LATLON'    , 'latlong');
define('DB_CN_RULES_RADIUS_KM' , 'radius_km');
define('DB_CN_RULES_IS_ACTIVE' , 'is_active');

define('FORMAT_DATE' , 'Y-m-d');
define('FORMAT_DATE_STR' , 'Y年m月d日');
define('URL_GOOGLEMAP', 'https://www.google.co.jp/maps/place//@%LAT%,%LON%,17z');

// PATH
if (ENV == ENV_DEVELOP) {
    define('URL_ROOT' , '//localhost/tweetcollector/');
} else {
    define('URL_ROOT' , '//192.168.1.81/tweetcollector/');
}
//GeomFromText('POINT(139.766084 35.681382)')
