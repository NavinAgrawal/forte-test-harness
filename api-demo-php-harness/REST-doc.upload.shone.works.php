<?php
require_once __DIR__ . '/config/bootstrap.php';
	$url = forte_base_url();
	$OrganizationID  = forte_config('organization_id');
	$LocationID = forte_config('location_id');
	$APIKey = forte_config('api_login_id');
	$SecureTransactionKey = forte_config('secure_transaction_key');
	$auth_token = base64_encode($APIKey.':'.$SecureTransactionKey);	
	$service_url = $url.'/organizations/'.$OrganizationID.'/documents/';
	
	$curl = curl_init($service_url);
	
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
   	curl_setopt($curl, CURLOPT_NOBODY, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_FAILONERROR, false);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'Authorization: Basic '.$auth_token,
		'X-Forte-Auth-Organization-Id: '.$OrganizationID,
		'Accept:application/json',
		'Content-Type: multipart/form-data; boundary=boundaryString',
		));	
	
	$resource = 'dispute';
	$resource_id = 'dsp_1048311494';
	$boundary_string='boundaryString';
	$file_name = 'testing.pdf';
	$file_content = file_get_contents($file_name);	
	$contentType='application/pdf';
	$data = "--".$boundary_string."\r\nContent-Disposition: form-data; name=\"myJsonString\"\r\nContent-Type: application/json\r\n\r\n{\r\n\"resource\":\"application\",\r\n\"resource_id\":\"$resource_id\",\r\n\"description\":\"example description\"\r\n}\r\n--".$boundary_string."\r\nContent-Disposition: form-data; name=\"file\"; filename=\"$file_name\"\r\nContent-Type: $contentType\r\n\r\n$file_content\r\n--".$boundary_string."--\r\n";
	
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($curl, CURLOPT_POST, true);
	$curl_response = curl_exec($curl);
	$info = curl_getinfo($curl);
	echo '<pre>';
	print_r('HttpStatus Code: ' . $info['http_code'] . '<br>');
	if($curl_response === false) {
	    print_r('Curl error: ' . curl_error($curl) . '<br>');
	}
	curl_close($curl);
	$pretty = json_decode($curl_response);
	echo '<pre>';
	print_r($pretty);
?>
