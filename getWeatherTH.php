<?php

    
$url = "http://data.tmd.go.th/api/WeatherForecastDaily/V1/";
$xml = simplexml_load_file($url);
$json = json_encode($xml);
echo $json;


