<html>
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

	$APILoginID       = 'YOUR_API_LOGIN_ID';		
	$SecureTransKey   = 'I5wrc7oh6e';
	$client_id        = '';                            //can be associated with an existing client, or it can be a clientless paymethod
	$millitime        = microtime(true) * 1000;
	//$utc_time         = number_format(($millitime * 10000) + 621355968000000000 , 0, '.', '');
	$utc_time	= utc();
	$data             = "$APILoginID|$utc_time";
	$hash             = hash_hmac('md5',$data,$SecureTransKey);
?>
<!-- sandbox -->
<center>
<form method='post' action='https://sandbox.paymentsgateway.net/SWP/co/capture.aspx?
	APILoginID=<?php echo $APILoginID;?>
	&UTCTime=<?php echo $utc_time;?>
	&TSHash=<?php echo $hash;?>
	&clientID=<?php echo $client_id;?>
	&bgcolor=ffffff
	&tbcolor=ffffff
	&style=5
	&msg=Complete all the fields and click Add Payment.
	&msg-loc=bottom		
	&msg-fontsize=12pt
	&showbtn2=yes
	&btntext2=Add Another Payment Method'>
	<input type="submit" value="Sandbox">
</form>

<!-- production -->
<center>
<form method='post' action='https://swp.paymentsgateway.net/co/capture.aspx?
	APILoginID=<?php echo $APILoginID;?>
	&UTCTime=<?php echo $utc_time;?>
	&TSHash=<?php echo $hash;?>
	&clientID=<?php echo $client_id;?>
	&bgcolor=ffffff
	&tbcolor=ffffff
	&style=5
	&msg=Complete all the fields and click Add Payment.
	&msg-loc=bottom		
	&msg-fontsize=12pt
	&showbtn2=yes
	&btntext2=Add Another Payment Method'>
	<input type="submit" value="Production">
</form>
</html>