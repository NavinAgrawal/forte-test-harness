<?PHP
if(isset($_POST['SubmitButton'])){
	$type=$_POST['format'];
	if (! file_exists("uploads")){
		mkdir("uploads");
	}
	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
	$fileToRead =fopen($target_file,'r');
	
	$counter =1;
	while(!feof($fileToRead)){
	$currentLine = fgets($fileToRead);
		$firstChar = $currentLine[0];
		if($type=="CSV" || $type=="FIXED"){
			if($firstChar=='3' || $firstChar=='4'){
				echo "<table border=\"0\">";
				echo "<tr>";
				echo "<td width=\"4%\"><pre>$counter</pre></td>";
				echo "<td>";
				echo "<pre>$currentLine</pre>";
				echo "</td>";
				echo "</tr>";
				$counter++;
			}
		}
		else if($type == "NACHA"){
			if($firstChar=='6'){
				echo "<table border=\"0\">";
				echo "<tr>";
				echo "<td width=\"4%\"><pre>$counter</pre></td>";
				echo "<td>";
				echo "<pre>$currentLine</pre>";
				echo "</td>";
				echo "</tr>";
				$counter++;
			}
		}
	}
	fclose($fileToRead);
	unlink($target_file);
}

?>