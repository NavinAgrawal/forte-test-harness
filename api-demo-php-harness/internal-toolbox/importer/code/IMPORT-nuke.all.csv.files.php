<?php
//this script nukes all the files from the /importer folder
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

$leftovers1 = '*.csv';
$leftovers2 = '*.txt';
array_map("unlink", glob('../' . $leftovers1));
array_map("unlink", glob('../' . $leftovers2));
unlink('*.csv');
unlink('*.txt');

$message = "Done.";
echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";

?>