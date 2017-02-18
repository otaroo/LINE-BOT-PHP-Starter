<?php
$access_token = 'x';

// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
    // Loop through each event
    foreach ($events['events'] as $event) {
        // Reply only when message sent is in 'text' format
        if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
            $text = $event['message']['text'];
            $UID =  $event['source']['groupId'];
            $replyToken = $event['replyToken'];
            
            // get ละติจูด ลองติจูด
            
            
            date_default_timezone_set("Asia/Bangkok");
            $url_Wea = 'https://api.darksky.net/forecast/0b57d9cda4b346d2937f726ce2b0a7ae/13.8027339,100.5528678?units=ca&exclude=hourly';
            $ch_Wea = curl_init($url_Wea);
            curl_setopt($ch_Wea, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch_Wea, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch_Wea, CURLOPT_RETURNTRANSFER, 1);
            $result_Wea = curl_exec($ch_Wea);
            $wea = json_decode($result_Wea, true);
            $summary = $wea["daily"]["data"][0]["summary"];
            $min = $wea["daily"]["data"][0]["temperatureMin"];
            $max = $wea["daily"]["data"][0]["temperatureMax"];
            $t = $wea["daily"]["data"][0]["time"];
            $time_d = date("d/m/Y",$t);
            $t = $wea["daily"]["data"][0]["sunriseTime"];
            $sunrise = date("h:i",$t);
            $t = $wea["daily"]["data"][0]["sunsetTime"];
            $sunset = date("h:i",$t);
            $messages = [
            'type' => 'text',
            'text' => "กรุงเทพมหานคร ".$time_d
            ];
            
            // Make a POST Request to Messaging API to reply to sender
            $url = 'https://api.line.me/v2/bot/message/reply';
            $data = [
            'replyToken' => $replyToken,
            'messages' => [$messages,$m_summary,$Temperature,$sun],
            ];
            $post = json_encode($data);
            $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            
            
        }
    }
}
echo "OK";

$search = urlencode("สุโขทัย");
$url_dataGo = 'http://demo-api.data.go.th/searching/api/dataset/query?dsname=tambon&path=TAMBON&property=CHANGWAT_T&operator=CONTAINS&value='.$search.'&property=AMPHOE_T&operator=CONTAINS&value='.$search.'&limit=100&offset=0';
$ch_dataGo = curl_init($url_dataGo);
curl_setopt($ch_dataGo , CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch_dataGo , CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch_dataGo , CURLOPT_RETURNTRANSFER, 1);
$result_dataGo  = curl_exec($ch_dataGo );
$wea = json_decode($result_dataGo, true);
echo $wea[0]["ชื่อจังหวัด"];
echo $wea[0]["ค่าละติจูด"];
echo $wea[0]["ค่าลองจิจูด"];