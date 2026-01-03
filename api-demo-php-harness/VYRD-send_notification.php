<?php

//This script captures the webhook, writes it to a logfile, and sends a notification email

date_default_timezone_set('America/Chicago');

$rawData      = file_get_contents("php://input");
$data         = json_decode($rawData, true);
$pretty       = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
$date         = new DateTime();
$headers      = apache_request_headers();
$destination  = 'VYRD-webhook.log';

$to          = $data["transaction"]["billing_address"]["email"];
$subject     = "Thank You for your Payment";
$firstname   = $data["transaction"]["billing_address"]["first_name"];
$lastname    = $data["transaction"]["billing_address"]["last_name"];
$name        = $firstname . ' ' . $lastname;
$phone       = $data["transaction"]["billing_address"]["phone"];
$email       = $data["transaction"]["billing_address"]["email"];
$policy      = $data["transaction"]["customer_id"];
$last_four   = $data["transaction"]["card"]["last_4_account_number"];
$auth_code   = $data["transaction"]["authorization_code"];
$trans_id    = str_replace("trn_","",($data["transaction"]["transaction_id"]));
$amount      = $data["transaction"]["authorization_amount"];
$source      = $data["source"];
$action      = $data["transaction"]["action"];

error_log($date->format('F j Y, g:i:s a T') . "\n\n", 3, $destination);
error_log('HEADERS:' . "\n", 3, $destination);
foreach ($headers as $header => $value) {
	error_log("$header: $value" . "\n", 3, $destination);
}
error_log("\n", 3, $destination);
error_log('Raw:' . "\n" . $rawData . "\n\n", 3, $destination);
error_log('Pretty:' . "\n", 3, $destination);
error_log($pretty . "\n\n", 3, $destination);

$message  = '<img src="https://www.jamesivey.com/OLD/vyrd/images/header01.jpg"><br><br>';
$message .= $date->format('F j, Y') . '<br>';
$message .= $date->format('g:i a T') . '<br><br>';
$message .= 'Name: ' . $name . '<br>';
$message .= 'Phone Number: ' . $phone . '<br>';
$message .= 'Email Address: ' . $email . '<br>';
$message .= 'Policy Number: ' . $policy . '<br>';
$message .= 'Authorization Code: ' . $auth_code . '<br>';
$message .= 'Last 4 digits: ' . $last_four . '<br>';
$message .= 'Transaction ID: ' . $trans_id . '<br><br>';
$message .= 'Your payment of $' . $amount . ' has been received. <br><br>';
$message .= 'Thank you!<br><br>';
$message .= 'VYRD Insurance<br>';
$message .= 'info@vyrd.co<br>';
$message .= '888-806-8973';
$message .= '<div style="width:20px; height:50px"></div>';

$header  = "From:postmaster@jamesivey.com \r\n";
$header .= "Cc:james.ivey@csgi.com \r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html\r\n";

if ($source == "ForteCO") {
	$sendmail = mail ($to,$subject,$message,$header);
}

if( $sendmail == true ) {
	echo "Message sent successfully...";
	error_log('Email sent successfully.' . "\n\n", 3, $destination);
	error_log('======================================================' . "\n\n\n", 3, $destination);
} else {
	echo "Message could not be sent...";
	error_log('Email could not be sent.' . "\n\n", 3, $destination);
	error_log('======================================================' . "\n\n\n", 3, $destination);	
}

?>