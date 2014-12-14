<?php

class TweetDBModel extends PDO {
    public function __construct() {
        $this->engine = DB_ENGINE;
        $this->host = DB_HOST;
        $this->database = DB_NAME;
        $this->user = DB_USER;
        $this->pass = DB_PASSWORD;
        $dns = $this->engine . ':dbname=' . $this->database . ";host=" . $this->host;
        parent::__construct($dns, $this->user, $this->pass);
    }

    public function insert_tweets($statuses) {
        $sql = 'INSERT INTO `' . DB_TN_TWEETS . '` (`' . DB_CN_TWEETS_TWEET_ID . '`, `' . DB_CN_TWEETS_TWEET_USER_ID . '`, `' . DB_CN_TWEETS_TEXT . '`, `' . DB_CN_TWEETS_LATLNG . '`) VALUES ';
        $sql_values = array();
        foreach (range(1, count($statuses)) as $i) {
            $sql_values[] = "(:TID$i, :TUID$i, ':TEXT$i', GeomFromText('POINT(:LAT$i :LON$i)'))";
        }
        $sql .= implode(',', $sql_values);
        $stmt = $this->prepare($sql);
        foreach ($statuses as $i => $st) {
            $i++;
            $stmt->bindValue(":TID$i", $st->id);
            $stmt->bindValue(":TUID$i", $st->user->id);
            $stmt->bindValue(":TEXT$i", $st->text);
            $stmt->bindValue(":LAT$i", $st->geo->coordinates[0]);
            $stmt->bindValue(":LON$i", $st->geo->coordinates[1]);
        }
        return $stmt->execute();
    }

}
