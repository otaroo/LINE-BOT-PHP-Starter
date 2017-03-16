<? 
$url = "http://rssfeeds.sanook.com/rss/feeds/sanook/game.news.xml";
$xml = simplexml_load_file($url);
$json = json_encode($xml);
$json =str_replace("<![CDATA","",$json);
$json =str_replace("]]>","",$json);
$array = json_decode($json,TRUE);

echo $json;


