<?php

//This script listens for a webhook post and writes it to a logfile named webhook.log

date_default_timezone_set('America/Chicago');

$rawData = $_POST['payload'];
$data    = json_decode($rawData, true);
$pretty  = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

/* $data = $_POST['payload'];           
$unescaped_data = stripslashes($data);
$obj = json_decode($unescaped_data, true); */

$date         = new DateTime();
$headers      = apache_request_headers();
$destination  = 'webhook.log';

error_log($date->format('F j Y, g:i:s a T') . "\n\n", 3, $destination);
error_log('HEADERS:' . "\n", 3, $destination);
foreach ($headers as $header => $value) {
	error_log("$header: $value" . "\n", 3, $destination);
}
error_log("\n", 3, $destination);
error_log('Raw:' . "\n" . $data . "\n\n", 3, $destination);
error_log('Pretty:' . "\n", 3, $destination);
error_log($pretty . "\n\n", 3, $destination);
error_log('======================================================' . "\n\n\n", 3, $destination);

?>
<html>
Thanks for your payment
</html>