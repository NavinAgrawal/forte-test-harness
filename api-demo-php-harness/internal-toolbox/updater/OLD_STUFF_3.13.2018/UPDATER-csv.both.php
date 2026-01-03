<html>
<?php

//disconnect these two files so it writes anew instead of append the old
unlink('files/AU.Report.xml');
unlink('files/AU.Report.csv');

//define the header row
$headers = 'MID,Name_on_Card,Card,Last_4,Mo,Year,Updated,AU_Code,AU_Description,Paymethod_Token,Customer_Token' . PHP_EOL;

//write the header row
$newfile = fopen('files/AU.Report.csv',"a");
fwrite($newfile, $headers);

//GET the data
$i = 0;
while($i >= 0){
	$ch = curl_init($endpoint . '&page_index='.$i);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Basic ' . $auth_token,
		'X-Forte-Auth-Organization-id: ' . $organization_id,
		'Accept:application/xml',
		'Content-type: application/xml'
	));

	$response = curl_exec($ch);
	curl_close($ch);
	file_put_contents('files/AU.Report.xml',$response);

	$xml = simplexml_load_string($response);
	if (strpos($response, 'Error') !== false) {
	echo (string)$xml->response->response_desc;
	die;
	}
	
	//parse thru the data plucking the fields we want
	$doc = new DOMDocument();
	$doc->load( 'files/AU.Report.xml' ); 
	$au_records = $doc->getElementsByTagName( "results" );

	foreach( $au_records as $au_record ) 
	{
		$paymethods = $au_record->getElementsByTagName( "paymethod_token" );
		$paymethod = $paymethods->item(0)->nodeValue;
		$pay_token = str_replace("mth_","",$paymethod);              //removes mth_ from beginning of token
		
		$customers = $au_record->getElementsByTagName( "customer_token" );
		$customer = $customers->item(0)->nodeValue;
		$cust_token = str_replace("cst_","",$customer);               //removes cst_ from beginning of token

		$labels = $au_record->getElementsByTagName( "label" );
		$label = $labels->item(0)->nodeValue;
		
		$locations = $au_record->getElementsByTagName( "location_id" );
		$location = $locations->item(0)->nodeValue;
		$merchant_id = str_replace("loc_","",$location);                 //removes loc_ from beginning of location id
		
		$masked_CCs = $au_record->getElementsByTagName( "masked_account_number" );
		$masked_CC = $masked_CCs->item(0)->nodeValue;
		
		$cust_IDs = $au_record->getElementsByTagName( "customer_id" );
		$cust_ID = $cust_IDs->item(0)->nodeValue;
		
		$card_names = $au_record->getElementsByTagName( "name_on_card" );
		$card_name = $card_names->item(0)->nodeValue;
		
		$cc_types = $au_record->getElementsByTagName( "card_type" );
		$cc_type = $cc_types->item(0)->nodeValue;
		
		$codes = $au_record->getElementsByTagName( "au_code" );
		$code = $codes->item(0)->nodeValue;
		
		$descriptions = $au_record->getElementsByTagName( "au_description" );
		$description = $descriptions->item(0)->nodeValue;
		
		$au_dates = $au_record->getElementsByTagName( "au_updated_date" );
		$au_date = $au_dates->item(0)->nodeValue;
		$date = date("Y-m-d", strtotime($au_date));                   //removes the timestamp
		
		$expire_yrs = $au_record->getElementsByTagName( "expire_year" );
		$expire_yr = $expire_yrs->item(0)->nodeValue;
		
		$expire_mos = $au_record->getElementsByTagName( "expire_month" );
		$expire_mo = $expire_mos->item(0)->nodeValue;
	
		//Write the rows
		$data = $merchant_id.','.$card_name.','.$cc_type.','.$masked_CC.','.$expire_mo.','.$expire_yr.','.$date.','.$code.','.$description.','.$pay_token.','.$cust_token . PHP_EOL;
		
		$sweet = fopen('files/AU.Report.csv', "a+");
		fwrite($sweet, $data);
	}
		$substring = "links";
		$resultcount = substr_count($response,$substring);
		if($resultcount < 1000){
			$i= -1;
		}
			else if($resultcount >= 1000){
				$i++;
			}
};

//Download the attachment to the user's hardrive
header('Content-Type: application/download');
header('Content-Disposition: attachment; filename="AU.Report.csv"');
header("Content-Length: " . filesize("files/AU.Report.csv"));

$fp = fopen("files/AU.Report.csv", "r");
fpassthru($fp);

$filename = 'AU.Report.csv';
?>