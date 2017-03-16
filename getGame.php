<? 
$url = "http://rssfeeds.sanook.com/rss/feeds/sanook/game.news.xml";
$xml = simplexml_load_file($url);
$json = json_encode($xml);
$array = json_decode($json,TRUE);

echo $json;


