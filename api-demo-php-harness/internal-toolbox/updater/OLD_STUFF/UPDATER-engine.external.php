<?php

require_once __DIR__ . '/../../../config/bootstrap.php';
@include('UPDATER-footer.php');

$start_date        = $_POST['s_year'].'-'.$_POST['s_month'].'-'.$_POST['s_day'];
$end_date          = $_POST['e_year'].'-'.$_POST['e_month'].'-'.$_POST['e_day'];
$sendto            = $_POST['sendto_email'];
$merchant_name     = $_POST['merchant_name'];
$base_url          = forte_base_url();
$auth_token        = base64_encode($api_access_id . ':' . $api_secure_key);
$endpoint          = $base_url . '/organizations/' . $organization_id . '/locations/' . $location_id . '/paymethods/?filter=start_au_updated_date+eq+' . $start_date . '+and+end_au_updated_date+eq+' . $end_date . '&page_size=1000';


if(isset($_POST['pdf'])) {	
	@include('UPDATER-pdf.php');
	@include('UPDATER-email.php');
}
	elseif(isset($_POST['xml'])) {
		@include('UPDATER-xml.php');
		@include('UPDATER-email.php');
	}
	elseif(isset($_POST['csv'])) {
		@include('UPDATER-csv.php');
		@include('UPDATER-email.php');
	}
	elseif(isset($_POST['webpage'])) {
		@include('UPDATER-webpage.php');
		@include('UPDATER-email.php');
	}
?>