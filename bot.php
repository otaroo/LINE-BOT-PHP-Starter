<?php
$access_token = 'Yfp4E1/cS+OUoQOVVHc2/uLctihQ5gHv9o5rPRMLp0drPl0ObyZwI8uYQjm/VozeGloTmKsOnpdNdwmUrJTw91JQX3LJG3bVSpRFe/q++N0p0ZuTsLoksNRK6TBkmR4+KIgNplG7sib3btmH6nYuowdB04t89/1O/w1cDnyilFU=';

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
			
			// Get text sent
			$text = $event['message']['text'];
			$UID =  $event['source']['groupId'];
			// Get replyToken
			$replyToken = $event['replyToken'];

			// Build message to reply back
			
			if($text =="weather")
			{
					$url_Wea = 'https://api.darksky.net/forecast/0b57d9cda4b346d2937f726ce2b0a7ae/13.8027339,100.5528678?units=ca&exclude=hourly';
					$ch_Wea = curl_init($url_Wea);
					curl_setopt($ch_Wea, CURLOPT_CUSTOMREQUEST, "GET");
					curl_setopt($ch_Wea, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch_Wea, CURLOPT_RETURNTRANSFER, 1);
					$result_Wea = curl_exec($ch_Wea);
					$wea = json_decode($result_Wea, true);
					$summary = $wea["daily"]["data"][1]["summary"];
					$min = $wea["daily"]["data"][1]["temperatureMin"];
					$max = $wea["daily"]["data"][1]["temperatureMax"];
					$t = $wea["daily"]["data"][1]["time"];
					$time_d = date("Y-m-d",$t);

					$messages = [
						'type' => 'text',
						'text' => "กรุงเทพมหานคร"
					];
					$m_summary = [
						'type' => 'text',
						'text' => "สภาพอากาศ ".$summary
					];
					$m_min = [
						'type' => 'text',
						'text' => "อุณหภูมิต่ำสุด ".$min
					];
					$m_max  = [
						'type' => 'text',
						'text' => "อุณหภูมิสูงสุด ".$max
					];

			
		
			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages,$m_summary,$m_min,$m_max],
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
}
echo "OK";
	
			
		
			
