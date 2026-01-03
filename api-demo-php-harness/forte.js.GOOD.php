<?php
require_once __DIR__ . '/config/bootstrap.php';
$js_url = forte_js_url();
$api_login_id = forte_config('api_login_id');
?>
<html>

	<script type="text/javascript" src="<?php echo htmlspecialchars($js_url, ENT_QUOTES); ?>"></script>
	
	<script type="text/javascript">
	function onTokenCreated(response) {
		var jsonPretty = JSON.stringify(response, undefined, 2);
		document.write('<pre>' + '<div style="margin-left:40%;">' + jsonPretty + '</pre>');	
	}
	function onTokenFailed(response) {
		var jsonPretty = JSON.stringify(response, undefined, 2);
		document.write('<pre>' + jsonPretty + '</pre>');	
	}
	</script>
	
	<style>
	body {
		background-color: white;
	}
	.content {
		margin: 0 40% 0 0;
		text-align: right;
	}
	</style>
	
	<!--Credit Card -->
	<br><form id="card" class="content">
	Name on Card: <input type="text" forte-data="name_on_card" value="Forte Support"><br>
	Card Type: <input type="text" forte-data="card_type" value="VISA"><br>
	Card Number: <input type="text" forte-data="card_number" value="4111111111111111"><br>
	Expire Month: <input type="text" forte-data="expire_month" value="01"><br>
	Expire Year: <input type="text" forte-data="expire_year" value="2027"><br>
	CVV Code: <input type="text" forte-data="cvv" value="123"><br><br>
	<button forte-api-login-id="<?php echo htmlspecialchars($api_login_id, ENT_QUOTES); ?>" forte-callback-success="onTokenCreated" forte-callback-error="onTokenFailed">Create Onetime Token</button>
	</form -->

	<!-- eCheck -->
	<!--br><form id="check" class="content" style="margin: 0 40% 0 0; text-align:right">
	Account Holder: <input type="text" forte-data="account_holder" value="Forte Support"><br>
	Routing Number: <input type="text" forte-data="routing_number" value="011401533"><br>
	Account Number: <input type="text" forte-data="account_number" value="123456789"><br>
	Checking or Savings: <input type="text" forte-data="account_type" value="checking"><br><br>
	<button forte-api-login-id="<?php echo htmlspecialchars($api_login_id, ENT_QUOTES); ?>" forte-callback-success="onTokenCreated" forte-callback-error="onTokenFailed">Create Onetime Token</button>
	</form -->
	
</html>
