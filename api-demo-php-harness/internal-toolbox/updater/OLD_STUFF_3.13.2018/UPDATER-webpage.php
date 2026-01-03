<?php

$newfile = fopen('files/AU.Report.html',"w");
ob_start();

$heading_s_date = date("F j, Y",strtotime($start_date));
$heading_e_date = date("F j, Y",strtotime($end_date));

//write the opening html tags as well as the header row
echo "<!DOCTYPE html>";
echo "<html>";
echo "<style type='text/css'>body {top-margin:30px; margin-bottom:50px;} table, td, th {padding:2px 6px 2px 6px; font-family:'Calibri'; font-size:13pt;} tr:nth-of-type(odd) {background-color:#E2E2E2;} th {font-size:13pt; font-family:'Calibri'; background-color:#00275E; color:white;}</style>";
echo "<body topmargin='30'>";
echo "<div align=center><font face='Cambria' style='font-size: 18pt'><b>Account Updater Report</b></font><br>";
echo "<font face='Calibri' style='font-size: 13pt'>" . $heading_s_date . ' - ' . $heading_e_date . "<br><br></font>";
echo "<center>";
echo "<table border='1' style='border-collapse: collapse' bordercolor='#C8C8C8'>";
echo "<thead><tr><th>#</th><th>MID</th><th>Cardholder Name</th><th>Card</th><th>Last 4</th><th>Mo</th><th>Year</th><th>Updated</th><th>AU Code</th><th>AU Description</th><th>Paymethod Token</th><th>Customer Token</th></tr></thead></center>";

//loop through the data
$c = 0;
$rowCount = 0;
while($c >= 0){	
	$ch = curl_init($endpoint . '&page_index='.$c);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Basic ' . $auth_token,
		'X-Forte-Auth-Organization-id: ' . $organization_id,
		'Accept:application/json',
		'Content-type: application/xml'
	));
	
	$response = curl_exec($ch);
	curl_close($ch);
	
	$xml = simplexml_load_string($response);

	if (strpos($response, 'Error') !== false) {
	echo (string)$xml->response->response_desc;
	die;
	}
	
	$data = json_decode($response,true);
	$row = $rowCount;
	for($i=0; $i < sizeof($data["results"]); $i++)		
	{
		$date = date("n/j/Y", strtotime($data["results"][$i]["card"]["au_updated_date"]));  //removes the timestamp from the date
		$pay_token = str_replace("mth_","",($data["results"][$i]["paymethod_token"]));  //removes mth_ from token
		$cust_token = str_replace("cst_","",($data["results"][$i]["customer_token"]));   //removes cst_ from token
		$merchant_id = str_replace("loc_","",($data["results"][$i]["location_id"]));        //removes loc_ from merchant id
		
		//write the rows
		echo '<tr>';
		echo '<td align=center>', ($row+1), '</td>';
		echo '<td align=center>', $merchant_id, '</td>';
		echo '<td>', ($data["results"][$i]["card"]["name_on_card"]), '</td>';
		echo '<td align=center>', ($data["results"][$i]["card"]["card_type"]), '</td>';	
		echo '<td align=center>', ($data["results"][$i]["card"]["masked_account_number"]), '</td>';
		echo '<td align=center>', ($data["results"][$i]["card"]["expire_month"]), '</td>';
		echo '<td align=center>', ($data["results"][$i]["card"]["expire_year"]), '</td>';
		echo '<td align=center>', $date, '</td>';
		echo '<td align=center>', ($data["results"][$i]["card"]["au_code"]), '</td>';
		echo '<td align=left style="text-align:left">', ($data["results"][$i]["card"]["au_description"]), '</td>';
		echo '<td align=left>', $pay_token, '</td>';
		echo '<td align=left>', $cust_token, '</td>';
		echo '</tr>';
		$row++;
	}
	$rowCount = $row; 
	
		$substring = "links";
		$resultcount = substr_count($response,$substring);
		if($resultcount < 1000){
			$c= -1;
		}
			else if($resultcount >= 1000){
				$c++;
			}
};

//write html closing tags
echo '</table>';
echo '</html>';

$data = ob_get_clean();
fwrite($newfile,$data);
//header("Location: files/AU.Report.html");

//Download the attachment to your hardrive
//header('Content-Type: text/html');
//header('Content-Disposition: attachment; filename="AU.Report.html"');
//header("Content-Length: " . filesize("files/AU.Report.html"));

//$fp = fopen("files/AU.Report.html", "r");
//fpassthru($fp);

//$filename = 'AU.Report.html';

?>