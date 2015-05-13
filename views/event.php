<?php
/* @var $datas */

echo 'view success!';
var_dump($datas);
exit();

//list($tweets, $tweets_4s) = get_tweets($lat, $long);


$maptype = "ROADMAP";
$data = array();
$p = 80000;
foreach($tweets as $tw) {
    $d = new stdclass();
    $p = @$tw["point"];
    if (!$p) {
        $p = -0.5;
    }
    $d->point = normalize_point($p);
    $d->pos = array($tw["lon"], $tw["lat"]);
    $data[] = $d;
}
/** negapozi を 正規化 */
function normalize_point($p) {
    return $p * -50000 + 100000;
}
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

var map;
var center = {lat: 35.6521438, lng: 139.7021483};
  function initialize() {
    var mapOptions = {
      zoom: 10,
      center: center,
      mapTypeId: google.maps.MapTypeId.TERRAIN
    };

    map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);

    var bounds = new google.maps.LatLngBounds();
    var  pos, point = [];
    for (var i=0; i < data.length; i++) {
        pos = new google.maps.LatLng(data[i].pos[1], data[i].pos[0]);
        point.push({
            location : pos,
            weight : data[i].point //ヒートマップの重み付けに使用するデータを指定
        })
        bounds.extend(pos);
    }
//    map.fitBounds(bounds); //全てのデータが画面に収まる様に表示を変更
 
    //ヒートマップレイヤの作成

    var heatmap = new google.maps.visualization.HeatmapLayer({
            fillOpacity: 1.1,
            radius:25,
            gradient: ['white', '#f50', '#f00', 'black', 'black']
    });
//    var heatmap = new google.maps.visualization.HeatmapLayer({
//            fillOpacity: 1.1,
//            radius: 50,
//            gradient: ['#fff', '#f00', '#f80', '#8f0', '#0f0', '#0f8', '#08f', '#00f', '#80f', '#f08', 'black']
//    });
//    var heatmap = new google.maps.visualization.HeatmapLayer({
//            fillOpacity: 1.1,
//            radius: 50,
//            gradient: ['white', '#00f', '#0ff']
//    });
    heatmap.setData(point);
    heatmap.setMap(map);
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
