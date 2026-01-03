<?php
require_once __DIR__ . '/config/bootstrap.php';
$js_url = forte_js_url();
$api_login_id = forte_config('api_login_id');
?>
<html>
<head>
	<script type="text/javascript" src="<?php echo htmlspecialchars($js_url, ENT_QUOTES); ?>"></script>
</head>
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
Credit Card Number:<input type="text" forte-data="card_number" /><br>
Exp month:<select forte-data="expire_month">
      <option value="1">Jan - 1</option>
      <option value="2">Feb - 2</option>
      <option value="3">Mar - 3</option>
      <option value="4">Apr - 4</option>
      <option value="5">May - 5</option>
      <option value="6">Jun - 6</option>
      <option value="7">Jul - 7</option>
      <option value="8">Aug - 8</option>
      <option value="9">Sep - 9</option>
      <option value="10">Oct - 10</option>
      <option value="11">Nov - 11</option>
      <option value="12">Dec - 12</option>
   </select><br>
Exp Year:<select forte-data="expire_year">
      <option value="2025">2025</option>
      <option value="2027">2027</option>
   </select><br>
CVV:
   <input type="text" forte-data="cvv" />
   <button type="submit" forte-api-login-id="<?php echo htmlspecialchars($api_login_id, ENT_QUOTES); ?>" forte-callback-success="onTokenCreated" forte-callback-error="onTokenFailed">Submit</button>

<!-- eCheck -->
<!-- Account Number:<input type="text" forte-data="account_number" />
Routing Number:<input type="text" forte-data="routing_number" />
Account Type:<input type="text" forte-data="account_type" />
<button type="submit" forte-api-login-id="<?php echo htmlspecialchars($api_login_id, ENT_QUOTES); ?>" forte-callback-success="onTokenCreated" forte-callback-error="onTokenFailed">Submit</button -->
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
