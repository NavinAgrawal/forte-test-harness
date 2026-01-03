<?php require_once __DIR__ . '/config/bootstrap.php'; ?>
<!DOCTYPE html>
<html>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
 
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
<style>
body {
	background-image: url('csgforte2x.png');
}
.bluebutton {
	color: white;
	background-color: #1A7DC0 !important;
	height: 34px;
	width: 100px;
	font-size: 1rem;
	border: 1px solid #003366 !important;
	border-radius: 4px;
	box-shadow: 2px 2px #4d4d4d;
	text-align: center;
	cursor: default !important;
}

.bluebutton:hover {
	background-color: #125887 !important;
}

.bluebutton:focus {
	outline: 1px solid #000000;
}

.bluebutton:active {
	box-shadow: 0 0px #666;
	transform: translateY(2px);
}
</style>
</head>
<div style="height:160px">&nbsp;</div>
<div style="background-color:white; width:500px;"><pre style="margin-left:50px;" id="message"></pre></div>
<center>
<br><br><br><br>
<button class="bluebutton"
	api_access_id="<?php echo htmlspecialchars(forte_config('api_access_id'), ENT_QUOTES); ?>"
	location_id="<?php echo htmlspecialchars(forte_config('location_id'), ENT_QUOTES); ?>"
	version_number="2.0"
	order_number="A1234"
	billing_company_name="Account Holder"
	billing_company_name_attr="hide"
	callback="oncallback"
	method="sale"
	utc_time="638551172724990000"
	hash_method="sha256"
	signature="REDACTED_HASH"
	>Pay Now
</button>
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
