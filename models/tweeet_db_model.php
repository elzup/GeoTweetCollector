<?php

class TweetDBModel {
    public function __construct() {
        $this->engine = DB_ENGINE;
        $this->host = DB_HOST;
        $this->database = DB_NAME;
        $this->user = DB_USER;
        $this->pass = DB_PASSWORD;
        $dns = $this->engine . ':dbname=' . $this->database . ";host=" . $this->host;
        parent::__construct($dns, $this->user, $this->pass);
    }

    private function insert_tweets($tweets) {
        $sql = $this->prepare('INSERT INTO ' . DB_TN_TWEETS . ' (' . DB_CN_TWEETS_TWEET_ID . ', ' . DB_CN_TWEETS_TEXT . ', ' . DB_CN_TWEETS_LATLNG . ') VALUES ');
        $sql_values = array();
        foreach (range(1, count($tweets)) as $i) {
            $sql_values[] = "(:TID$i, :TEXT$i, :POS$i)";
        }
        $sql .= implode(',', $sql_values);
        $stmt = $this->prepare($sql);
        foreach ($words as $i => $word) {
            $i++;
            $stmt->bindValue(":WORD$i", $word->word);
            $stmt->bindValue(":TID$i", $word->twitter_id);
            $stmt->bindValue(":TS$i", date(MYSQL_TIMESTAMP, $word->timestamp));
        }
        return $stmt->execute();
    }

}
