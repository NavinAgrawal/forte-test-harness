<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script type="text/javascript" src="https://sandbox.forte.net/checkout/v2/js"></script> <!-- Sandbox -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
</head>

<script>
function oncallback(e) {
    var formatted_json = JSON.stringify(JSON.parse(e.data), null, 2);
    $('#message').html(formatted_json);
    var response = JSON.parse(e.data);

    switch (response.event) {
        case 'begin':
            break;
        case 'success':
            alert('Thanks for your payment.\n\nTrace Number: ' + response.trace_number);
            break;
        case 'failure':
            alert('Transaction failed.\n\nReason: ' + response.response_description);
            break;
    }
}
</script>

<?php
require_once __DIR__ . '/config/bootstrap.php';
function utc() {
    $curlUTC = curl_init();
    curl_setopt($curlUTC, CURLOPT_URL, 'https://checkout.forte.net/getUTC?callback=?');
    curl_setopt($curlUTC, CURLOPT_RETURNTRANSFER, true);
    $curlData = curl_exec($curlUTC);
    $start = strpos($curlData, '(');
    $end = strpos($curlData, ')');
    $utc = substr($curlData, $start + 1, $end - $start - 1);
    curl_close($curlUTC);
    return $utc;
}

// Forte credentials and parameters
$location_id     = forte_config('location_id');
$api_access_id   = forte_config('api_access_id');
$api_secure_key  = forte_config('api_secure_key');
$method          = 'token';  // or 'sale' if you want to charge immediately
$version         = '2.0';
$utc             = utc();

// Your stored Forte customer token
$customer_token  = 'cst_xxxxx'; // this customer exists in forte sandbox account

// Signature format with customer_token
$data = "$api_access_id|$method|$version||$utc|||";

$hash = hash_hmac('md5', $data, $api_secure_key);
?>

<body>
    <pre style="margin-left:50px;" id="message"></pre>
    <center>
	<button
		api_access_id="<?php echo htmlspecialchars(forte_config('api_access_id'), ENT_QUOTES); ?>"
		location_id="<?php echo htmlspecialchars(forte_config('location_id'), ENT_QUOTES); ?>"
		version_number="<?php echo $version; ?>"
		method="<?php echo $method; ?>"
		utc_time="<?php echo $utc; ?>"
		callback="oncallback"
		signature="<?php echo $hash; ?>"
		billing_company_name_attr="hide"
		>Pay Now
	</button>
    </center>
</body>
</html>
