<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
$target_dir = "../../importer/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// If data.csv exists, rename it to data-OLD.csv
/* if (file_exists('../../importer/data.ACH.csv')) {
	rename('../../importer/data.ACH.csv','../../importer/data.ACH.OLD.csv');
}  */

// Allow certain file formats
if($imageFileType != "csv" ) {
    echo "<script type='text/javascript'>alert('There was an error uploading your file.\\n\\nEither you forgot to select a file, your file exceeds the size limit, or you chose a file\\nthat is not CSV.\\n\\nYou might need to save your data as \"data.CC.csv\" in the internal-toolbox/importer folder.');</script>";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "<script type='text/javascript'>alert('Your file was not uploaded. Try again.');window.history.back();</script>";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {		
		rename($target_file,'../data.ACH.csv');
		sleep(1);
		//get the number of tokens in undo.import.csv file (without loading it into memory)
		$file = new SplFileObject('../data.ACH.csv', 'r');
		$file->seek(PHP_INT_MAX);
		$number_records = $file->key();
	
		$message = "The file \"". basename( $_FILES["fileToUpload"]["name"]). "\" has been uploaded with $number_records customers.\\n\\nYou will find it in the /internal-toolbox/importer/ folder as \"data.ACH.csv\".";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
    } else {
        echo "<script type='text/javascript'>alert('There was an error uploading your file.\\n\\nYou need to save your data as \"data.ACH.csv\" in the /internal-toolbox/importer/ folder.');window.history.back();</script>";
    }
}
fclose($target_file);
?>