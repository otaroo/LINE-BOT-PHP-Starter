<?php


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
            $UID =  $event['source']['userId'];
            $replyToken = $event['replyToken'];
            $messages=[];
            $data = [];
            
            if(!($event['message']['text'] === NULL)){
                $text =explode( ' ', $event['message']['text']);
                if( $text[0] == "Jarvis" && $text[1] === "อากาศ"){
                    
                    $location = GetLocation($text[2]);
                    $messages = GetWeather($location,$text[2]);
                    $data = [
                    'replyToken' => $replyToken,
                    'messages' => [$messages],
                    ];
                }elseif($text[0] == "Jarvis" && $text[1] === "หาเพลง"){
                    if(!($text[2] === null)){
                        $s_youtubr=$text[2];
                        if(!($text[3] === null)){
                            $s_youtubr=$text[2]."+".$text[3];
                        }
                        $messages = GetYoutube($s_youtubr);
                        $data = [
                        'replyToken' => $replyToken,
                        'messages' => [$messages],
                        ];
                    }else{
                        $messages = [
                        'type' => 'text',
                        'text' => "หาไม่เจอ"
                        ];
                        $data = [
                        'replyToken' => $replyToken,
                        'messages' => [$messages],
                        ];
                    }
                }elseif($text[0] == "Jarvis" && ($text[1] === "ทำอะไรได้บ้าง"||$text[1] === "ทำไรได้บ้าง")){
                    $messages_1 = [
                    'type' => 'text',
                    'text' => "รายงานสภาพอากาศ [Jarvis อากาศ 'ชื่อจังหวัด']"
                    ];
                    $messages_2 = [
                    'type' => 'text',
                    'text' => "หาเพลงใน Youtube [Jarvis หาเพลง 'ชื่อเพลง']"
                    ];
                    $data = [
                    'replyToken' => $replyToken,
                    'messages' => [$messages_1,$messages_2 ],
                    ];
                }elseif($text[0] == "Jarvis" && $text[1] === "บันทึก" && $UID === "Uf96e29269201978e3c4cdc4bff843be0" ){
                    $messages = [
                    'type' => 'text',
                    'text' => "บันทึกเรียบร้อยครับ เจ้านาย"
                    ];
                    $data = [
                    'replyToken' => $replyToken,
                    'messages' => [$messages],
                    ];   
                }elseif($text[0] == "Jarvis" && ( !($text[1] === NULL) || $text[1] === NULL)){
                    $a=array("ว่ามา","สบายดีไหม","ครับผม","พร้อมบริการ","หิว", "Hello!!", "How are you?", "I'm Bot", "ช่วงนี้กำลังยุ่ง", "ขอเวลาพักผ่อนนิดนึง", "ว่างหรอ", "ไม่ใช่เพื่อนเล่น", "ซักวันจะเป็นมนุษย์", "อย่าเกรียน", "มึงเก๋าหรอ!!","ธัมมชโย อยู่ที่ไหน?");
                    $messages = [
                    'type' => 'text',
                    'text' => $a[array_rand($a)]
                    ];
                    $data = [
                    'replyToken' => $replyToken,
                    'messages' => [$messages],
                    ];
                }
                PushMessage($data);
                
            }
            
            
            
            /*$summary = $wea["daily"]["data"][0]["summary"];
            $min = $wea["daily"]["data"][0]["temperatureMin"];
            $max = $wea["daily"]["data"][0]["temperatureMax"];
            $t = $wea["daily"]["data"][0]["time"];
            $time_d = date("d/m/Y",$t);
            $t = $wea["daily"]["data"][0]["sunriseTime"];
            $sunrise = date("h:i",$t);
            $t = $wea["daily"]["data"][0]["sunsetTime"];
            $sunset = date("h:i",$t);*/
        }
    }
}
echo "OK";

function GetLocation($province) {
    $search = urlencode($province);
    $url_dataGo = 'http://demo-api.data.go.th/searching/api/dataset/query?dsname=tambon&path=TAMBON&property=CHANGWAT_T&operator=CONTAINS&value='.$search.'&property=AMPHOE_T&operator=CONTAINS&value='.$search.'&limit=100&offset=0';
    $ch_dataGo = curl_init($url_dataGo);
    curl_setopt($ch_dataGo , CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch_dataGo , CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_dataGo , CURLOPT_RETURNTRANSFER, 1);
    $result_dataGo  = curl_exec($ch_dataGo );
    $wea = json_decode($result_dataGo, true);
    return $wea[0]["ค่าละติจูด"].",".$wea[0]["ค่าลองจิจูด"];
}


function GetWeather($location,$province) {
    date_default_timezone_set("Asia/Bangkok");
    $url_Wea = 'https://api.darksky.net/forecast/0b57d9cda4b346d2937f726ce2b0a7ae/'.$location.'?units=ca&exclude=hourly';
    $ch_Wea = curl_init($url_Wea);
    curl_setopt($ch_Wea, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch_Wea, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_Wea, CURLOPT_RETURNTRANSFER, 1);
    $result_Wea = curl_exec($ch_Wea);
    $result_W = json_decode($result_Wea, true);
    $currently = $result_W["currently"]["temperature"];
    if(!($currently === null)){
        $messages = [
        'type' => 'text',
        'text' => $province." ".$currently." องศา"
        ];
    }else{
        $a=array("หาไม่เจอ","ตอนนี้ยังไม่มี","ลองใหม่","แค่ชื่อจังหวัดเท่านั้น");
        $messages = [
        'type' => 'text',
        'text' => $a[array_rand($a)]
        ];
    }
    return $messages ;
}

function GetYoutube($search_query) {
    
    $url_Yt = 'https://www.googleapis.com/youtube/v3/search?part=snippet&key=AIzaSyBjQJjyNUFfev4rznR_TMef0i0bl4TmyCw&q='.$search_query;
    $ch_Yt = curl_init($url_Yt);
    curl_setopt($ch_Yt, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch_Yt, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_Yt, CURLOPT_RETURNTRANSFER, 1);
    $result_Yt = curl_exec($ch_Yt);
    $youtube_data = json_decode($result_Yt, true);
    $url = $youtube_data["items"][0]["id"]["videoId"];
    $title = $youtube_data["items"][0]["snippet"]["title"];
    if(strlen($title)>50){
        $title = substr($title ,0,45);
    }
    $image_m= $youtube_data["items"][0]["snippet"]["thumbnails"]["medium"]["url"];
    $image_h= $youtube_data["items"][0]["snippet"]["thumbnails"]["high"]["url"];
    $messages = [
    'type' => 'template',
    'altText' => 'template',
    'template' => [
    'type' => 'buttons',
    'thumbnailImageUrl' => $image_h,
    'title' => ' ',
    "text" => $title."...",
    "actions" => [
    [
    "type" => "uri",
    "label" => "ดูบน Youtube",
    "uri" => "https://www.youtube.com/watch?v=".$url
    ]
    ]
    ]
    ];
    
    
    return  $messages;
}

function PushMessage($data){
    $access_token = 'Yfp4E1/cS+OUoQOVVHc2/uLctihQ5gHv9o5rPRMLp0drPl0ObyZwI8uYQjm/VozeGloTmKsOnpdNdwmUrJTw91JQX3LJG3bVSpRFe/q++N0p0ZuTsLoksNRK6TBkmR4+KIgNplG7sib3btmH6nYuowdB04t89/1O/w1cDnyilFU=';
    $url = 'https://api.line.me/v2/bot/message/reply';
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