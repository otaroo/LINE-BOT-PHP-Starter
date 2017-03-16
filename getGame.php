<? 
$url = "http://rssfeeds.sanook.com/rss/feeds/sanook/game.news.xml";
$xml = simplexml_load_file($url);
$json = json_encode($xml);
str_replace("<![CDATA","",$json);
$array = json_decode($json,TRUE);

echo $json;


