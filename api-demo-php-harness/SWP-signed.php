<html>

<?php

require_once __DIR__ . '/config/bootstrap.php';
//These variables calculate the hash signature for SWP signed transactions. 

	$api_login_id      = forte_config('api_login_id');
	$secure_trans_key  = forte_config('secure_transaction_key');
	$total_amount      = ''; 
	$trans_type        = '';
	$version           = '2.0';
	$order_number      = 'abc123';
	$millitime         = microtime(true) * 1000;
	$utc_time          = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
	$data              = "$api_login_id||$version||$utc_time|";
	$ts_hash           = hash_hmac('md5',$data,$secure_trans_key);
?>

<form method="post" action="<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>" -->         <!-- production -->
<!--form method="post" action="<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>">        <!-- sandbox -->
	<input type="hidden" name="pg_api_login_id" value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"/>
	<label for="cc">Credit Card:</label>
	<input type="radio" id="pg_transaction_type" name="pg_transaction_type" value="10"/><br>
	<label for="echeck">Echeck/ACH:</label>
	<input type="radio" id="pg_transaction_type" name="pg_transaction_type" value="20"/><br>
	<input type="hidden" name="pg_version_number" value="2.0"/>
	<label for="Total">Total Amount:</label><br>
	<input type="text" name="pg_total_amount" value=""/><br>
	<input type="hidden" name="pg_utc_time" value="<?php echo $utc_time; ?>"/>
	<input type="hidden" name="pg_transaction_order_number" value="<?php echo $order_number; ?>"/>
	<input type="hidden" name="pg_ts_hash" value="<?php echo $ts_hash; ?>"/>
	<label for="cc">First Name:</label><br>
	<input type='text' name='pg_billto_postal_name_first' value='' required/><br>
	<label for="cc">Last Name:</label><br>
	<input type='text' name='pg_billto_postal_name_last' value='' required/><br>
	<input type='checkbox' name='I am not a robot' required/><br>
	<span class="checkbox-text">I am not a robot.
	<INPUT TYPE=SUBMIT value='Pay Now'></form>
</html>
