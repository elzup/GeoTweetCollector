<?php

class TweetDBModel extends PDO {
    public function __construct() {
        try {
        $this->engine = DB_ENGINE;
        $this->host = DB_HOST;
        $this->database = DB_NAME;
        $this->user = DB_USER;
        $this->pass = DB_PASSWORD;
        $dns = $this->engine . ':dbname=' . $this->database . ";host=" . $this->host;
        parent::__construct($dns, $this->user, $this->pass);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }

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
        $sql = 'SELECT *, X(' . DB_CN_RULES_LATLON. ') as lat, Y(' . DB_CN_RULES_LATLON. ') as lon FROM `' . DB_TN_RULES . '` WHERE `' . DB_CN_RULES_ID . '` = :ID';
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":ID", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function get_old_id($id) {
        $sql = 'SELECT `' . DB_CN_TWEETS_TWEET_ID . '` FROM `' . DB_TN_TWEETS . '` WHERE `' . DB_CN_TWEETS_RULES_ID . '` = :ID ORDER BY `' . DB_CN_TWEETS_TWEET_ID . '` LIMIT 1';
        $stmt = $this->prepare($sql);
        $stmt->bindValue(":ID", $id);
        $stmt->execute();
        return $stmt->fetch() ?: -1;
    }

    public function load_rules($limit = 10) {
        $rule_list = array();
        foreach ($this->select_rules($limit) as $row) {
            $rule_list[] = new Rule($row);
        }
        return $rule_list;
    }

    public function select_rules($limit = 10) {
        $sql = 'SELECT *, X(' . DB_CN_RULES_LATLON. ') as lat, Y(' . DB_CN_RULES_LATLON. ') as lon  FROM `' . DB_TN_RULES . '`';
        if (isset($limit)) {
            $sql .= ' ORDER BY `' . DB_CN_RULES_ID . '` DESC LIMIT ' . $limit;
        }
        $res = $this->query($sql);
        return $res;
    }

    public function insert_tweets($statuses, $rule_id) {
//        $sql = 'INSERT INTO `' . DB_TN_TWEETS . '` (`' . DB_CN_TWEETS_TWEET_ID . '`, `' . DB_CN_TWEETS_TWEET_USER_ID . '`, `' . DB_CN_TWEETS_TEXT . '`, `' . DB_CN_TWEETS_GEO_LAT . '`, `' . DB_CN_TWEETS_GEO_LON . '`, `' . DB_CN_TWEETS_RULES_ID .'`) VALUES ';
        $sql = 'INSERT INTO `' . DB_TN_TWEETS . '` (`' . DB_CN_TWEETS_TWEET_ID . '`, `' . DB_CN_TWEETS_TWEET_USER_ID . '`, `' . DB_CN_TWEETS_TEXT . '`, `' . DB_CN_TWEETS_LATLNG .'`, `' . DB_CN_TWEETS_RULES_ID. '`, `' . DB_CN_TWEETS_TIMESTAMP. "`) VALUES ";
        $sql_values = array();
        foreach ($statuses as $i => $st) {
            $i++;
//            $sql_values[] = "(':TID$i', ':TUID$i', ':TEXT$i')";
            $sql_values[] = "(:TID{$i}E, :TUID{$i}E, :TEXT{$i}E, GeomFromText('POINT(" . implode(' ', $st->geo->coordinates) . ")'), :RID{$i}E, :TIME{$i}E)";

        }
        $sql .= implode(',', $sql_values);

        $pre_sql = $sql;

        $stmt = $this->prepare($sql);
        foreach ($statuses as $i => $st) {
            $i++;
            $stmt->bindValue(":TID{$i}E", $st->id);
            $stmt->bindValue(":TUID{$i}E", $st->user->id);
            $stmt->bindValue(":TEXT{$i}E", $st->text);
            $stmt->bindValue(":RID{$i}E", $rule_id);
            $stmt->bindValue(":TIME{$i}E", date("Y-m-d H:i:s", strtotime($st->created_at) - 60 * 60 * 9));
        }
        if (!$stmt->execute()) {
            echo '<pre>';
            var_dump($stmt->errorInfo());
            exit;
        }
    }

    public function load_tweets_recet($limit = 200) {
        $sql = 'SELECT *, X(' . DB_CN_TWEETS_LATLNG . ') as lat, Y(' . DB_CN_TWEETS_LATLNG . ') as lon FROM `' . DB_TN_TWEETS . '`';
        if (isset($limit)) {
            $sql .= ' ORDER BY `' . DB_CN_TWEETS_ID . '` DESC LIMIT ' . $limit;
        }
        $res = $this->query($sql);
        return $res->fetchAll();
    }

    public function insert_rule(Rule $rule) {
//        $sql = 'INSERT IGNORE INTO `' . DB_TN_RULES . '` (`' . DB_CN_RULES_LABEL . '`, `' . DB_CN_RULES_DATE . '`, `' . DB_CN_RULES_LATLON . '`, `' . DB_CN_RULES_RADIUS_KM . '`, `' . DB_CN_RULES_IS_ACTIVE . "`) VALUES (:LABEL, :DATE, GeomFromText('POINT(" . $rule->lat . ' ' . $rule->lon . ")'), :RAD, :ISA)";
        $sql = 'INSERT INTO `' . DB_TN_RULES . '` (`' . DB_CN_RULES_LABEL . '`, `' . DB_CN_RULES_DATE . '`, `' . DB_CN_RULES_LATLON . '`, `' . DB_CN_RULES_RADIUS_KM . '`, `' . DB_CN_RULES_IS_ACTIVE . "`) VALUES (:LABEL, :DATE, GeomFromText('POINT(" . $rule->lat . ' ' . $rule->lon . ")'), :RAD, :ISA)";
        $stmt = $this->prepare($sql);
        $stmt->bindValue(':LABEL', $rule->label);
        $stmt->bindValue(':DATE', $rule->getDateMysql());
        $stmt->bindValue(':RAD', $rule->radius);
        $stmt->bindValue(':ISA', '1');
        $stmt->execute();
//        return $this->lastInsertId();
    }

    public function update_rule_disactive($id) {
        $sql = 'UPDATE `' . DB_TN_RULES . '` SET `' . DB_CN_RULES_IS_ACTIVE . '` = 0';
        return $this->query($sql);
    }

}
