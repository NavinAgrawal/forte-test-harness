<!DOCTYPE html>
<html>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<?php

require_once __DIR__ . '/config/bootstrap.php';
// https://sandbox.forte.net/checkout/getUTC?callback=?  //sandbox (for UTC)
// https://checkout.forte.net/getUTC?callback=?          //production (for UTC)

function utc() {
	$curlUTC = curl_init();
	curl_setopt($curlUTC, CURLOPT_URL, 'https://checkout.forte.net/getUTC?callback=?');
	curl_setopt($curlUTC, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($curlUTC, CURLOPT_RETURNTRANSFER, true);	
	$curlData = (curl_exec($curlUTC));
	$stripOpenParen = stripos($curlData,"(");
	$stripClosedParen = stripos($curlData,")");		
	$utc = substr($curlData,$stripOpenParen+1,$stripClosedParen-2);
	return $utc;
	curl_close($curlUTC);
}
	
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$totalamount = "0.01";
$cust_token = 'UCl7jTmtTESu-bBMnAp8nw';
$pay_token = '***********************';
$method = 'sale';  //can be token, auth, sale or schedule
$version = '2.0';
$ordernumber = 'invoice-1234';
$utc = utc();
$expireUTC = $utc+1;
$data = "$api_access_id|$method|$version|$totalamount|$utc|$ordernumber||";
$hash = hash_hmac('md5',$data,$api_secure_key);
?>

<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!--script type="text/javascript" src="https://checkout.forte.net/v2/js"></script>     <!-- production -->
<script type="text/javascript" src="https://sandbox.forte.net/checkout/v2/js"></script>    <!-- sandbox -->
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<script>
	function oncallback(e) {
        var formatted_json = JSON.stringify(JSON.parse(e.data), null, 2);
        $('#message').html(formatted_json);
		var response = JSON.parse(e.data);
		switch (response.event) {
			case 'begin':
			break;
			case 'success':
				alert('Thanks for your payment.' + "\n\n" + 'The trace number is:' + "\n" + response.trace_number);
			break;
			case 'failure':
				alert('Sorry, the transaction failed.' + "\n\n" + 'The failed reason is ' + response.response_description);
		}
	}
</script>
</head>
<body>
<pre style="margin-left:50px;" id="message"></pre>
<center>
<button 
	api_access_id="<?php echo htmlspecialchars(forte_config('api_access_id'), ENT_QUOTES); ?>"
	location_id="<?php echo htmlspecialchars(forte_config('location_id'), ENT_QUOTES); ?>"
	version_number="<?php echo $version;?>"
	callback="oncallback"
	total_amount="<?php echo $totalamount;?>"
	consumer_id="Account-ID-4444"
	order_number="<?php echo $ordernumber;?>"
	total_amount_attr="edit"
	reference_id="reference ID"
	method="<?php echo $method;?>"
	utc_time="<?php echo $utc;?>"
	signature="<?php echo $hash;?>"
	xdata_1="custom data number 1"
	xdata_2="custom data number 2"
	line_item_header="Lowtop or Hightop,Color,Size"
	line_item_1="High,Green,9"
	line_item_2="Low,Red,11.5"
	line_item_3="High,Blue,7"
	>Pay Now
</button>
</body>
</html>