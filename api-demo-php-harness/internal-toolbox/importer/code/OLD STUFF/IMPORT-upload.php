<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
$target_dir = "../../../internal-toolbox/importer/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// If data.csv exists, rename it to data-OLD.csv
if (file_exists('uploads/data.csv')) {
	rename('../../../internal-toolbox/importer/data.csv','../../../internal-toolbox/importer/data-OLD.csv');
}
// Allow certain file formats
if($imageFileType != "csv" ) {
    echo "Sorry, only CSV files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		$message = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
		echo "<script type='text/javascript'>if(confirm('$message') == true) {window.history.back();} else {window.history.back();}</script>";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
rename($target_file,'../../../internal-toolbox/importer/data.csv');
?>