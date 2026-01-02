<?php
$replicate_this = '../../../internal-toolbox/importer/code/IMPORT-undo.the.import.CC.php';

for ($i = 2; $i <= 9; $i++) {
	$new_file = '../../../toolbox'. $i . '/importer/code/IMPORT-undo.the.import.CC.php';
	$file_contents = file_get_contents($replicate_this);
	$string1 = "/toolbox".$i.'/';
	$string2 = "TOOLBOX ".$i;
	$string3 = "Toolbox ".$i;
	$file_contents = str_replace("/internal-toolbox/", $string1, $file_contents);
	$file_contents = str_replace("TOOLBOX 1", $string2, $file_contents);
	$file_contents = str_replace("Toolbox 1", $string3, $file_contents);
	file_put_contents($new_file,$file_contents);
}
echo 'finished';
?>