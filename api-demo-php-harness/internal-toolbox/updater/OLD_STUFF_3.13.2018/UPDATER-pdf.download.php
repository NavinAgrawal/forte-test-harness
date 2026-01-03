<?php

require_once __DIR__ . '/../../../config/bootstrap.php';

// PDF Conversion service provided by "HTML 2 PDF Rocket" https://www.html2pdfrocket.com

//first we create the html file, then make an API call to HTML2PDF Rocket and his service
//converts the html to a PDF file. 200/month for free, more than that is monthly charge.

//create the html file
$myfile = fopen('files/AU.Report.html',"w");
ob_start();
include "UPDATER-webpage.php";
$data = ob_get_clean();
fwrite($myfile,$data);
//fclose($myfile);

//API key for the service and URL to the webpage we're going to convert
$apikey = forte_config('html2pdf_api_key');
$value = 'http://www.calligraphydallas.com/updater/files/AU.Report.html';
 
//set PDF file parameters, including URL to the footer (timestamp and page numbers)
$postdata = http_build_query(
    array(
        'apikey' => $apikey,
        'value' => $value,
        'MarginBottom' => '10',
        'MarginTop' => '10',
		'MarginLeft' => '10',
		'MarginRight' => '10',
		'UseLandscape' => 'true',
        'FooterURL' => 'http://www.calligraphydallas.com/updater/UPDATER-pdf.footer.php'
    )
);
 
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

//make the API call
$context  = stream_context_create($opts);
$result = file_get_contents('http://api.html2pdfrocket.com/pdf', false, $context);

//save the PDF to disk and display it in the browser window
file_put_contents('files/AU.Report.pdf', $result);

//Download the attachment to the user's hardrive
header('Content-Type: application/download');
header('Content-Disposition: attachment; filename="AU.Report.pdf"');
header("Content-Length: " . filesize("files/AU.Report.pdf"));

$fp = fopen("files/AU.Report.pdf", "r");
fpassthru($fp);
	
$filename = 'AU.Report.pdf';

?>
