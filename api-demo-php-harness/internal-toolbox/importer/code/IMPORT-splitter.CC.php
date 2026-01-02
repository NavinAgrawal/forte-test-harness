<?php

//this script splits the full dataset into separate pieces depending on how many instances will be run

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

//capture the number of instances
$numberinstances = $_POST['splitter_CC'];

// if Splitter value is NULL
if ($numberinstances == NULL) {
	$message = "Didn\'t you forget something?";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}

if (!file_exists('../../importer/dataset.CC.csv')) {
	$message = "The file \"dataset.CC.csv\" does not exist.\\n\\nIt belongs in /internal-toolbox/importer/ folder and it ain\'t there.";
	echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
	exit;
}

//get the total number of records in the dataset (without loading it into memory)
$file = new SplFileObject('../../importer/dataset.CC.csv', 'r');
$file->seek(PHP_INT_MAX);
$totalrecords = $file->key();

//define a few variables
$inputFile  = '../../importer/dataset.CC.csv';
$outputFile = '../../importer/output';
$splitSize  = ceil($totalrecords/$numberinstances);  //calculate the size of the chunks

//magically split the csv into multiple csv's
$in = fopen($inputFile, 'r');

$rowCount = 0;
$fileCount = 1;
while (!feof($in)) {
    if (($rowCount % $splitSize) == 0) {
        if ($rowCount > 0) {
            fclose($out);
        }
        $out = fopen($outputFile . $fileCount++ . '.csv', 'w');
    }
    $data = fgetcsv($in);
    if ($data)
        fputcsv($out, $data);
    $rowCount++;
}
fclose($out);
fclose($in);
unset($file);
unlink('../../importer/output'.($numberinstances + 1).'.csv');
unlink('../../importer/dataset.CC.csv');

//rename the output files and move them to their proper destination folder
$i = 1;
while ($i <= $numberinstances) {
	rename ('../../importer/output'.$i.'.csv', '../../../toolbox'.$i.'/importer/data.CC.csv');
	$i++;
}

//get the number of records in the last chunk (without loading it into memory)
$file = new SplFileObject('../../../toolbox'.$numberinstances.'/importer/data.CC.csv', 'r');
$file->seek(PHP_INT_MAX);
$totalrecordsLast = $file->key();

$numberchunks = ($numberinstances-1);
$total = ($splitSize * $numberchunks)+$totalrecordsLast;
$message = "SPLITTER RESULTS:\\n\\nTotal records in the CC dataset: $totalrecords\\nNumber of instances: $numberinstances\\nSize of chunks 1-$numberchunks: $splitSize records each\\nSize of chunk $numberinstances: $totalrecordsLast records\\nCheck the math: ($splitSize x $numberchunks) + $totalrecordsLast = $total";
echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";

?>