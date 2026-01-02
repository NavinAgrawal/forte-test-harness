<?php

//This script captures the webhook, writes it to a logfile, and sends a notification email

date_default_timezone_set('America/Chicago');

$rawData      = file_get_contents("php://input");
$data         = json_decode($rawData, true);
$pretty       = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$date         = new DateTime();
$headerdate   = new DateTime();
$headers      = apache_request_headers();
$destination  = 'ABBA-webhook.log';

$to          = $data["customer"]["addresses"]["email"];
$subject     = "Thank You for submitting your Loan Repayment Information";
$firstname   = $data["customer"]["addresses"]["first_name"];
$lastname    = $data["customer"]["addresses"]["last_name"];
$name        = $firstname . ' ' . $lastname;
$phone       = $data["customer"]["addresses"]["phone"];
$email       = $data["customer"]["addresses"]["email"];
$source      = $data["source"];

error_log($date->format('F j Y, g:i:s a T') . "\n\n", 3, $destination);
error_log('HEADERS:' . "\n", 3, $destination);
foreach ($headers as $header => $value) {
	error_log("$header: $value" . "\n", 3, $destination);
}
error_log("\n", 3, $destination);
error_log('Raw:' . "\n" . $rawData . "\n\n", 3, $destination);
error_log('Pretty:' . "\n", 3, $destination);
error_log($pretty . "\n\n", 3, $destination);

$message  = '<img src="https://www.jamesivey.com/forte/ABBA/ABBA_logo01.png"><br><br>';
$message .= $date->format('F j, Y') . '<br>';
$message .= $date->format('g:i a T') . '<br><br>';
$message .= 'Name: ' . $name . '<br>';
$message .= 'Phone Number: ' . $phone . '<br>';
$message .= 'Email Address: ' . $email . '<br>';
$message .= 'Your loan repayment information has been received. <br><br>';
$message .= 'Thank you!<br><br>';
$message .= 'ABBA Loan Repayment Information<br>';
$message .= 'info@abbafund.com<br>';
$message .= '555-555-5555';
$message .= '<div style="width:20px; height:50px"></div>';

$header  = 'To:' . $to . '\r\n';
$header  = "From:postmaster@jamesivey.com \r\n";
$header .= "Cc:james.ivey@csgi.com \r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html\r\n";
$header  = "Sender:postmaster@jamesivey.com \r\n";
$header  = 'Date:' . $headerdate . '\r\n';

if($source == "ForteCO") {
	$send = mail($to,$subject,$message,$header);
}

if($send == true) {
	echo "Message sent successfully...";
	error_log('Email sent successfully.' . "\n\n", 3, $destination);
	error_log('======================================================' . "\n\n\n", 3, $destination);
} else {
	echo "Message could not be sent...";
	error_log('Email could not be sent.' . "\n\n", 3, $destination);
	error_log('======================================================' . "\n\n\n", 3, $destination);	
}

?>