<?php
/* @var $datas */

echo '<pre>';
$maptype = "ROADMAP";
$data = array();
$p = 80000;
$idlib = array();
foreach($datas as $cid => $culster) {
    $idlib[$cid] = $culster->tag;
    foreach ($culster->top_cluster as $p) {
        $d = new stdclass();
        $d->id = $cid;
        $d->pos = array($p->lat, $p->lng);
        $d->text = $p->text;
        $d->timestamp = $p->timestamp;
        $data[] = $d;
    }
}
var_dump($idlib);
/** negapozi を 正規化 */
$date_str = date('Y年m月d日 H時m分');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
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
      zoom: 10,
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

        var col = id_to_color(data[i].id);
        set_marker(col, data[i].lat, data[i].lng, map, infowindow, data[i].timestamp, data[i].text + "[" + data[i].timestamp + "]");
 
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

function id_to_color(id) {
    return '#ffffff';
//    return ['#ff0000', '#00ff00', '#0000ff', 'ffff00', 'ff00ff', '00ffff', 'ffffff', '888888', '000000'][id];
}


</script>
  </head>
  <body onload="initialize()">
<header>
    <h1>エリアストレス in 東京都</h1>
    <div class="control">
        <div>
        <p><?= $date_str ?> 現在の東京都エリアストレス</p>
        </div>
        <div>
        </div>
    </div>
</header>
    <div id="map_canvas" style="width:100%; height:100%"></div>
  </body>
</html>
