<?php

require_once __DIR__ . '/../../config/bootstrap.php';

// PDF Conversion service provided by "HTML 2 PDF Rocket" https://www.html2pdfrocket.com
// First we create the html file, then make an API call to HTML 2 PDF Rocket and his service converts
// the html to a PDF file. 200 conversions per month for free, more than that for a monthly charge.


// set PDF file parameters, including URL to the footer. The footer produces the "Created On" date and page numbers.
$postdata = http_build_query(
    array(
        'apikey' => forte_config('html2pdf_api_key'),
        'value' => 'https://www.calligraphydallas.com/updater/AU.working.html',    //URL to the html file we are going to convert
        'MarginBottom' => '13',
        'MarginTop' => '12',
		'MarginLeft' => '10',
		'MarginRight' => '10',
		'UseLandscape' => 'false',
        'FooterURL' => 'https://www.calligraphydallas.com/updater/UPDATER-pdf.footer.php'   //URL to the pdf footer script
    )
); 
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

// make the API call to the "HTML 2 PDF Rocket" conversion service
$context  = stream_context_create($opts);
$result = file_get_contents('http://api.html2pdfrocket.com/pdf', false, $context);

// save the PDF on the server
file_put_contents($filename, $result);

?>
