<?php
require_once __DIR__ . '/config/bootstrap.php';
$js_url = forte_js_url();
$api_login_id = forte_config('api_login_id');
?>
<html>
<style>
input, text { padding-left:10px; }
</style>
<style>
body {
	background-image: url('optum.png');
	background-repeat:no-repeat;
	background-size:cover;
}
.bluebutton {
	color: white;
	background-color: #ff8800 !important;
	background-repeat:no-repeat;
	background-size:cover;
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
.formstyle {
  border: 5px outset red;
  background-color: lightblue;
  text-align: center;
  width: 400px;
}

</style>
<body bgcolor="#ddf7d5">
<center>
<div class="formstyle">   
	<script type="text/javascript" src="<?php echo htmlspecialchars($js_url, ENT_QUOTES); ?>"></script>
	
	<!--Credit Card -->
	<br><form id="card" class="content" >
	Name on Card: <input type="text" forte-data="name_on_card" value="Forte Support"><br>
	Card Type: <input type="text" forte-data="card_type" value="VISA"><br>
	Card Number: <input type="text" forte-data="card_number" value="4111111111111111"><br>
	Expire Month: <input type="text" forte-data="expire_month" value="04"><br>
	Expire Year: <input type="text" forte-data="expire_year" value="2029"><br>
	CVV Code: <input type="text" forte-data="cvv" value="069"><br><br>
	<button forte-api-login-id="<?php echo htmlspecialchars($api_login_id, ENT_QUOTES); ?>" forte-callback-success="onTokenCreated" forte-callback-error="onTokenFailed">Create Onetime Token</button>
	</form>

	<!-- eCheck -->
	<!--br><form id="check" class="content" style="margin: 0 40% 0 0; text-align:right">
	Account Holder: <input type="text" forte-data="account_holder" value="Forte Support"><br>
	Routing Number: <input type="text" forte-data="routing_number" value="011401533"><br>
	Account Number: <input type="text" forte-data="account_number" value="123456789"><br>
	Checking or Savings: <input type="text" forte-data="account_type" value="checking"><br><br>
	<button forte-api-login-id="<?php echo htmlspecialchars($api_login_id, ENT_QUOTES); ?>" forte-callback-success="onTokenCreated" forte-callback-error="onTokenFailed">Create Onetime Token</button>
	</form -->
</div>	
	<script type="text/javascript">
      function onTokenCreated(response) {
		var ott = response.onetime_token;
		var type = response.card_type;
		var name = document.getElementsByTagName("input")[0].value;
		alert("The onetime token is: " + ott);
		window.location.href = "REST-onetime.to.transaction.php?ott=" + ott + "&type=" + type + "&name_on_card=" + name; 
      }
      function onTokenFailed(response) {
        alert("Getting onetime_token failed: " + response.response_description);
      }
   </script>

</html>
