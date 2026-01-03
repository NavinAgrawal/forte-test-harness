<?php
require_once __DIR__ . '/config/bootstrap.php';
ini_set('max_execution_time', 0);

//my sandbox
$base_url          = forte_base_url();
$organization_id   = forte_config('organization_id');
$location_id       = forte_config('location_id');
$api_access_id     = forte_config('api_access_id');
$api_secure_key    = forte_config('api_secure_key');
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url.'/organizations/'.$organization_id. '/locations/'.$location_id. '/transactions';
//$endpoint          = $base_url.'/organizations/'.$organization_id. '/applications/?filter=start_received_date+eq+%272020-05-01%27+and+end_received_date+eq+%272020-05-15%27&page_size=1000';
//$endpoint          = $base_url.'/organizations/'.$organization_id. '/locations/'.$location_id.'/transactions/?filter=paymethod_type+eq+card';
//$endpoint          = $base_url.'/organizations/'.$organization_id. '/locations/'.$location_id.'/schedules/?orderby=name_on_card+asc';
//$endpoint          = $base_url.'/organizations/org_xxxxx/applications/?page_size=1000';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/transactions/?filter=start_received_date+eq+%272018-06-01%27+and+end_received_date+eq+%272018-06-03%27&page_size=1000';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/customers/?filter=start_created_date+eq+%272018-06-27%27+AND+end_created_date+eq+%272018-06-28%27&page_size=1000&page_index=0';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/transactions/?filter=start_received_date+eq+%272021-02-11%27+AND+end_received_date+eq+%272021-02-20%27&entered_by=Scheduled';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/settlements/stl_a94a88ae-d2aa-40ce-ab5f-05249f26895d/?page_size=1000&page_index=0';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/settlements/?filter=start_settle_date+eq+%272018-06-01%27+AND+end_settle_date+eq+%272018-06-02%27&page_size=1000&page_index=0';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/transactions/?filter=status+eq+unfunded&page_size=1000';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/transactions/trn_xxxxx/settlements';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?filter=default_paymethod_type+eq+echeck';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?filter=paymethod_type+eq+card&orderby=name_on_card+desc';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?filter=paymethod_type+eq+echeck&orderby=account_holder+desc';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?filter=start_au_updated_date+eq+%272017-01-01%27+and+end_au_updated_date+eq+%272017-11-30%27&page_size=100&page_index=4';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?filter=["card"]["card_type"]+eq+visa&page_size=10000';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?filter=account_type+eq+%27checking%27';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/fundings/?filter=start_effective_date+eq+%272017-08-25%27+AND+end_effective_date+eq+%272017-08-26%27';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/scheduleitems/?page_size=1000&page_index=0&filter=start_schedule_item_processed_date+eq+%272017-08-30T00:00:00%27+and+end_schedule_item_processed_date+eq+%272017-08-30T23:59:59%27';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/transactions/?filter=start_received_date+eq+%272018-05-15%27+AND+end_received_date+eq+%272018-05-16%27';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/settlements/?filter=start_settle_date+eq+%272019-09-20T06:35:00%27+AND+end_settle_date+eq+%272019-09-23T12:00:01%27';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/settlements/?filter=start_settle_date+eq+%272018-07-26%27+AND+end_settle_date+eq+%272018-08-01%27';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/settlements/?filter=start_settle_date+eq+%272017-05-30T06:35:00%27+AND+end_settle_date+eq+%272017-05-31T12:00:01%27';
//$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?filter=start_au_updated_date+eq+%272019-03-01%27+and+end_au_updated_date+eq+%272019-04-18%27&page_size=1000&orderby=name_on_card+asc';

//Address Info
$address = array (
	'first_name' => 'Fred',
	'last_name' => 'Jones',
	'email' => 'integration@forte.net',
	'physical_address' => array (
		'street_line1' => '11212 Myrtice Dr',
		'locality' => 'Dallas',
		'region' => 'TX',
		'postal_code' => '75013'
	)
);

//eCheck Info
/* $echeck = array (
	'account_holder' => 'James G Ivey',
	'routing_number' => '111000614',    //Canadian 000257596
	'account_number' => '12234556',
	'account_type' => 'checking',
	'sec_code' => 'CCD', */
	//'one_time_token' => 'ott_ej9aKME7QuSmd9vM7X03eQ'
// );

//Credit Card Info
$card = array (
	//'card_type' => 'amex',
	//'name_on_card' => 'Jimmy D Ivey',
	//'account_number' => '377936419194774',
	//'expire_month' => '08',
	//'expire_year' => '2024',
	//'card_verification_value' => '403',
	'one_time_token' => 'ott_Fwp1fZvIRhGazGYmojOLiA'
);

$xdata = array (
	'xdata_1' => 'Has a big dog. Be sure the owner is present.',
	'xdata_2' => 'this is xdata_2',
	'xdata_3' => 'and here you have xdata_3'
);

$line_items = array (
	'line_item_header' => 'this,that,other',
	'line_item_1' => 'red,green,blue',
	'line_item_2' => 'yellow,purple,black'
);

$params = array (
	'action' => 'sale',                      //sale, authorize, credit, void, capture, inquiry, verify, force, reverse
	'card' => $card,
	'billing_address' => $address,
	'authorization_amount' => 0.08,
	//'line_items' => $line_items,
	//'xdata' => $xdata,
	'order_number' => '1056-AABd',
	//'reference_id' => '11223344',
	//'customer_id' => '123456789012345678901234567890',
	//'customer_token' => '',
	//'paymethod_token' => 'mth_xxxxx'
	//'original_transaction_id' => '',
	//'transaction_id' => '',
	//'authorization_code' => '',
	//'subtotal_amount' => 1,
	//'service_fee_amount' => '.26'
	//'save_token' => 'customer'                       //"customer" saves both, "paymethod" saves only the paymethod
);

//$params = $_POST;

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_HTTPHEADER, array (
	'Authorization: Basic ' . $auth_token,
	'X-Forte-Auth-Organization-Id: ' . $organization_id,
	'Accept: application/json',
	'Content-type: application/json'
));

$response = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);
$data = json_decode($response);
$pretty = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

echo '<pre>';
print_r('HttpStatusCode: ' . $info['http_code'] . '<br><br>');
print_r($data);
//var_dump($response);
echo '</pre>';
?>