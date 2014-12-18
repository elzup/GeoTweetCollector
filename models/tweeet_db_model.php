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

    public function load_rule($id) {
        $res = $this->select_rule($id);
        return new Rule($res);
    }

    public function select_rule($id) {
        $sql = 'SELECT * FROM `' . DB_TN_RULES . '` WHERE `' . DB_CN_RULES_ID . '` = :ID';
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":ID", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function get_old_id($id) {
        $sql = 'SELECT `' . DB_CN_TWEETS_ID . '` FROM `' . DB_TN_TWEETS . '` WHERE `' . DB_CN_TWEETS_RULES_ID . '` = :ID ORDER BY `' . DB_CN_TWEETS_ID . '` LIMIT 1';
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":ID", $id);
        $stmt->execute();
        return $stmt->fetch() ?: -1;
    }

    public function load_rules() {
        $rule_list = array();
        foreach ($this->select_rules() as $row) {
            $rule_list[] = new Rule($row);
        }
        return $rule_list;
    }

    public function select_rules() {
        $sql = 'SELECT * FROM `' . DB_TN_RULES . '`';
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":ID", $id);
        $stmt->execute();
        return $stmt->fetch_all();
    }


    public function insert_tweets($statuses, $rule_id) {
        $sql = 'INSERT INTO `' . DB_TN_TWEETS . '` (`' . DB_CN_TWEETS_TWEET_ID . '`, `' . DB_CN_TWEETS_TWEET_USER_ID . '`, `' . DB_CN_TWEETS_TEXT . '`, `' . DB_CN_TWEETS_GEO_LAT . '`, `' . DB_CN_TWEETS_GEO_LON . '`, `' . DB_CN_TWEETS_RULES_ID .'`) VALUES ';
        $sql_values = array();
        foreach (range(1, count($statuses)) as $i) {
//            $sql_values[] = "(':TID$i', ':TUID$i', ':TEXT$i')";
            $sql_values[] = "(':TID{$i}E', ':TUID{$i}E', ':TEXT{$i}E', ':LAT{$i}E', ':LON{$i}E', :RID{$i}E)";
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
            $stmt->bindValue(":RID{$i}E", $rule_id);
            $pre_sql = str_replace(array(":TID{$i}E", ":TUID{$i}E", ":TEXT{$i}E", ":LAT{$i}E", ":LON{$i}E"), array($st->id, $st->user->id, $st->text, $st->geo->coordinates[0], $st->geo->coordinates[1]), $pre_sql);
        }
//        echo $sql . PHP_EOL;
//        echo $pre_sql;
        return $stmt->execute();
    }

    public function insert_rule(Rule $rule) {
        $sql = 'INSERT INTO `' . DB_TN_RULES . '` (`' . DB_CN_RULES_LABEL . '`, `' . DB_CN_RULES_DATE . '`, `' . DB_CN_RULES_LAT . '`, `' . DB_CN_RULES_LON . '`, `' . DB_CN_RULES_RADIUS_KM . '`) VALUES (:LABEL, :DATE, :LAT, :LON, :RAD)';
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':LABEL', $rule->label);
        $stmt->bindValue(':DATE', $rule->getDateMysql());
        $stmt->bindValue(':LAT', $rule->lat);
        $stmt->bindValue(':LON', $rule->lon);
        $stmt->bindValue(':RAD', $rule->radius);
        return $stmt->execute();
    }

    public function update_rule_disactive($id) {
        $sql = 'UPDATE `' . DB_TN_RULES . '` SET `' . DB_CN_RULES_IS_ACTIVE . '` = 0';
        return $this->query($sql);
    }

}
