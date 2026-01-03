<!DOCTYPE html>
<html>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
<?php
require_once __DIR__ . '/config/bootstrap.php';
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
$location_id = forte_config('location_id');
$api_access_id = forte_config('api_access_id');
$api_secure_key = forte_config('api_secure_key');
$totalamount = "0.02";
$method = 'sale';  
$version = '2.0';
$utc = utc();
$utc = $utc;
$data = "$api_access_id|$method|$version|$totalamount|$utc|||";
$hash = hash_hmac('sha256',$data,$api_secure_key);
?>

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<!--script type="text/javascript" src="https://checkout.forte.net/v2/js"></script>     <!-- production -->
	<script type="text/javascript" src="https://sandbox.forte.net/checkout/v2/js"></script>    <!-- sandbox -->
	<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
</head>
<body>
<center>
<center>
<br>
<p style="font-size:20px; color:blue; font-weight:bold; font-family:Tahoma">To pay your bill, please click the button below.</p>
<br>
<button class="bluebutton"
	api_access_id="<?php echo htmlspecialchars(forte_config('api_access_id'), ENT_QUOTES); ?>"
	location_id="<?php echo htmlspecialchars(forte_config('location_id'), ENT_QUOTES); ?>"
	version_number="<?php echo $version;?>"
	total_amount="<?php echo $totalamount;?>"
	method="<?php echo $method;?>"
	utc_time="<?php echo $utc;?>"
	hash_method="sha256"
	signature="<?php echo $hash;?>"
	>Pay Now
</button>
</body>
</html>