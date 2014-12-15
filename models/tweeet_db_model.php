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

    public function insert_tweets_wrap($statuses) {
        foreach ($statuses as $st) {
            $this->insert_tweets(array($st));
        }
    }

    public function insert_tweets($statuses) {
        $sql = 'INSERT INTO `' . DB_TN_TWEETS . '` (`' . DB_CN_TWEETS_TWEET_ID . '`, `' . DB_CN_TWEETS_TWEET_USER_ID . '`, `' . DB_CN_TWEETS_TEXT . '`, `' . DB_CN_TWEETS_GEO_LAT . '`, `' . DB_CN_TWEETS_GEO_LON . '`) VALUES ';
//        $sql = 'INSERT INTO `' . DB_TN_TWEETS . '` (`' . DB_CN_TWEETS_TWEET_ID . '`, `' . DB_CN_TWEETS_TWEET_USER_ID . '`, `' . DB_CN_TWEETS_TEXT . '`) VALUES ';
        $sql_values = array();
        foreach (range(1, count($statuses)) as $i) {
//            $sql_values[] = "(':TID$i', ':TUID$i', ':TEXT$i')";
            $sql_values[] = "(':TID{$i}E', ':TUID{$i}E', ':TEXT{$i}E', ':LAT{$i}E', ':LON{$i}E')";
        }
        $sql .= implode(',', $sql_values);

        $pre_sql = $sql;

        $stmt = $this->prepare($sql);
        foreach ($statuses as $i => $st) {
            $i++;
            $stmt->bindValue(":TID{$i}E", $st->id);
            $stmt->bindValue(":TUID{$i}E", $st->user->id);
            $stmt->bindValue(":TEXT{$i}E", $st->text);
            $stmt->bindValue(":LAT{$i}E", $st->geo->coordinates[0]);
            $stmt->bindValue(":LON{$i}E", $st->geo->coordinates[1]);
            $pre_sql = str_replace(array(":TID{$i}E", ":TUID{$i}E", ":TEXT{$i}E", ":LAT{$i}E", ":LON{$i}E"), array($st->id, $st->user->id, $st->text, $st->geo->coordinates[0], $st->geo->coordinates[1]), $pre_sql);
        }
        echo $sql . PHP_EOL;
        echo $pre_sql;
        return $stmt->execute();
    }
}
