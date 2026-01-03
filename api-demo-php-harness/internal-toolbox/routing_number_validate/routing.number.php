<?php

/*$file = fopen("trn.txt","r");
while(! feof($file))
{	
$routingNumber = str_replace(array('.', ' ', "\n", "\t", "\r"), '', fgets($file));*/

$routingNumber = $_POST['routing_number'];
$service_url = 'https://www.routingnumbers.info/api/data.json?rn='.$routingNumber;
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_NOBODY, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_FAILONERROR, false);
curl_setopt($curl, CURLOPT_POST, true);
$curl_response = curl_exec($curl);
$info = curl_getinfo($curl);
print_r('HttpStatus Code: ' . $info['http_code'] . '<br>');
if($curl_response === false)
{
	print_r('Curl error: ' . curl_error($curl) . '<br>');
}
curl_close($curl);

$data = json_decode($curl_response);
echo '<pre>';
print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
print_r($data);
echo '</pre>';

/*
# search.php
session_start();
$_SESSION['searchresult'] = $data;
header('Location: toolbox/routing_number_validate.php'); */






/*$data = json_decode($curl_response,true);
$message= $data["message"];
if($message == "OK"){
	echo "Valid Routing Number";
}
else{
	echo "invalid routing number";
}*/
	
	

	
?>