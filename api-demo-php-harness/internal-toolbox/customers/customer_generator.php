<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <!-- Template Design by www.studio7designs.com. -->
  <head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
    <meta content="en-gb" http-equiv="Content-Language" />
    <title>Tech Support Toolbox</title>
    <link href="../favicon.ico" rel="SHORTCUT ICON" />
    <link href="../style.css" type="text/css" rel="stylesheet" />
  </head>
  <!-- Begin Body -->
  <body>
    <div id="border">
      <div id="container">
        <!-- navbar -->
        <?php include '../include-navbar.php'; ?>
        <!-- header background image is in the style sheet-->
        <div id="header"> <a href="index.php"></a> </div>
        <!-- content -->
        <div id="content">
          <div class="splitleft">
		  <div class="fieldBorder"> 
		  
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
						<input class="button" type="submit" value="Generate">
					</fieldset>
				</form>
				</div>
            </div>
          <!-- Begin Page Menu  -->
          <?php include '../include-menu.php'; ?> </div>
      </div>
      <?php include '../include-footer.php'; ?>
    </div>
  </body>
</html>