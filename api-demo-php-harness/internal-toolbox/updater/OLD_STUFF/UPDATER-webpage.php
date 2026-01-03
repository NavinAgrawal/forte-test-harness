<?php

$newfile = fopen('AU.Report.html',"w");
ob_start();

echo "<!DOCTYPE html>";
echo "<html>";
echo "<style type='text/css'>body {top-margin:30px; margin-bottom:50px;} table, td, th {padding:3px 8px 3px 8px; font-family:'Calibri'; font-size:13pt;} tr:nth-of-type(odd) {background-color:#E2E2E2;} th {font-size:13pt; font-family:'Calibri'; background-color:#00275E; color:white;}</style>";
echo "<body topmargin='30'>";
echo "<div align=center><font face='Cambria' style='font-size: 18pt'><b>Account Updater Report</b></font><br>";
echo "<font face='Calibri' style='font-size: 13pt'>Cards Updated " . $start_date . ' to ' . $end_date . "<br><br></font></div>";
echo "<center>";
echo "<table border='1' style='border-collapse: collapse' bordercolor='#C8C8C8'>";
echo "<tr><th>#</th><th>Merchant ID</th><th>Cardholder Name</th><th>Card Type</th><th>Last 4</th><th>Month</th><th>Year</th><th>Date Updated</th><th>Code</th><th>Description</th></tr></center>";

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
		echo '<tr>';
		echo '<td align=center>', ($row), '</td>';
		echo '<td align=center>', ($data["results"][$i]["location_id"]), '</td>';
		echo '<td>', ($data["results"][$i]["card"]["name_on_card"]), '</td>';
		echo '<td align=center>', ($data["results"][$i]["card"]["card_type"]), '</td>';	
		echo '<td align=center>', ($data["results"][$i]["card"]["masked_account_number"]), '</td>';
		echo '<td align=center>', ($data["results"][$i]["card"]["expire_month"]), '</td>';
		echo '<td align=center>', ($data["results"][$i]["card"]["expire_year"]), '</td>';
		echo '<td align=left style="text-align:left">', ($data["results"][$i]["card"]["au_updated_date"]), '</td>';
		echo '<td align=center>', ($data["results"][$i]["card"]["au_code"]), '</td>';
		echo '<td align=left style="text-align:left">', ($data["results"][$i]["card"]["au_description"]), '</td>';
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

echo '</table>';
echo '</html>';

$data = ob_get_clean();
fwrite($newfile,$data);
header("Location: AU.Report.html");
header('Content-Type: text/html');

//Download the attachment to your hardrive
header('Content-Type: text/html');
header('Content-Disposition: attachment; filename="AU.Report.html"');
header("Content-Length: " . filesize("AU.Report.html"));

$fp = fopen("AU.Report.html", "r");
fpassthru($fp);
$filename = 'AU.Report.html';

?>