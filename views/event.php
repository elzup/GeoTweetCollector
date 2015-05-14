<?php
/* @var $datas */

$maptype = "ROADMAP";
$data = array();
$p = 80000;
$idlib = array();
$idcollib = array();
foreach($datas as $cid => $culster) {
    $idlib[$cid] = $culster->tag;
    echo $cid;
    echo '<br />';
    $col = getColorCode($cid, count($datas));
    echo '<br />';
    echo $col;
    foreach ($culster->top_cluster as $p) {
        $d = new stdclass();
        $d->id = $cid;
        $d->pos = array($p->lat, $p->lng);
        $d->text = $p->text;
        $d->timestamp = $p->timestamp;
        $d->color = $col;
        $data[] = $d;
    }
}
/** negapozi を 正規化 */
$date_str = date('Y年m月d日 H時m分');

function getColorCode($id, $max) {
    return implode('', array_map(function ($v) {
        return sprintf("%02s", dechex($v));
    }, HSVtoRGB($id * 360 / $max, 100, 100)));
}

function HSVtoRGB($iH, $iS, $iV) {
    if($iH < 0)   $iH = 0;   // Hue:
    if($iH > 360) $iH = 360; //   0-360
    if($iS < 0)   $iS = 0;   // Saturation:
    if($iS > 100) $iS = 100; //   0-100
    if($iV < 0)   $iV = 0;   // Lightness:
    if($iV > 100) $iV = 100; //   0-100
    $dS = $iS/100.0; // Saturation: 0.0-1.0
    $dV = $iV/100.0; // Lightness:  0.0-1.0
    $dC = $dV*$dS;   // Chroma:     0.0-1.0
    $dH = $iH/60.0;  // H-Prime:    0.0-6.0
    $dT = $dH;       // Temp variable
    while($dT >= 2.0) $dT -= 2.0; // php modulus does not work with float
    $dX = $dC*(1-abs($dT-1));     // as used in the Wikipedia link
    switch($dH) {
    case($dH >= 0.0 && $dH < 1.0):
        $dR = $dC; $dG = $dX; $dB = 0.0; break;
    case($dH >= 1.0 && $dH < 2.0):
        $dR = $dX; $dG = $dC; $dB = 0.0; break;
    case($dH >= 2.0 && $dH < 3.0):
        $dR = 0.0; $dG = $dC; $dB = $dX; break;
    case($dH >= 3.0 && $dH < 4.0):
        $dR = 0.0; $dG = $dX; $dB = $dC; break;
    case($dH >= 4.0 && $dH < 5.0):
        $dR = $dX; $dG = 0.0; $dB = $dC; break;
    case($dH >= 5.0 && $dH < 6.0):
        $dR = $dC; $dG = 0.0; $dB = $dX; break;
    default:
        $dR = 0.0; $dG = 0.0; $dB = 0.0; break;
    }
    $dM  = $dV - $dC;
    $dR += $dM; $dG += $dM; $dB += $dM;
    $dR *= 255; $dG *= 255; $dB *= 255;
    return [round($dR), round($dG), round($dB)];
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <title>イベント検知システム</title>
<link rel="stylesheet" href="style/main.css">
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_API_KEY ?>&sensor=TRUE&libraries=geometry,visualization"></script>
<script type="text/javascript">
var data = []; 
data = <?php echo json_encode($data) ?>;
console.log(data);

var map, infowindow;
var center = {lat: 35.6521438, lng: 139.7021483};
  function initialize() {
    var mapOptions = {
      zoom: 11,
      center: center,
      mapTypeId: google.maps.MapTypeId.TERRAIN
    };

    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
    infowindow = new google.maps.InfoWindow();
    var geocoder = new google.maps.Geocoder();

//    var bounds = new google.maps.LatLngBounds();
    var pos, point = [];
    for (var i=0; i < data.length; i++) {
        pos = new google.maps.LatLng(data[i].pos[1], data[i].pos[0]);
        point.push({
            location : pos,
            weight : data[i].point //ヒートマップの重み付けに使用するデータを指定
        })
//        bounds.extend(pos);

        set_marker(data[i].color, data[i].pos[0], data[i].pos[1], map, infowindow, data[i].timestamp, data[i].text + "[" + data[i].timestamp + "]");
 
    }
//    map.fitBounds(bounds); //全てのデータが画面に収まる様に表示を変更


    //ヒートマップレイヤの作成
    var collib = ['red', 'green', 'blue', 'yello', 'black', 'perple'];
//    var heatmap = new google.maps.visualization.HeatmapLayer({
//            fillOpacity: 1.1,
//            radius: 50,
//            gradient: ['#fff', '#f00', '#f80', '#8f0', '#0f0', '#0f8', '#08f', '#00f', '#80f', '#f08', 'black']
//    });
//    heatmap.setData(point);
//    heatmap.setMap(map);
}

function set_marker(col, lat, lon, map, infowindow, time, text) {
    var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + col,
        new google.maps.Size(21, 34), new google.maps.Point(0,0), new google.maps.Point(10, 34));
    marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lon),
        icon: pinImage,
        map: map
    });
    google.maps.event.addListener(marker, 'mouseover', (function(marker, user_lock, k, j, time) {
        return function() {
            infowindow.setContent(text);
            infowindow.open(map, marker);
        }
    })(marker, time));
}

function id_to_color(id, max) {
    console.log(id);
    console.log(max);
    h = 360 * id / max;
    console.log(h);
    rgb = HSVtoRGB(h, 50, 100);
    console.log(rgb);
    console.log(rgb["r"]);
//    return parseInt(rgb[0], 16) + parseInt(rgb[1], 16) + parseInt(rgb[2], 16);
    return ['ff0000', '00ff00', '0000ff', 'ffff00', 'ff00ff', '00ffff', 'ffffff', '888888', '000000'][id];
}

</script>
  </head>
  <body onload="initialize()">
    <header>
      <h1>東京イベント検知システム</h1>
      <div class="control">
      </div>
    </header>
    <div id="map_canvas" style="width:100%; height:80%"></div>
  </body>
</html>
