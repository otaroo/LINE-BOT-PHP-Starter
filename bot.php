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
            if(!($event['message']['text'] === NULL) && strpos($event['message']['text'], 'Jarvis') !== false){
                $text =explode( ' ', $event['message']['text']);
                if( $text[0] == "จาวิส" && $text[1] === "อากาศ"){
                    
                    $location = GetLocation($text[2]);
                    $messages = GetWeather($location,$text[2]);
                    $data = [
                    'replyToken' => $replyToken,
                    'messages' => [$messages],
                    ];
                }elseif($text[0] == "จาวิส" && (strpos($text[0], 'เพลง') !== false ||strpos($text[0], 'คลิป') !== false )){
                    if(!($text[2] === null)){
                        $s_youtubr = "";
                        if(count($text)>3){
                            for ($x = 2; $x <= count($text)-1; $x++) {
                                if($x !=count($text)-1){
                                    $s_youtubr =$s_youtubr.$text[$x]."+";
                                }else{
                                    $s_youtubr =$s_youtubr.$text[$x];
                                }
                            }
                        }else{
                            $s_youtubr = $text[2];
                        }
                        $messages = GetYoutube($s_youtubr);
                        $data = [
                        'replyToken' => $replyToken,
                        'messages' => [$messages],
                        ];
                    }else{
                        $messages = [
                        'type' => 'text',
                        'text' => "เพลงอะไรล่ะ?"
                        ];
                        $data = [
                        'replyToken' => $replyToken,
                        'messages' => [$messages],
                        ];
                    }
                }elseif($text[0] == "จาวิส" && strpos($text[1], 'เบอร์') !== false){
                    if(!($text[2] === null)){
                        $messages = getDataUser($text[2]);
                        $data = [
                        'replyToken' => $replyToken,
                        'messages' => [$messages],
                        ];
                    }
                }elseif($text[0] == "จาวิส" && !($UID===null) ){ //&& $UID=="Uf96e29269201978e3c4cdc4bff843be0"
                    if($text[1] == "เปิดไฟ"){
                        $messages = setLamp("ON");
                    }else if($text[1] == "ปิดไฟ"){
                        $messages = setLamp("OFF");
                    }
                    $data = [
                    'replyToken' => $replyToken,
                    'messages' => [$messages],
                    ];
                    
                }elseif(strpos($text[0], 'จาวิส') !== false && ( !($text[1] === NULL) || $text[1] === NULL)){
                    $a=array("ว่ามา","สบายดีไหม","ครับผม","พร้อมบริการ","หิว", "Hello!!", "How are you?", "I'm Bot", "ช่วงนี้กำลังยุ่ง", "ขอเวลาพักผ่อนนิดนึง", "ว่างหรอ", "ไม่ใช่เพื่อนเล่น", "ซักวันจะเป็นมนุษย์", "อย่าเกรียน", "มึงเก๋าหรอ!!");
                    $messages = [
                    'type' => 'text',
                    'text' => $a[array_rand($a)]
                    ];
                    $data = [
                    'replyToken' => $replyToken,
                    'messages' => [$messages],
                    ];
                    saveData($text[1]);
                }
                PushMessage($data);
                
        }elseif(strpos($event['message']['text'], 'บ้าน') !== false){
            $text = $event['message']['text'];
            $search =  iconv_substr($text,4);
            $messages = getLocationUser($search);
            $data = [
            'replyToken' => $replyToken,
            'messages' => [$messages],
            ];
            PushMessage($data);
            
        }elseif(strpos($event['message']['text'], 'เบอร์') !== false){
            $text = $event['message']['text'];
            $search =  iconv_substr($text,5);
            $messages = getDataUser($search);
            $data = [
            'replyToken' => $replyToken,
            'messages' => [$messages],
            ];
            PushMessage($data);
        }
        
    }elseif($event['type'] == 'message' && $event['message']['type'] == 'location'){
        $location =  $event['message']['latitude'].",".$event['message']['longitude'];
        $messages = GetWeather($location,"");
        $replyToken = $event['replyToken'];
        $data = [
        'replyToken' => $replyToken,
        'messages' => [$messages],
        ];
        PushMessage($data);
    }elseif($event['type'] == 'message' && $event['message']['type'] == 'image'){
        $imageid =  $event['message']['id'];
        GetContent($imageid);
        $replyToken = $event['replyToken'];
        $messages = [
            'type' => 'text',
            'text' => $imageid
            ];
            $data = [
            'replyToken' => $replyToken,
            'messages' => [$messages],
            ];
            PushMessage($data);
    }
}
}
echo "OK";

