<?php

//this script builds the csv file

date_default_timezone_set('America/Chicago');

//define the header row
$headers = 'MID,"Name On Card",Card,"Last 4",Mo,Year,"Updated On","AU Code","AU Description","Report Created On"' . PHP_EOL;

//write the header row
$newfile = fopen($filename,"a");
fwrite($newfile, $headers);

//begin the loop and do the REST GET call
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
	
	//begin looping thru the GET call dataset
	$data = json_decode($response, true);
	for($i=0; $i < sizeof($data["results"]); $i++)
	{
		//define the fields
		$date = date("n/j/Y", strtotime($data["results"][$i]["card"]["au_updated_date"]));  //removes the timestamp from the date
		$pay_token = str_replace("mth_","",($data["results"][$i]["paymethod_token"]));      //removes mth_ from token
		$cust_token = str_replace("cst_","",($data["results"][$i]["customer_token"]));      //removes cst_ from token
		$merchant_id = str_replace("loc_","",($data["results"][$i]["location_id"]));        //removes loc_ from location id (aka merchant ID)
		$cc_type = ($data["results"][$i]["card"]["card_type"]);
		$card_name = ($data["results"][$i]["card"]["name_on_card"]);
		$masked_cc = ($data["results"][$i]["card"]["masked_account_number"]);
		$expire_mo = ($data["results"][$i]["card"]["expire_month"]);
		$expire_yr = ($data["results"][$i]["card"]["expire_year"]);
		$code = ($data["results"][$i]["card"]["au_code"]);
		$description = ($data["results"][$i]["card"]["au_description"]);
		
		//define the row
		$entries = $merchant_id.','.$card_name.','.$cc_type.','.$masked_cc.','.$expire_mo.','.$expire_yr.','.$date.','.$code.','.$description.','.$created_on . PHP_EOL;

		//write the row
		$sweet = fopen($filename, "a+");
		fwrite($sweet, $entries);
		$rowCount++;
	}
	
	//if the once-per-record word "links" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "links";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		else if($resultcount >= 1000){
			$c++;
		}
};

//Download the attachment to the user's hardrive
header('Content-Type: application/download');
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Length: " . filesize($filename));

$fp = fopen($filename, "r");
fpassthru($fp);

//close and delete the leftovers
$leftovers = 'AU*';
array_map( "fclose", glob( $leftovers ));
array_map( "unlink", glob( $leftovers ));

?>