<html>
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


	$APILoginID       = forte_config('api_login_id');
	$secure_keys      = (array)forte_config('secure_transaction_keys', []);
	$SecureTransKey   = $secure_keys['swp_redirect_full'] ?? forte_config('secure_transaction_key');
	$transType          = '10';
	$totalAmount        = '0.01'; 
	$version            = '2.0';
	$cmdPaymethod       = 'insert';      //"insert" creates a paymethod and returns a token
	$cmdClient          = 'insert';      //"insert" creates a client and returns a token
	$millitime          = microtime(true) * 1000;
	$utc                = utc();		
	$data               = "$APILoginID|$transType|$version|$totalAmount|$utc|||||";
	$hash               = hash_hmac('md5',$data,$SecureTransKey);
?>
<body style="background:blue">
<!--form method='POST' style="margin: 20 50% 0 0; text-align:right" action='<?php echo htmlspecialchars(forte_swp_url('Redirect/default.aspx'), ENT_QUOTES); ?>' -->      <!-- production -->
<form method='POST' style="margin: 20 50% 0 0; text-align:right" action='<?php echo htmlspecialchars(forte_swp_url('Redirect/default.aspx'), ENT_QUOTES); ?>'>    <!-- sandbox -->
	Hash  <input type='' name='pg_ts_hash' value='<?php echo $hash;?>'/><br>
	API Login ID  <input type='' name='pg_api_login_id' value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"/><br>
	Transaction Type  <input type='' name='pg_transaction_type' value='<?php echo $transType;?>'/><br>
	<!--input type='' name='pg_valid_amount' value=''/ -->
	<input type='hidden' name='pg_version_number' value='<?php echo $version;?>'/>
	<input type='hidden' name='pg_utc_time' value='<?php echo $utc;?>'/>
	<!--input type='hidden' name='pg_payment_command' value='<?php echo $cmdPaymethod;?>'/>
	<input type='hidden' name='pg_client_command' value=''/ -->
	Amount  <input type='' name='pg_total_amount' value='<?php echo $totalAmount;?>'/-->
	<!--input type='hidden' name='pg_sales_tax_amount' value=''/>
	<input type='hidden' name='pg_convenience_fee' value=''/ -->
	<input type='hidden' name='pg_billto_postal_name_first' value='Fred'/>
	<input type='hidden' name='pg_billto_postal_name_last' value='Test'/>
	<input type='hidden' name='pg_billto_company_name' value='Forte Test'/>
	<input type='hidden' name='pg_billto_postal_street_line1' value='500 W. Bethany Drive'/>
	<input type='hidden' name='pg_billto_postal_street_line2' value='Suite 200'/>
	<input type='hidden' name='pg_billto_postal_city' value='Allen'/>
	<input type='hidden' name='pg_billto_postal_stateprov' value='TX'/>
	<input type='hidden' name='pg_billto_postal_postalcode' value='75013'/>
	<input type='hidden' name='pg_billto_postal_countrycode' value=''/>
	<input type='hidden' name='pg_billto_telecom_phone_number' value='214-555-7878'/>
	<input type='hidden' name='pg_billto_online_email' value='integration@forte.net'/>
	<input type='hidden' name='pg_payment_card_type' value='VISA'/>
	<input type='hidden' name='pg_payment_card_name' value='Forte Test'/>
	<input type='hidden' name='pg_payment_card_number' value='4111111111111111'/>
	<input type='hidden' name='pg_payment_card_expdate_month' value='08'/>
	<input type='hidden' name='pg_payment_card_expdate_year' value='2026'/>
	<input type='hidden' name='pg_payment_card_verification' value='555'/>
	<input type='hidden' name='pg_payment_check_trn' value=''/>
	<input type='hidden' name='pg_payment_check_account' value=''/>
	<input type='hidden' name='pg_payment_check_account_type' value=''/>
	<input type='hidden' name='pg_consumer_id' value=''/>
	<input type='hidden' name='pg_consumerorderid' value=''/>
	<input type='hidden' name='pg_walletid' value=''/>
	<input type='hidden' name='pg_merchant_data_1' value='csg'/>
	<input type='hidden' name='pg_merchant_data_2' value=''/>
	<input type='hidden' name='pg_merchant_data_3' value=''/>
	<input type='hidden' name='pg_merchant_data_4' value=''/>
	<input type='hidden' name='pg_line_item_header' value=''/>
	<input type='hidden' name='pg_line_item_1' value=''/>
	<input type='hidden' name='pg_line_item_2' value=''/>
	<input type='hidden' name='pg_line_item_3' value=''/>
	<input type='hidden' name='pg_line_item_4' value=''/>
	<input type='hidden' name='pg_user_defined_1' value=''/>
	<input type='hidden' name='pg_user_defined_2' value=''/>
	<input type='hidden' name='pg_user_defined_3' value=''/>
	<input type='hidden' name='pg_avs_method' value=''/>
	<!--input type='hidden' name='pg_schedule_transaction' value='1'/>
	<input type='hidden' name='pg_schedule_quantity' value='12'/>
	<input type='hidden' name='pg_schedule_frequency' value='20'/>
	<input type='hidden' name='pg_schedule_start_date' value='10012019'/ -->
	<input type='hidden' name='pg_transaction_order_number' value=''/>
	<!--input type='hidden' name='pg_client_id' value=''/>
	<input type='hidden' name='pg_payment_method_id' value=''/ -->
	<input type='hidden' name='pg_return_url' value='<?php echo htmlspecialchars(forte_swp_url('Redirect/results.aspx'), ENT_QUOTES); ?>'/-->
	<!--input type='hidden' name='pg_return_url' value='https://www.calligraphydallas.com/forte/capture.postback-SWP.php'/ --->
	<!--input type='hidden' name='pg_return_method' value='asyncpost'/ -->
	<input type='hidden' name='pg_client_status' value=''/>
	<input type='hidden' name='pg_note' value=''/>
	<input type='hidden' name='pg_is_default' value=''/>
	<input type='hidden' name='pg_procurement_card' value='FALSE'/>
	<input type='hidden' name='pg_customer_acct_code' value=''/>
	<input type='hidden' name='pg_swipe' value=''/>
	<input type='hidden' name='pg_swipe_data' value=''/>
	<input type=submit value='Pay Now'/>
</form>
</html>
