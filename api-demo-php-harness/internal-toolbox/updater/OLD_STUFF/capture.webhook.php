<?php

//This script listens for a webhook post, captures it and inserts it into the MySQL database 'transactions' table

$rawData = file_get_contents("php://input");
$data    = json_decode($rawData, true);
$pretty  = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

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

// This is a SQL command that inserts the webhook values into the 'transactions' table.
// The order doesn't matter, as long as the names line up with the values.
// The names are the database fields, the VALUES are the webhook fields.

$sql = ("INSERT INTO transactions (
	time_received,
	organization_id,
	location_id,
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
		'".$data['organization_id']."',
		'".$data['location_id']."',
		'".$data['transaction']['transaction_id']."',
		'".$data['transaction']['authorization_amount']."',
		'".$data['transaction']['billing_address']['first_name']."',
		'".$data['transaction']['billing_address']['last_name']."',
		'".$data['transaction']['card']['name_on_card']."',
		'".$data['transaction']['customer_id']."',
		'".$data['transaction']['order_number']."',
		'".$data['transaction']['authorization_code']."',
		'".$data['transaction']['card']['masked_account_number']."',
		'".$data['transaction']['card']['card_type']."',
		'".$data['transaction']['echeck']['routing_number']."',
		'".$data['transaction']['echeck']['account_holder']."',
		'".$data['transaction']['echeck']['masked_account_number']."',
		'".$data['transaction']['echeck']['account_type']."',
		'".$data['transaction']['response']['response_code']."',
		'".$data['transaction']['response']['response_desc']."'
		)"
	);
		
mysqli_query($connect, $sql);
mysqli_close($connect);

//write the raw webhook (including headers) to a logfile named james.log
$date = new DateTime();
$headers = apache_request_headers();
$destination  = 'james.log';

error_log($date->format('F j Y, g:i:s a T') . "\n" . 'D E X   W E B H O O K' . "\n" . 'Headers:' . "\n", 3, $destination);
foreach ($headers as $header => $value) {
	error_log("    $header: $value" . "\n", 3, $destination);
}
error_log('Raw:' . "\n    " . $rawData . "\n", 3, $destination);
error_log('Pretty:' . "\n" . $pretty . "\n\n\n", 3, $destination);

?>