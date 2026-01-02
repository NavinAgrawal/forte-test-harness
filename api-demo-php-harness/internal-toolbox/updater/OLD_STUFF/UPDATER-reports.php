<?php
if (isset($_POST['pdf'])) {
	
	//create the pdf file
	include('UPDATER-pdf.php');
	
	//email it
	include('UPDATER-mail.php');
}
	else if (isset($_POST['xml'])) {
		include('UPDATER-xml.php');
		include('UPDATER-download.php');
		include('UPDATER-mail.php');
	}
	else if (isset($_POST['csv'])) {
		include('UPDATER-csv.php');
		include('UPDATER-download.php');
		include('UPDATER-mail.php');
	}
	else if (isset($_POST['webpage'])) {
		include('UPDATER-html.php');
	}
?>