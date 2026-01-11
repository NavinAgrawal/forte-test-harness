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
	$SecureTransKey   = $secure_keys['swp_embedded_charge'] ?? forte_config('secure_transaction_key');
	$total_amount     = '0.03'; 
	$trans_type       = '10';
	$version_number   = '2.0';
	$tax              = '';
	$order_number     = '12345';
	$millitime        = microtime(true) * 1000;
	$utc_time         = utc();
	$data             = "$APILoginID|$trans_type|$version_number|$total_amount|$utc_time|$order_number";
	$hash             = hash_hmac('md5',$data,$SecureTransKey);
?>

<!-- sandbox -->
<center>
<form method='post' action='<?php echo htmlspecialchars(forte_swp_url('default.aspx'), ENT_QUOTES); ?>'>    
	<input type='hidden' name='pg_api_login_id' value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"/>
	<input type='hidden' name='pg_transaction_type' value='<?php echo $trans_type;?>'/>
	<input type='hidden' name='pg_version_number' value='<?php echo $version_number;?>'/>
	<input type='hidden' name='pg_total_amount' value='<?php echo $total_amount;?>'/>
	<input type='hidden' name='pg_sales_tax_amount' value='<?php echo $tax;?>'/>
	<input type='hidden' name='pg_utc_time' value='<?php echo $utc_time;?>'/>
	<input type='hidden' name='pg_transaction_order_number' value='<?php echo $order_number;?>'/>
	<input type='hidden' name='pg_ts_hash' value='<?php echo $hash;?>'/>
	<input type='hidden' name='pg_billto_postal_name_first' value='Forte'/>
	<input type='hidden' name='pg_billto_postal_name_last' value='Test'/>
	<input type='hidden' name='pg_billto_postal_name_company' value=''/>
	<input type='hidden' name='pg_billto_postal_street_line1' value='500 Bethany Dr.'/>
	<input type='hidden' name='pg_billto_postal_street_line2' value='Suite 200'/>
	<input type='hidden' name='pg_billto_postal_city' value='Allen'/>
	<input type='hidden' name='pg_billto_postal_state' value='Texas'/>
	<input type='hidden' name='pg_billto_postal_postalcode' value='75013'/>
	<input type='hidden' name='pg_billto_online_email' value='integration@forte.net'/> 
	<input type='hidden' name='pg_tb_color' value='#E6E6E6'/> 
	<input type='hidden' name='pg_return_url' value=''/> 
	<input type='hidden' name='pg_template_id' value='5'/>
	<input type='hidden' name='pg_bg_color' value=''/>
	<input type='hidden' name='pg_font_color' value=''/>
	<input type='hidden' name='pg_show_add_btn' value=''/>
	<input type='hidden' name='pg_receipt' value=''/>
	<input type='submit' value='Sandbox'/>
</form>

<!-- production -->
<center>
<form method='GET' action='<?php echo htmlspecialchars(forte_swp_url('co/charge.aspx'), ENT_QUOTES); ?>'>      
	<input type='hidden' name='pg_api_login_id' value="<?php echo htmlspecialchars(forte_config('api_login_id'), ENT_QUOTES); ?>"/>
	<input type='hidden' name='pg_transaction_type' value='<?php echo $trans_type;?>'/>
	<input type='hidden' name='pg_version_number' value='<?php echo $version_number;?>'/>
	<input type='hidden' name='e_pg_total_amount' value='<?php echo $total_amount;?>'/>
	<input type='hidden' name='pg_sales_tax_amount' value'<?php echo $tax;?>'/>
	<input type='hidden' name='pg_utc_time' value='<?php echo $utc_time;?>'/>
	<input type='hidden' name='pg_transaction_order_number' value='<?php echo $order_number;?>'/>
	<input type='hidden' name='pg_ts_hash' value='<?php echo $hash;?>'/>
	<input type='hidden' name='pg_billto_postal_name_first' value='Forte'/>
	<input type='hidden' name='pg_billto_postal_name_last' value='Test'/>
	<input type='hidden' name='pg_billto_postal_name_company' value=''/>
	<input type='hidden' name='pg_billto_postal_street_line1' value='500 Bethany Dr.'/>
	<input type='hidden' name='pg_billto_postal_street_line2' value='Suite 200'/>
	<input type='hidden' name='pg_billto_postal_city' value='Allen'/>
	<input type='hidden' name='pg_billto_postal_state' value='Texas'/>
	<input type='hidden' name='pg_billto_postal_postalcode' value='75013'/>
	<input type='hidden' name='pg_billto_online_email' value='integration@forte.net'/> 
	<input type='hidden' name='pg_tb_color' value='#E6E6E6'/> 
	<input type='hidden' name='pg_return_url' value='<?php echo htmlspecialchars(forte_swp_url('PostTest.aspx'), ENT_QUOTES); ?>'/> 
	<input type='hidden' name='pg_template_id' value=''/>
	<input type='hidden' name='pg_bg_color' value=''/>
	<input type='hidden' name='pg_font_color' value=''/>
	<input type='hidden' name='pg_show_add_btn' value=''/>
	<input type='hidden' name='pg_receipt' value=''/>
	<input type='submit' value='Bradley'/>
</form>
</html>
