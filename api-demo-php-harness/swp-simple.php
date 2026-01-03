<?php require_once __DIR__ . '/config/bootstrap.php'; ?>
<html>
<center>
<div style="width:400px; text-align:left">

<!-- Unsigned --->

<!--form method='Post' action= '<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>'>          <!-- production -->
<form method='post' action= '<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>'>      <!-- sandbox -->
	<!--input type='hidden' name='pg_api_login_id' value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"/ -->
	<input type='hidden' name='pg_api_login_id' value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"/ -->
	<input type="hidden" name="pg_transaction_type" value=""/>
	<!--input type='hidden' name='pg_continue_url' value='https://www.microsoft.com'/ -->	
	<input type='hidden' name='pg_return_method' value='AsyncPost'/ -->
	<input type='hidden' name='pg_return_url' value='https://www.calligraphydallas.com/forte/capture.postback-SWP.php'/ -->
	<!--input type='hidden' name='pg_continue_description' value='https://www.calligraphydallas.com/forte/swp-postback.php'/ -->
	<!--input type='hidden' name='pg_cancel_description' value='https://www.calligraphydallas.com/forte/swp-postback.php'/ -->
	<input type='submit' value='Pay Now'> Unsigned
</form>
</html>


<!-- Unsigned super simple -->

<form method='Post' action= '<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>'>          <!-- production -->
<!--form method='post' action= '<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>'>      <!-- sandbox -->
	<input type='hidden' name='pg_api_login_id' value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"/ -->
	<input type='submit' value='Pay Now'> Unsigned Super simple 
</form>



<!-- Signed (includes UTC and signature hash)  -->

<?php
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

	$api_login_id      = forte_config('api_login_id');
	$secure_trans_key  = forte_config('secure_transaction_key');
	$order_number      = 'abc123';
	$version           = '2.0';
	//$millitime         = microtime(true) * 1000;
$utc = utc();
$utc = $utc;
	//$utc_time          = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
	$data              = "$api_login_id||$version||$utc|";
	$ts_hash           = hash_hmac('md5',$data,$secure_trans_key);
?>
<form method='Post' action= '<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>'>          <!-- production -->
<!--form method="post" action="<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>">        <!-- sandbox -->
	<input type="hidden" name="pg_api_login_id" value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"/>
	<input type="hidden" name="pg_transaction_type" value="10"/>
	<input type='hidden' name='e_pg_total_amount' value='0.01'/>
	<input type="hidden" name="pg_transaction_order_number" value="<?php echo $order_number; ?>"/>
	<input type="hidden" name="pg_version_number" value="2.0"/>
	<input type="hidden" name="pg_utc_time" value="<?php echo $utc; ?>"/>
	<input type="hidden" name="pg_ts_hash" value="<?php echo $ts_hash; ?>"/>
	<INPUT TYPE=SUBMIT value='Pay Now'> Signed (includes UTC and signature hash)
</form>
</div>
</html>
