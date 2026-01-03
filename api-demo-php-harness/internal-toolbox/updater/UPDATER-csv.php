<?php

// this script builds the csv file

// define the header row
$headers = 'MID,"Name On Card","Consumer ID",Card,"Last 4",Mo,Year,"Updated On","AU Code","AU Description","Report Created On"' . PHP_EOL;

// write the header row
$newfile = fopen($filename,"a");
fwrite($newfile, $headers);

// begin the loop and do the REST GET call
$c = 0;
$rowCount = 0;
while($c >= 0){	
	$ch = curl_init($endpoint.'&page_index='.$c);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Basic ' . $auth_token,
		'X-Forte-Auth-Organization-id: ' . $organization_id,
		'Accept:application/json'
	));
	
	$response = curl_exec($ch);
	curl_close($ch);
	
	// begin looping thru the GET call dataset
	$data = json_decode($response, true);
	for($i=0; $i < sizeof($data["results"]); 
	$i++)
	{
		// define the fields
		$date         = date("n/j/Y", strtotime($data["results"][$i]["card"]["au_updated_date"]));  //removes the timestamp from the date
		$pay_token    = str_replace("mth_","",($data["results"][$i]["paymethod_token"]));      //removes mth_ from token
		$cust_token   = str_replace("cst_","",($data["results"][$i]["customer_token"]));      //removes cst_ from token
		$merchant_id  = str_replace("loc_","",($data["results"][$i]["location_id"]));        //removes loc_ from location id (aka merchant ID)
		$cc_type      = ($data["results"][$i]["card"]["card_type"]);
		$card_name    = ($data["results"][$i]["card"]["name_on_card"]);
		$last_four    = ($data["results"][$i]["card"]["last_4_account_number"]);
		//$expire_mo  = ($data["results"][$i]["card"]["expire_month"]);
		$expire_mo    = sprintf("%02d",($data["results"][$i]["card"]["expire_month"]));
		$expire_yr    = ($data["results"][$i]["card"]["expire_year"]);
		$code         = ($data["results"][$i]["card"]["au_code"]);
		$description  = ($data["results"][$i]["card"]["au_description"]);
		$customer_id  = ($data["results"][$i]["customer_id"]);
		
		// define the row
		$entries = $merchant_id.','.$card_name.','.$customer_id.','.$cc_type.','.$last_four.','.$expire_mo.','.$expire_yr.','.$date.','.$code.','.$description.','.$created_on . PHP_EOL;

		//write the row
		$sweet = fopen($filename, "a+");
		fwrite($sweet, $entries);
		$rowCount++;
	}
	
	// if the word "links" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "links";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		else if($resultcount >= 1000){
			$c++;
		}
};

?>