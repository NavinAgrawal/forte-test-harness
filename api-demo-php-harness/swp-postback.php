<html>
Hi Krista
<img src="patty01.png">
<?php

//This script listens for a SWP postback, captures it and writes it to a logfile named upwire.log
$rawString = file_get_contents("php://input");
parse_str($rawString, $data);
date_default_timezone_set('America/Chicago');


//write the postback (including headers) to a logfile named james.log
$date = new DateTime();
$headers = apache_request_headers();
$destination  = 'upwire.log';

error_log($date->format('F j Y, g:i:s a T') . "\n" . 'S W P   P O S T B A C K' . "\n" . 'Headers:' . "\n", 3, $destination);
foreach ($headers as $header => $value) {
	error_log("    $header: $value" . "\n", 3, $destination);
}
error_log('Raw:' . "\n    " . $rawString . "\n", 3, $destination);
error_log('Pretty:' . "\n", 3, $destination);
foreach ($data as $key => $value) {
	error_log("    $key=$value" . "\n", 3, $destination);
}
error_log("\n\n", 3, $destination);

?>
</html>