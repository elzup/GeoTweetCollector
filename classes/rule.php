<?php

class Rule {
    public $lat;
    public $lng;
    public $radius;
    public $date_text;

    public function __construct($obj = NULL) {
    }

    public function set($lat, $lng, $radius, $date_text) {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->radius = $radius;
        $this->date_text = $date_text;
    }

    public function getGeocode() {
        return "{$this->lat},{$this->lng},{$this->radius}km";
    }
}
