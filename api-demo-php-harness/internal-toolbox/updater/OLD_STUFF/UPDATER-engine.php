<?php
require_once __DIR__ . '/../../../config/bootstrap.php';

//capture form data, set some variables, set the endpoint for the REST call

date_default_timezone_set('America/Chicago');
    $api_access_id = forte_post_value('api_access_id', 'api_access_id');
    $api_secure_key = forte_post_value('api_secure_key', 'api_secure_key');
$start_date        = $_POST['s_year'].'-'.$_POST['s_month'].'-'.$_POST['s_day'];
$end_date          = $_POST['e_year'].'-'.$_POST['e_month'].'-'.$_POST['e_day'];
$merchant_id       = $_POST['location_id'];
$created_on        = date("Y-m-d g:i a T");
$filename_s_date   = date("M.j.Y",strtotime($start_date));
$filename_e_date   = date("M.j.Y",strtotime($end_date));
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
    $organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
    $location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?filter=start_au_updated_date+eq+'.$start_date.'+and+end_au_updated_date+eq+'.$end_date.'&page_size=1000&orderby=name_on_card+asc';



//if PDF button, do these things
if(isset($_POST['pdf'])) {
	$filename = 'AU.Report--mid.'.$merchant_id.'--'.$filename_s_date.'-'.$filename_e_date.'.pdf';
	@include('UPDATER-pdf.php');
}
//if CSV button, do these things
if(isset($_POST['csv'])) {
	$filename = 'AU.Report--mid.'.$merchant_id.'--'.$filename_s_date.'-'.$filename_e_date.'.csv';
	@include('UPDATER-csv.php');
}
//if XML button, do these things
if(isset($_POST['xml'])) {
	$filename = 'AU.Report--mid.'.$merchant_id.'--'.$filename_s_date.'-'.$filename_e_date.'.xml';
	@include('UPDATER-xml.php');
}
?>