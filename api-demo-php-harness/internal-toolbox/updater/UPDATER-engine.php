<?php
require_once __DIR__ . '/../../config/bootstrap.php';

// capture the data posted in the form
// set some variables
// call the script per the selected format
// download the report
// email the report
// clean-up.

date_default_timezone_set('America/Chicago');

$start_date        = $_POST['start_date'];
$end_date          = $_POST['end_date'];
$merchant_id       = $_POST['location_id'];
//$sendto            = $_POST['info@forte.net'];
//$email_note        = $_POST['here is a note'];
//$company_name      = $_POST['company'];
    $api_access_id = forte_post_value('api_access_id', 'api_access_id');
    $api_secure_key = forte_post_value('api_secure_key', 'api_secure_key');
    $organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
    $location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
$created_on        = date("Y-m-d g:i a T");
$filename_s_date   = date("M.j.Y",strtotime($start_date));
$filename_e_date   = date("M.j.Y",strtotime($end_date));
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/paymethods/?filter=start_au_updated_date+eq+'.$start_date.'+and+end_au_updated_date+eq+'.$end_date.'&page_size=1000&orderby=name_on_card+asc';


// if CSV button, build the csv file
if(isset($_POST['csv'])) {
	$filename = 'AU.Report--mid.'.$merchant_id.'--'.$filename_s_date.'-'.$filename_e_date.'.csv';
	@include('UPDATER-csv.php');
}
// if XML button, build the XML file
if(isset($_POST['xml'])) {
	$filename = 'AU.Report--mid.'.$merchant_id.'--'.$filename_s_date.'-'.$filename_e_date.'.xml';
	@include('UPDATER-xml.php');
}
// if PDF button, build the PDF file
if(isset($_POST['pdf'])) {
	$filename = 'AU.Report--mid.'.$merchant_id.'--'.$filename_s_date.'-'.$filename_e_date.'.pdf';
	@include "UPDATER-html.php";      // first build an html file, then convert the html to a pdf
	@include('UPDATER-pdf.php');
}

// download it to the user's hardrive
header("Content-Type: application/download");
header("Content-Disposition: attachment; filename=".$filename);
header("Content-Length: " . filesize($filename));
$fp = fopen($filename, "r");
fpassthru($fp);

// email it
//@include('UPDATER-email.php');

// delete the leftovers
//$leftovers = 'AU*';
//array_map("unlink", glob($leftovers));
?>