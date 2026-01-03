<?php

// This script builds the xml file. First it creates a csv with the fields we want, then converts the csv file to XML

// define the header row
$headers = 'MID,Name_on_Card,Card,Last_4,Mo,Year,Updated_On,AU_Code,AU_Description,Report_Created_On' . PHP_EOL;

// write the header row
$newfile = fopen('AU.working.csv',"a");
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
	$data = json_decode($response, true);
	
	// begin looping thru the GET call dataset
	for($i=0; $i < sizeof($data["results"]); $i++) {
		
		// define the fields
		$date = date("n/j/Y", strtotime($data["results"][$i]["card"]["au_updated_date"]));  //removes the timestamp from the date
		$merchant_id = str_replace("loc_","",($data["results"][$i]["location_id"]));        //removes loc_ from location id (aka merchant ID)
		$cc_type = ($data["results"][$i]["card"]["card_type"]);
		$card_name = ($data["results"][$i]["card"]["name_on_card"]);
		$masked_CC = ($data["results"][$i]["card"]["masked_account_number"]);
		$expire_mo = ($data["results"][$i]["card"]["expire_month"]);
		$expire_yr = ($data["results"][$i]["card"]["expire_year"]);
		$code = ($data["results"][$i]["card"]["au_code"]);
		$description = ($data["results"][$i]["card"]["au_description"]);
		
		// define the row
		$entries = $merchant_id.','.$card_name.','.$cc_type.','.$masked_CC.','.$expire_mo.','.$expire_yr.','.$date.','.$code.','.$description.','.$created_on . PHP_EOL;

		// write the row
		$sweet = fopen('AU.working.csv', "a+");
		fwrite($sweet, $entries);
		$rowCount++;
	}
	
	// if the once-per-record word "links" occurs less than 1000 times in the dataset, break out of the loop
	$substring = "links";
	$resultcount = substr_count($response,$substring);

	if($resultcount < 1000){
		$c= -1;
	}
		else if($resultcount >= 1000){
			$c++;
		}
};

// convert the csv to XML
$inputFilename = 'AU.working.csv';
$newfile = $filename;
$inputFile = fopen($inputFilename, 'rt');
$headers = fgetcsv($inputFile);

$doc  = new DomDocument();
$doc->formatOutput = true;
$root = $doc->createElement('rows');
$root = $doc->appendChild($root);

while (($row = fgetcsv($inputFile)) !== FALSE) {
	$container = $doc->createElement('row');
	foreach($headers as $i => $header) {
		$child = $doc->createElement($header);
		$child = $container->appendChild($child);
		$value = $doc->createTextNode($row[$i]);
		$value = $child->appendChild($value);
	}
	$root->appendChild($container);
}
$strxml = $doc->saveXML();
$handle = fopen($newfile, "w");
fwrite($handle, $strxml);
fclose($handle);

?>