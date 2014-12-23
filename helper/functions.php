<?php

function h($string) {
    return htmlspecialchars($string);
}

function get_googlemap_url($lat, $lon) {
    return str_replace(array('%LAT%', '%LON%'), array($lat, $lon), URL_GOOGLEMAP);
}
