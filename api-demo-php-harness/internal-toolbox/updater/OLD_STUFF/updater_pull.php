<?php


require_once __DIR__ . '/../../../config/bootstrap.php';
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
//$start_date        = $_POST['start_date'];
//$end_date          = $_POST['end_date'];
$start_date        = '2018-01-01';
$end_date          = '2018-01-31';
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url . '/organizations/' . $organization_id . '/locations/' . $location_id . '/paymethods/?filter=start_au_updated_date+eq+' . $start_date . '+and+end_au_updated_date+eq+' . $end_date . '&page_size=1000';

echo "<!DOCTYPE html>";
echo "<html>";
echo "<style type='text/css'>body {top-margin:50px; margin-bottom:50px;} table, td, th {padding:3px 8px 3px 8px; font-family:'Trebuchet MS'; font-size:14pt;} th {font-size:16pt; font-family:Trebuchet MS;}</style>";
echo "<body topmargin='50'>";
echo "<div align=center><font face='Cambria' style='font-size: 20pt'><b>Account Updater Report</b></font><br>";
echo "<font face='Trebuchet MS' style='font-size: 16pt'>Cards Updated " . $start_date . ' to ' . $end_date . "<br><br></font></div>";
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
	$info = curl_getinfo($ch);	
	curl_close($ch);
	$data = json_decode($response,true);
	$row = $rowCount;
	//for($i=0; $i < sizeof($data["results"]); $i++)
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

?>