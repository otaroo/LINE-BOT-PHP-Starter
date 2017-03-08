<? 
$url = "https://www.blognone.com/atom.xml";
$xml = simplexml_load_file($url);
$json = json_encode($xml);
$array = json_decode($json,TRUE);

echo $json;


