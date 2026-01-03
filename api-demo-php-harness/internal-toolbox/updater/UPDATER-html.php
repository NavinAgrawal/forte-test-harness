<?php

// for the PDF, first we build an html file, (this script), then make an API call
// to "HTML 2 PDF Rocket" to convert the html to a PDF file (UPDATER-pdf.php)

// open the file
$html = fopen('AU.working.html',"w");
ob_start();

// write the opening html tags
echo "<!DOCTYPE html>";
echo "<html>";

// write the css
echo "
<style type='text/css'> 
	body {
	margin-top:30px; 
	} 
	table, td, th {
	padding:2px 6px 2px 6px; 
	font-family:'Calibri'; 
	font-size:13pt;
	} 
	tr:nth-of-type(odd) {
	background-color:#E2E2E2;
	} 
	th {
	font-size:13pt; 
	font-family:'Calibri'; 
	background-color:#00275E; 
	color:white;
	}
</style>";

// clean up the date for the report heading
$heading_s_date = date("F j, Y",strtotime($start_date));
$heading_e_date = date("F j, Y",strtotime($end_date));

// begin the body, write the heading	
echo "<body topmargin='30'>";
echo "<div align=center><font face='Cambria' style='font-size: 18pt'><b>Account Updater Report</b></font><br>";
echo "<font face='Calibri' style='font-size: 13pt'>" . $heading_s_date . ' - ' . $heading_e_date . "</font></div>";
echo "<br>";
echo "<center>";
echo "<table border='1' style='border-collapse: collapse' bordercolor='#C8C8C8'>";

// write the header row
echo "	
<thead>
	<tr>
	  <th>#</th>
	  <th>MID</th>
	  <th>Cardholder Name</th>
	  <th>Card</th>
	  <th>Last 4</th>
	  <th>Mo</th>
	  <th>Year</th>
	  <th>Updated</th>
	  <th>AU Code</th>
	  <th>AU Description</th>
	</tr>
</thead>";

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
	$data = json_decode($response,true);
	
	// a humble attempt at error-handling
	$xml = simplexml_load_string($response);
	if (strpos($response, 'Error') !== false) {
	echo (string)$xml->response->response_desc;
	die;
	}
	
	// begin looping thru the data
	$row = $rowCount;
	for($i=0; $i < sizeof($data["results"]); $i++) 
	{
		$date = date("n/j/Y", strtotime($data["results"][$i]["card"]["au_updated_date"]));  //removes the timestamp from the date
		$pay_token = str_replace("mth_","",($data["results"][$i]["paymethod_token"]));      //removes mth_ from token
		$cust_token = str_replace("cst_","",($data["results"][$i]["customer_token"]));      //removes cst_ from token
		$merchant_id = str_replace("loc_","",($data["results"][$i]["location_id"]));        //removes loc_ from location id

		if(!empty($expire_mo)) {
			$new_mo = str_pad($expire_mo, 2, '0', STR_PAD_LEFT);
		}
			else {
				$new_mo = NULL;
			}
			
		// write the rows plucking the fields we want
		echo '<tr>';
		echo '<td align="center">', ($row + 1), '</td>';
		echo '<td align="center">', $merchant_id, '</td>';
		echo '<td>', ($data["results"][$i]["card"]["name_on_card"]), '</td>';
		echo '<td align="center">', ($data["results"][$i]["card"]["card_type"]), '</td>';	
		echo '<td align="center">', ($data["results"][$i]["card"]["masked_account_number"]), '</td>';
		echo '<td align="center">', str_pad(($data["results"][$i]["card"]["expire_month"]), 2, '0', STR_PAD_LEFT), '</td>';
		echo '<td align="center">', ($data["results"][$i]["card"]["expire_year"]), '</td>';
		echo '<td align="center">', $date, '</td>';
		echo '<td align="center">', ($data["results"][$i]["card"]["au_code"]), '</td>';
		echo '<td>', ($data["results"][$i]["card"]["au_description"]), '</td>';
		echo '</tr>';
		$row++;
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

// write the html closing tags
echo '</table>';
echo '</center>';
echo '</body>';
echo '</html>';

// and write the file to the server
$data = ob_get_clean();
fwrite($html,$data);
?>