<?php

require_once __DIR__ . '/../../../config/bootstrap.php';
//$organization_id   = forte_config('organization_id');
//$location_id       = forte_config('location_id');
//$api_access_id     = forte_config('api_access_id');
//$api_secure_key    = forte_config('api_secure_key');
    $organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
    $location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
    $api_access_id = forte_post_value('api_access_id', 'api_access_id');
    $api_secure_key = forte_post_value('api_secure_key', 'api_secure_key');

//capture the data posted from the form
$start_date        = $_POST['s_year'].'-'.$_POST['s_month'].'-'.$_POST['s_day'];
$end_date          = $_POST['e_year'].'-'.$_POST['e_month'].'-'.$_POST['e_day'];
$base_url          = forte_base_url();
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url . '/organizations/' . $organization_id . '/locations/' . $location_id . '/paymethods/?filter=start_au_updated_date+eq+' . $start_date . '+and+end_au_updated_date+eq+' . $end_date . '&page_size=1000';

//if PDF selected
if(isset($_POST['pdf'])) {
	@include('UPDATER-pdf.download.php');
}
	
//if CSV selected	
if(isset($_POST['csv'])) {
	@include('UPDATER-csv.download.php');
}
	
//if XML selected
if(isset($_POST['xml'])) {
	@include('UPDATER-xml.download.php');
}
?>
