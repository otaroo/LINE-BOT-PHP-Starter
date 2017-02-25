<?php

    
$url = "https://www.movie2free.com/feed/";
$xml = simplexml_load_file($url);
$json = json_encode($xml);
$array = json_decode($json,TRUE);
$title = $array["channel"]["item"][0]["title"];
$guid = $array["channel"]["item"][0]["guid"];
echo $json;


