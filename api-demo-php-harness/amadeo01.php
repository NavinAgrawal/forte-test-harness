<html>
<?php
require_once __DIR__ . '/config/bootstrap.php';
	$api_login_id      = forte_config('api_login_id');
	$secure_trans_key  = forte_config('secure_transaction_key');
	$total_amount      = '0.00000000001'; 
	$trans_type        = '10';
	$version           = '2.0';
	$order_number      = '.';
	$millitime         = microtime(true) * 1000;
	$utc_time          = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
	$data              = "$api_login_id|$trans_type|$version|$total_amount|$utc_time|$order_number";
	$ts_hash           = hash_hmac('md5',$data,$secure_trans_key);
?>

<form method="post" action="<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>">
	<input type="hidden" name="pg_api_login_id" value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>">
	<input type="hidden" name="pg_transaction_type" value="<?php echo $trans_type; ?>">
	<input type="hidden" name="pg_version_number" value="2.0"/>
	<input type="hidden" name="pg_total_amount" value="<?php echo $total_amount; ?>">
	<input type="hidden" name="pg_utc_time" value="<?php echo $utc_time; ?>">
	<input type="hidden" name="pg_transaction_order_number" value="<?php echo $order_number; ?>">
	<input type="hidden" name="pg_ts_hash" value="<?php echo $ts_hash; ?>">
	<input type="submit" value="Pay Now">
</form>
</html>
