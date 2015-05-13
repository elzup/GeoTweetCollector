<?php

class ClusterModel {
    private $date;

    public static $path = './data/';
    public function __construct($date = '2015-05-10') {
        $this->date = $date;
    }

    public function get_values() {
        $filename = ClusterModel::$path . $this->date . '.json';
        $json = file_get_contents($filename);
        return json_decode($json);
    }
}
