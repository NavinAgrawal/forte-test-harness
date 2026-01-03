<?php
require_once __DIR__ . '/../../../config/bootstrap.php';
//this script sets some global variables and defines what all the buttons do

$base_url = (isset($_POST['base_url']) && $_POST['base_url'] !== '') ? $_POST['base_url'] : forte_base_url();
$organization_id = forte_prefixed_post('organization_id', 'org_', 'organization_id');
$location_id = forte_prefixed_post('location_id', 'loc_', 'location_id');
$api_access_id = forte_post_value('api_access_id', 'api_access_id');
$api_secure_key = forte_post_value('api_secure_key', 'api_secure_key');

$merchant_id = str_replace("loc_","",$location_id);
$auth_token  = base64_encode($api_access_id . ':' . $api_secure_key);
$base_query  = 'auth_token=' . urlencode($auth_token) .
	'&organization_id=' . urlencode($organization_id) .
	'&base_url=' . urlencode($base_url) .
	'&location_id=' . urlencode($location_id);
$filename    = 'CUSTOMER.DATA--MID.'.$merchant_id.'--'.date("Y.m.d").'.csv';
$endpoint    = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000';
$environment = forte_env_name();

if (strtolower($base_url) == strtolower(forte_config('base_url_production', 'https://api.forte.net/v3'))) {
	$environment = 'production';
}
if (strtolower($base_url) == strtolower(forte_config('base_url_sandbox', 'https://sandbox.forte.net/api/v3'))) {
	$environment = 'sandbox';
}

