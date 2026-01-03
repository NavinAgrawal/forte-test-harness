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

	$api_login_id      = forte_config('api_login_id');
	$secure_trans_key  = forte_config('secure_transaction_key');
	$total_amount      = '6.01'; 
	$trans_type        = '10';
	$version           = '2.0';
	$order_number      = '..';
	$utc = utc();
	$utc = $utc;
	$data              = "$api_login_id|$trans_type|$version|$total_amount|$utc|$order_number";
	$ts_hash           = hash_hmac('md5',$data,$secure_trans_key);
?>

<center>
<form method="post" action="<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>" -->         <!-- production -->
<!--form method="post" action="<?php echo htmlspecialchars(forte_swp_url('co/default.aspx'), ENT_QUOTES); ?>">        <!-- sandbox -->	
	<input type='hidden' name='pg_api_login_id' value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"/>
	<input type="hidden" name="pg_transaction_type" value="10"/>
	<input type="hidden" name="pg_version_number" value="2.0"/>
	<input type="hidden" name="pg_total_amount" value="<?php echo $total_amount; ?>"/>
	<input type="hidden" name="pg_utc_time" value="<?php echo $utc; ?>"/>
	<input type="hidden" name="pg_transaction_order_number" value="<?php echo $order_number; ?>"/>
	<input type="hidden" name="pg_ts_hash" value="<?php echo $ts_hash; ?>"/>
	<input type='hidden' name='pg_line_item_header' value='Permit Number,Style,Price'/>
	<input type='hidden' name='pg_line_item_1' value='REC19-00124,Blue,10.04'/>
	<input type='hidden' name='pg_line_item_2' value='REC19-00125,Red,3'/>
	<input type='hidden' name='pg_line_item_3' value='REC19-00126,green,2'/>
	<input type='hidden' name='pg_line_item_4' value='REC19-00127,purple,1'/>	
	<input type='hidden' name='pg_return_url' value='<?php echo htmlspecialchars(forte_swp_url('PostTest.aspx'), ENT_QUOTES); ?>'/>
	<!--input type='hidden' name='pg_swipe' value='EMV-1'/ -->
	<input type='submit' value='Pay Now'>
</form>
</html>
