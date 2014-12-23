<?php

class Rule {
    public $label;
    public $lat;
    public $lon;
    public $radius;
    public $date_timestamp;

    public function __construct($obj = NULL) {
        if (!isset($obj)) {
            return;
        }
        $this->label          = $obj[DB_CN_RULES_LABEL];
        $this->lat            = $obj[DB_CN_RULES_LAT];
        $this->lon            = $obj[DB_CN_RULES_LON];
        $this->radius         = $obj[DB_CN_RULES_RADIUS_KM];
        $this->date_timestamp = strtotime($obj[DB_CN_RULES_DATE]);
    }

    public function set($label, $lat, $lon, $radius, $date_text) {
        $this->label = $label;
        $this->lat = $lat;
        $this->lon = $lon;
        $this->radius = $radius;
        $this->date_timestamp = strtotime($date_text);
    }

    public function getGeocode() {
        return "{$this->lat},{$this->lon},{$this->radius}km";
    }

    public function getDateMysql() {
        return date(FORMAT_DATE, $this->date_timestamp);
    }

    public function getDateStr() {
        return date(FORMAT_DATE_STR, $this->date_timestamp);
    }
}
