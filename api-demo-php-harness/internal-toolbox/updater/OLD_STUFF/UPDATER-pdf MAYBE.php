	<?php

	require_once __DIR__ . '/../../../config/bootstrap.php';

	//Set variables and create a file for the report html
	$start_date = $_POST['start_date'];
	$end_date   = $_POST['end_date'];	
	$myfile = fopen('AU.Report.html',"w");

	//Turn on output buffering and run the script that builds the html report
	ob_start();
	include "UPDATER-html.report.php";
	$data = ob_get_clean();
	fwrite($myfile,$data);
	fclose($myfile);

	//Make an API call to the Rocket PDF service
	$apikey = forte_config('html2pdf_api_key');
	$value = 'http://www.calligraphydallas.com/stuff/AU.Report.html';
	$params = array(
		'apikey' => $apikey,
		'value' => $value,
		'MarginTop' => '10',
		'MarginBottom' => '10',
		'MarginRight' => '5',
		'MarginLeft' => '5',
		'UseLandscape' => 'true',
		'FooterUrl' => 'http://www.calligraphydallas.com/stuff/pdf-footer2.htm',
	);
	$result = file_get_contents('http://api.html2pdfrocket.com/pdf?' . http_build_query($params));
	file_put_contents('AU.Report.pdf', $result); 

	//Download the pdf attachment
	header('Content-Description: File Transfer');
	header('Content-Type: application/pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . strlen($result));
	header('Content-Disposition: attachment; filename=' . 'AU.Report.pdf' );	
	echo $result;

	$newfile = 'AU.Report.pdf';
	exit;
	?>
