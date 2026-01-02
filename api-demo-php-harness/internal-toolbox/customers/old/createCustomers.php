<html>
<head>
<style type="text/css"> 
@import "form.css";
</style>
<title>Customer Creation Tool</title>
</head>
<body>
<div class="pan">
<div class="headerBanner">
	<h1 class="headerText"> Customer Generation Tool</h1>
</div>
<?php include 'include-menubar.php'; ?>
<div class = "inputDiv">
<form class="inputForm" method="POST" action="REST_create_customers.php">
	<fieldset>
		<legend>Generation Parameters:</legend>
		<label for "genNum" class="inputLabel">Quantity to Generate:</label>
		<input class="inputField" type="number" name="genNum" min="0" max="100" step="1" value="0" required="true"> <br />
		<label for "apiID" class="inputLabel">API ID:</label>
		<input class="inputField" type="text" name="apiID" pattern="[A-Za-z0-9]{32}" maxlength="32" required="true"> <br />
		<label for "apiKey" class="inputLabel">API Access Key:</label>
		<input class="inputField" type="text" name="apiKey" pattern="[A-Za-z0-9]{32}" maxlength="32" required="true"> <br />
		<label for "orgID" class="inputLabel">Organization ID:</label>
		<input class="inputField" type="text" name="orgID" pattern="[0-9]{6}" maxlength="6" required="true"> <br />
		<label for "locID" class="inputLabel">Location ID:</label>
		<input class="inputField" type="text" name="locID" pattern="[0-9]{6}" maxlength="6" required="true"> <br />
		<input class="submitButton" type="submit" value="Generate">
		</fieldset>
</form>
</div>
<div class="footer">
<span class="footerNote"> 
Maintained by: <a class="footerLink" href="mailto:dustin.thomas@forte.com">Dustin Thomas</a>
</span>
<span class="footerNote"> Last updated: 7/25/2018 </span>
</div>
</div>

</body>