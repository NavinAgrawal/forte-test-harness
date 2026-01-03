<html>
<!--
Forte Checkout Integration Guide:   http://www.forte.net/devdocs/a_checkout/co_product_overview.htm
Forte Checkout Workbench:           https://sandbox.forte.net/checkout/workbench/
Using Webhooks:                     http://www.forte.net/devdocs/reference/webhooks.htm
Sample Code:                        https://bitbucket.org/fortepayments/
Transaction Response Codes:         https://www.forte.net/devdocs/reference/response_codes.htm
Frequently Asked Questions:         https://www.forte.net/devdocs/reference/faq.htm
Forte Technical Support:
			7:00 am - 7:00 pm CST
			866.290.5400 option 5
			integration@forte.net				
-->
<?php
require_once __DIR__ . '/config/bootstrap.php';
	$API_access_id  = forte_config('api_access_id');
	$API_secure_key = forte_config('api_secure_key');
	$subtotal_amount = "2.00";
	$location_id = forte_config('location_id');
	$method = 'sale';
	$version = '2.0';
	$ordernumber = '1';
	date_default_timezone_set("America/Chicago");
	$unixtime = strtotime(gmdate('Y-m-d H:i:s'));
	$millitime = microtime(true) * 1000;
	$utc = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
	$data = "$API_access_id|$method|$version|$subtotal_amount|$utc|$ordernumber||";
	$hash = hash_hmac('md5',$data,$API_secure_key);
?>
<head>

<!--script type="text/javascript" src="https://checkout.forte.net/v2/js"></script -->     <!-- production -->
<script type="text/javascript" src="https://sandbox.forte.net/checkout/v2/js"></script>    <!-- sandbox -->
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>

<script>
	function oncallback(e) {
        var formatted_json = JSON.stringify(JSON.parse(e.data), null);
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
	<button api_access_id=<?php echo $API_access_id;?>
		location_id=<?php echo $location_id;?>
		version_number=<?php echo $version;?>
		callback="oncallback"
		swipe=EMV-1
		billing_company_name="Account Holder"
		consumer_id="abc123"
		method=<?php echo $method;?>
		total_amount="<?php echo $subtotal_amount;?>"
		utc_time=<?php echo $utc;?>
		signature=<?php echo $hash;?>
		order_number=<?php echo $ordernumber;?>
		billing_name_attr="hide"
		>Pay now</button>
</body>
</html>

<!--
api_access_id: REDACTED_HASH
version_number: 2.0
method: auth
save_token: False
utc_hash_method: md5
order_number: 924a7b65-2a87-42c5-86f6-effe1cbdfb00
location_id: 201849
key: REDACTED_HASH
utc_time: 638533746471887403
expire_utc: 638533758471887403
signature: REDACTED_HASH
billing_name:
billing_name_attr: edit,required
billing_street_line1:
billing_street_line1_attr: edit
billing_street_line2:
billing_street_line2_attr: edit
billing_locality:
billing_locality_attr: edit
billing_region:
billing_region_attr: edit
billing_postal_code:
billing_postal_code_attr: edit
allowed_methods: visa,mast,amex,dine,jcb,disc
total_amount: 13.00
swipe: EMV-1
hybrid_close_modal: true
callback: onForteCallback
id: forteButton
style: display:none
class: fortebtn
data-id: modal0
request_id: 1a02bb9e-9a27-415f-de03-a2a90d65c926