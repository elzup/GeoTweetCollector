<?php

$a = ['hoge' => 2, 'fuga' => 3, 'piyo' => 4];
$b = ['hoge' => 20, 'fuga' => 30];
$c = array_merge($b, $a);
var_dump($c);
