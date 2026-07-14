<?php
$sms_send_url="https://sms.mram.com.bd/smsapi";
$apikey="C3002401697093328d8759.51998408";
$sender_id=urlencode("8809601016554");


// SMS Send Start
$number="8801720981682";
$message="একটি রিকুইজিশন আপনার অনুমোদনের জন্য অপেক্ষা করছে। Link :hi how are you";

echo $message_url=$sms_send_url."?api_key=".$apikey."&type=unicode&contacts=".urlencode($number)."&senderid=".$sender_id."&msg=".urlencode($message);
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $message_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
    echo $result = curl_exec ($curl);
    curl_close ($curl);
    
/*    
$SMS_number="8801712193135";
$SMS_message="Material requisition request for the project of polash test";

$SMS_url=$sms_send_url."?api_key=".$apikey."&type=unicode&contacts=".urlencode($SMS_number)."&senderid=".$sender_id."&msg=".urlencode($SMS_message);
    $curl_Send = curl_init();
    curl_setopt ($curl_Send, CURLOPT_URL, $SMS_url);
    curl_setopt($curl_Send, CURLOPT_RETURNTRANSFER, true);
	
    $result_Send = curl_exec ($curl_Send);
    curl_close ($curl_Send); 
    */
// SMS Send End
?>