// if REST creds are NULL
if ($organization_id == NULL or $location_id == NULL or $api_access_id == NULL or $api_secure_key == NULL) {
	$message = "Dood. You\'re missing a credential or two.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();}else {window.history.back();}</script>";
	exit;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////   THE BUTTONS   //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////

// if "Check Contents" button
if(isset($_POST['inventory'])) {
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers';
	@include "IMPORT-count.everything.php";
}

// if "Delete Leftovers" button
if(isset($_POST['leftovers'])) {
	$message = "You are about to delete all the files that are leftover from the previous import.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-delete.leftovers.php';
		}
		else {
			window.history.back();
		}
	</script>";
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////   IMPORT CC BUTTONS   //////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


// if "Import CC Data" button
if(isset($_POST['import_CC'])) {
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
	
	if(!file_exists('../data.CC.csv')){
		$message = "The file data.CC.csv does not exist.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
	}
	//get the number of tokens in undo.import.csv file (without loading it into memory)
	$file = new SplFileObject('../data.CC.csv', 'r');
	$file->seek(PHP_INT_MAX);
	$number_records = $file->key();
	
	$message = "You are about to import $number_records credit card customers into $environment mid $merchant_id.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Importing.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-import.data.CC.php?endpoint=".$endpoint."&".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}

// if "Import CC Failures" button
if(isset($_POST['errors_CC'])) {
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';

	$delete_set = "../failure.CC.data.csv";
	if (!file_exists($delete_set)) {
		$message = "There is no CC failure data to import.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
		exit;
	}
	
	//get the number of tokens in undo.import.csv file (without loading it into memory)
	$file = new SplFileObject('../failure.CC.data.csv', 'r');
	$file->seek(PHP_INT_MAX);
	$number_records = $file->key();
	
	$message = "You are about to import $number_records credit card failures into $environment mid $merchant_id.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Importing.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-import.failures.CC.php?endpoint=".$endpoint."&".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}


///////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////   IMPORT CC SCHEDULES BUTTONS   /////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


// if "Import CC Schedules" button
if(isset($_POST['CCschedules'])) {
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
	
	if(!file_exists('../data.CC.csv')){
		$message = "The file data.CC.csv does not exist.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
	}
	//get the number of tokens in undo.import.csv file (without loading it into memory)
	$file = new SplFileObject('../data.CC.csv', 'r');
	$file->seek(PHP_INT_MAX);
	$number_records = $file->key();
	
	$message = "You are about to import $number_records credit card customers into $environment mid $merchant_id.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Importing.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-import.CC.schedules.php?endpoint=".$endpoint."&".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}


// if "Import ACH Schedules" button
if(isset($_POST['ACHschedules'])) {
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
	
	if(!file_exists('../data.ACH.csv')){
		$message = "The file data.ACH.csv does not exist.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
	}
	//get the number of tokens in undo.import.csv file (without loading it into memory)
	$file = new SplFileObject('../data.ACH.csv', 'r');
	$file->seek(PHP_INT_MAX);
	$number_records = $file->key();
	
	$message = "You are about to import $number_records ACH customers into $environment mid $merchant_id.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Importing.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-import.ACH.schedules.php?endpoint=".$endpoint."&".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}



///////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////   IMPORT ACH BUTTONS   /////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


// if "Import ACH Data" button
if(isset($_POST['import_ACH'])) {
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
	
	if(!file_exists('../data.ACH.csv')){
		$message = "The file data.ACH.csv does not exist.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
	}
	
	//get the number of tokens in undo.import.csv file (without loading it into memory)
	$file = new SplFileObject('../data.ACH.csv', 'r');
	$file->seek(PHP_INT_MAX);
	$number_records = $file->key();
	
	$message = "You are about to import $number_records ACH customers into $environment mid $merchant_id.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Importing.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-import.data.ACH.php?endpoint=".$endpoint."&".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}

// if "Import ACH Failures" button
if(isset($_POST['errors_ACH'])) {
	
	//if failure file does not exist, alert message
	if (!file_exists("../failure.ACH.data.csv")) {
		$message = "There is no ACH failure data to import.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
		exit;
	}
	//get the number of tokens in undo.import.csv file (without loading it into memory)
	$file = new SplFileObject('../failure.ACH.data.csv', 'r');
	$file->seek(PHP_INT_MAX);
	$number_records = $file->key();
	
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
	$message = "You are about to import $number_records ACH failures into $environment mid $merchant_id.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Importing.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-import.failures.ACH.php?endpoint=".$endpoint."&".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////   EXPORT CUSTOMERS, SCHEDULES OR PAYMETHODS   /////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


// if "Export Customers" button
if(isset($_POST['export'])) {
	$message = "You are about to export all the customers from $environment mid $merchant_id.\\n\\nClick Ok if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Exporting customers.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-export.customers.php?".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}

// if "Export Schedules" button
if(isset($_POST['schedules'])) {
	$message = "You are about to export all the schedules from $environment mid $merchant_id.\\n\\nClick Ok if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Exporting schedules.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-export.schedules.php?".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}

// if "Export Paymethods" button
if(isset($_POST['paymethods'])) {
	$message = "You are about to export all the paymethods from $environment mid $merchant_id.\\n\\nClick Ok if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Exporting paymethods.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-export.paymethods.php?".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}

///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////   NUKE THE MID and UNDO THE IMPORT   //////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


// if "NUKE THE MID" button
if(isset($_POST['delete_everything'])) {
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/?page_size=1000';
	$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/schedules/';
	$message2 = "Are you sure?\\n\\nYou are about to delete everything in $environment mid $merchant_id - All customers, all paymethods, and all schedules.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Deleting.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message2') == true) {
			window.location.href='IMPORT-nuke.the.mid.php?endpoint=".$endpoint."&endpoint2=".$endpoint2."&".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}

// if "Undo The Import CC" button
if(isset($_POST['undo_import_CC'])) {
	$delete_set = "../undo.import.CC.csv";
	if (!file_exists($delete_set)) {
		$message = "There is no token file to undo.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
		exit;
	}
	//get the number of records in undo.import.csv file (without loading it into memory)
	$file = new SplFileObject('../undo.import.CC.csv', 'r');
	$file->seek(PHP_INT_MAX);
	$number_tokens = $file->key();
	
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000';
	$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
	$message = "Are you sure?";
	$message2 = "Are you sure?\\n\\nYou are about to delete $number_tokens CC customers in $environment mid $merchant_id.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Deleting.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message2') == true) {
			window.location.href='IMPORT-undo.the.import.CC.php?endpoint=".$endpoint."&endpoint2=".$endpoint2."&".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}

// if "Undo The Import ACH" button
if(isset($_POST['undo_import_ACH'])) {
	$delete_set = "../undo.import.ACH.csv";
	if (!file_exists($delete_set)) {
		$message = "There is no token file to undo.";
		echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
		exit;
	}
	//get the number of tokens in undo.import.csv file (without loading it into memory)
	$file = new SplFileObject('../undo.import.ACH.csv', 'r');
	$file->seek(PHP_INT_MAX);
	$number_tokens = $file->key();
	
	$endpoint = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/?page_size=1000';
	$endpoint2 = $base_url.'/organizations/'.$organization_id.'/locations/'.$location_id.'/customers/';
	$message = "Are you sure?";
	$message2 = "Are you sure?\\n\\nYou are about to delete $number_tokens ACH customers in $environment mid $merchant_id.\\n\\nClick OK if that is what you want, or Cancel to reconsider.";
	echo "<p align='center'><br><br><br><br><img border='0' src='149.gif' width='128' height='128'><br><br><font style='font-family:Calibri; font-size:26px;'>Toolbox 1 says:<br>Deleting.... Please wait.</p>";
	echo "<script type='text/javascript'>
		if(confirm('$message2') == true) {
			window.location.href='IMPORT-undo.the.import.ACH.php?endpoint=".$endpoint."&endpoint2=".$endpoint2."&".$base_query."';
		}
		else {
			window.history.back();
		}
	</script>";
}

// nuke all the csv files in the /importer/ folder including tokens and undo import file
if(isset($_POST['nuke_the_files'])) {
	$message = "This is going to delete ALL csv files in the importer folder, including\\nthe tokens files and the Undo Import files from the previous import.";
	echo "<script type='text/javascript'>
		if(confirm('$message') == true) {
			window.location.href='IMPORT-nuke.all.csv.files.php';
		}
		else {
			window.history.back();
		}
	</script>";	
}
?>
