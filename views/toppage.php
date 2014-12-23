<?php
/** @var $title string */
/** @var $rules Rule[] */

$title = 'GEO Tweet Collector';

$dates = array();
foreach (range(0, 7) as $i) {
    $dates[date('Y/m/d', strtotime("-{$i} day"))] = date('m月d日', strtotime("-{$i} day"));
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="./bower_components/pure/pure.css">
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-min.css">
    <link rel="stylesheet" href="./style/style.css">
<!--[if lte IE 8]>
    <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.5.0/grids-responsive-old-ie-min.css">
<![endif]-->
<!--[if gt IE 8]><!-->
<!--<![endif]-->
</head>
<body>
<section id="wrapper">
    <h1>GEO Tweet Collector</h1>
    <div class="pure-g">
        <div class="pure-u-1-2">
        <form class="pure-form pure-form-stacked" method="POST" action="<?= URL_ROOT . 'job/submit' ?>">
            <fieldset>
                <legend>ツイート収集フォーム</legend>

                ラベル
                <label for="label"></label>
                <input id="label" name="label" type="label" placeholder="label">

                <label for="date">収集対象日</label>
                <select id="date" name="date">
                <?php foreach ($dates as $date_num => $date) { ?>
                <option value="<?= $date_num ?>"><?= $date ?></option>
                <?php } ?>
                </select>

                <label for="lat">緯度(lat)</label>
                <input id="lat" name="lat" type="text" placeholder="lat">
                <label for="lon">軽度(long)</label>
                <input id="lon" name="lon" type="text" placeholder="lon">
                <label for="rad">半径</label>
                <input id="rad" name="rad" type="text" placeholder="rad">

                <button type="submit" class="pure-button button-large pure-button-primary">集める</button>
            </fieldset>
        </form>
        </div>
        <div class="pure-u-1-2">
        <table>
            <tr>
                <th>ラベル</th>
                <th>Geocode</th>
                <th>日付</th>
            </tr>
<?php foreach($rules as $rule) { ?>
            <tr>
                <td><?= h($rule->label) ?></td>
                <td><a href="<?= get_googlemap_url($rule->lat, $rule->lon) ?>" target="_blank"><?= $rule->getGeocode() ?></a></td>
                <td><?= $rule->getDateStr() ?></td>
            </tr>
<?php } ?>
</table>
        </div>
    </div>
</section>
</body>
</html>
