<?php

//This script listens for a SWP postback, captures it and inserts it into the MySQL database 'transactions' table

$rawString = file_get_contents("php://input");
parse_str($rawString, $data);

date_default_timezone_set('America/Chicago');

//database credentials
$host_name = 'db729615427.db.1and1.com';
$database  = 'db729615427';
$user_name = 'dbo729615427';
$password  = 'Pumpkin&77';

//establish a connection to the database
$connect = mysqli_connect($host_name, $user_name, $password, $database);
if (mysqli_connect_error()) {
    echo("Database connection failed: " . mysqli_connect_error());
}
else {
    echo "Connection to MySQL database was successful.";
}

// This is a SQL command that inserts the postback values into the 'transactions' table.
// The order doesn't matter, as long as the names line up with the values.
// The names are the database fields, the VALUES are the postback fields.

$sql = ("INSERT INTO transactions (
	time_received,
	transaction_id,
	amount,
	billing_firstname,
	billing_lastname,
	name_on_card,
	customer_id,
	order_number,
	authorization_code,
	card_last_four,
	card_type,
	routing_number,
	account_holder,
	echeck_last_4,
	account_type,
	response_code,
	response_desc
	)
	VALUES (
		'".date('Y-m-d H:i:s')."',
		'".$data['pg_trace_number']."',
		'".$data['pg_total_amount']."',
		'".$data['pg_billto_postal_name_first']."',
		'".$data['pg_billto_postal_name_last']."',
		'".$data['transaction']['card']['name_on_card']."',
		'".$data['transaction']['customer_id']."',
		'".$data['pg_consumerorderid']."',
		'".$data['pg_authorization_code']."',
		'".$data['pg_last4']."',
		'".$data['pg_payment_card_type']."',
		'".$data['transaction']['echeck']['routing_number']."',
		'".$data['transaction']['echeck']['account_holder']."',
		'".$data['transaction']['echeck']['masked_account_number']."',
		'".$data['transaction']['echeck']['account_type']."',
		'".$data['pg_response_code']."',
		'".$data['pg_response_description']."'
		)"
	);
		
mysqli_query($connect, $sql);
mysqli_close($connect);

//write the postback (including headers) to a logfile named james.log
$date = new DateTime();
$headers = apache_request_headers();
$destination  = 'james.log';

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