function GetContent($messageId){
    $Id = urlencode($messageId);
    $url_dataGo = 'https://api.line.me/v2/bot/message/'.$Id.'/content';
    $output_filename = $Id;
    $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
        $host = "https://api.line.me/v2/bot/message/'.$Id.'/content";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $host);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, false);
        curl_setopt($ch, CURLOPT_REFERER, "https://api.line.me");
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        curl_close($ch);
    
        print_r($result); // prints the contents of the collected file before writing..
    
    
        // the following lines write the contents to a file in the same directory (provided permissions etc)
        $fp = fopen($output_filename, 'w');
        fwrite($fp, $result);
        fclose($fp);
}
function GetLocation($province) {
    $s_youtubr = urlencode($province);
    $url_dataGo = 'http://demo-api.data.go.th/searching/api/dataset/query?dsname=tambon&path=TAMBON&property=CHANGWAT_T&operator=CONTAINS&value='.$s_youtubr.'&property=AMPHOE_T&operator=CONTAINS&value='.$s_youtubr.'&limit=100&offset=0';
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

function GetYoutube($s_youtubr_query) {
    
    $url_Yt = 'https://www.googleapis.com/youtube/v3/search?part=snippet&key=AIzaSyBjQJjyNUFfev4rznR_TMef0i0bl4TmyCw&q='.$s_youtubr_query;
    $ch_Yt = curl_init($url_Yt);
    curl_setopt($ch_Yt, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch_Yt, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_Yt, CURLOPT_RETURNTRANSFER, 1);
    $result_Yt = curl_exec($ch_Yt);
    $youtube_data = json_decode($result_Yt, true);
    $url = $youtube_data["items"][0]["id"]["videoId"];
    $title = $youtube_data["items"][0]["snippet"]["title"];
    $image_m= $youtube_data["items"][0]["snippet"]["thumbnails"]["medium"]["url"];
    $image_h= $youtube_data["items"][0]["snippet"]["thumbnails"]["high"]["url"];
    /*$messages = [
    'type' => 'template',
    'altText' => 'template',
    'template' => [
    'type' => 'buttons',
    'thumbnailImageUrl' => $image_h,
    'title' => ' ',
    "text" => $title,
    "actions" => [
    [
    "type" => "uri",
    "label" => "ดูบน Youtube",
    "uri" => "https://www.youtube.com/watch?v=".$url
    ]
    ]
    ]
    ];*/
    $columns=[];
    for($index = 0 ;$index<=2;$index++){
        $title=" ";
        if(strlen($youtube_data["items"][$index]["snippet"]["thumbnails"]["high"]["url"])>60){
            $title=$youtube_data["items"][$index]["snippet"]["thumbnails"]["high"]["url"];
        }
        $columns[$index] = [
        'thumbnailImageUrl' => $youtube_data["items"][$index]["snippet"]["thumbnails"]["high"]["url"],
        'title' => ' ',
        "text" => $title,
        "actions" => [
        [
        "type" => "uri",
        "label" => "ดูบน Youtube",
        "uri" => "https://www.youtube.com/watch?v=".$youtube_data["items"][$index]["id"]["videoId"]
        ]
        ]
        ];
    }
    
    $messages = [
    'type' => 'template',
    'altText' => 'template',
    'template' => [
    "type" => "carousel",
    'columns' => $columns
    
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
    if(!$result){
        $Log = json_decode($result, true);
        $message_log = LogMessage($Log["message"]);
        LogPush($message_log);
    }else{
        $Log = json_decode($result, true);
        $message_log = LogMessage($Log["message"]);
        LogPush($message_log);
    }
    curl_close($ch);
}

function LogMessage($Log){
    if(!($Log === null)){
        $messages = [
        'type' => 'text',
        'text' => $Log
        ];
        return  $messages;
    }
}


function LogPush($Log){
    $data = [
    'to' => "Uf96e29269201978e3c4cdc4bff843be0",
    'messages' => [$Log],
    ];
    $access_token = 'Yfp4E1/cS+OUoQOVVHc2/uLctihQ5gHv9o5rPRMLp0drPl0ObyZwI8uYQjm/VozeGloTmKsOnpdNdwmUrJTw91JQX3LJG3bVSpRFe/q++N0p0ZuTsLoksNRK6TBkmR4+KIgNplG7sib3btmH6nYuowdB04t89/1O/w1cDnyilFU=';
    $url = 'https://api.line.me/v2/bot/message/push';
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

function saveData($text){
    $url = 'https://Jarvis-e3312.firebaseio.com/data/message.json';
    $post = json_encode($text);
    $headers = array('Content-Type: application/json');
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    if(!$result){
        $result =   curl_error($ch).curl_errno($ch);
        $Log = json_decode($result, true);
        $message_log = LogMessage($Log["message"]);
        LogPush($message_log);
    }
    $data = json_decode($result, true);
    curl_close($ch);
    
}

function getDataUser($user){
    $url = 'https://Jarvis-e3312.firebaseio.com/data/user.json';
    $ch_Yt = curl_init($url);
    curl_setopt($ch_Yt, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch_Yt, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_Yt, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch_Yt);
    $data = json_decode($result, true);
    $phone = $data[$user]["number"][0];
    $messages = [
    'type' => 'text',
    'text' => $phone
    ];
    return  $messages;
}
function getLocationUser($user){
    $url = 'https://Jarvis-e3312.firebaseio.com/data/user.json';
    $ch_Yt = curl_init($url);
    curl_setopt($ch_Yt, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch_Yt, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_Yt, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch_Yt);
    $data = json_decode($result, true);
    $latitude = $data[$user]["location"]["latitude"];
    $longitude = $data[$user]["location"]["longitude"];
    $address = $data[$user]["location"]["address"];
    $messages = [
    'type' => 'location',
    'title' => $user,
    'address' => $address,
    'latitude' =>  $latitude,
    'longitude' => $longitude,
    ];
    return  $messages;
}
function setLamp($data){
    $username = "n5nsV5bzcxaGuCV";
    $password = "435J4qZahKuPAQhzD3tpHNpWR";
    $payloadName = $data;
    $url = 'https://api.netpie.io/microgear/Jarvis/nodemcu';
    $ch_netpie = curl_init($url);
    curl_setopt($ch_netpie, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch_netpie , CURLOPT_USERPWD, $username . ":" . $password);
    curl_setopt($ch_netpie, CURLOPT_POSTFIELDS, $payloadName);
    curl_setopt($ch_netpie , CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch_netpie, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch_netpie, CURLOPT_POSTFIELDS, $payloadName);
    curl_setopt($ch_netpie, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch_netpie);
    $netpie_m ="";
    if(curl_exec($ch_netpie) === false)
    {
        $netpie_m = curl_error($ch_netpie);
    }else{
        $data = json_decode($result, true);
        if($data["message"]=="Success"){
            $netpie_m = "เรียบร้อย";
        }
        
    }
    
    $messages = [
    'type' => 'text',
    'text' => $netpie_m
    ];
    return $messages;
}