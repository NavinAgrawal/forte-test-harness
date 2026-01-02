<?php

//this script combines the multiple CC token files into one file named combined.CC.csv (located in the /internal-toolbox/importer folder)

error_reporting(E_ALL & ~E_NOTICE);

$numberinstances = $_POST['combinerCC'];

// if Combiner "number of instances" textbox is empty
if ($numberinstances == NULL) {
	$message = "Didn\'t you forget something?";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}

if (!file_exists('../../../toolbox2/importer/tokens.CC.csv')) {
	$message = "There are no token files to combine.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}

//magically join all the token files into one file
function joinFiles(array $files, $result) {
    if(!is_array($files)) {
        throw new Exception('`$files` must be an array');
    }

    $wH = fopen($result, "w+");

    foreach($files as $file) {
        $fh = fopen($file, "r");
        while(!feof($fh)) {
            fwrite($wH, fgets($fh));
        }
        fclose($fh);
        unset($fh);
    }
    fclose($wH);
    unset($wH);
}

$i = 1;
$arr = array();
while ($i <= $numberinstances) {
    $arr[] = '../../../toolbox'.$i.'/importer/tokens.CC.csv';  
    $i++;
}

joinFiles($arr, '../../../internal-toolbox/importer/combined.tokens.CC.csv');
sleep(1);
$message = "All token files have been combined into a file named combined.tokens.CC.csv.\\n\\nFind the file in the /internal-toolbox/importer folder.";
echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
?>