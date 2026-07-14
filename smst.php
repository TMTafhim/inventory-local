<?php
$sms_send_url="https://880sms.com/smsapi";
$apikey="C200976967610d20399c04.83457952";
$sender_id=urlencode("Sigma Royal");
 
/*$message_url=$sms_send_url."?api_key=".$apikey."&type=text&contacts=".urlencode($number)."&senderid=".$sender_id."&msg=".urlencode($message);
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $message_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
    $result = curl_exec ($curl);
    curl_close ($curl);*/
 $number="8801720981682"; 
  $message="Hi Polash how are you";
echo $message_url=$sms_send_url."?api_key=".$apikey."&type=text&contacts=".urlencode($number)."&senderid=".$sender_id."&msg=".urlencode($message);
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $message_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
    echo $result = curl_exec ($curl);
    curl_close ($curl);    
?>