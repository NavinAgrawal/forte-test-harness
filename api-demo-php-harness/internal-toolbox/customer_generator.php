<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <!-- Template Design by www.studio7designs.com. -->
  <head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
    <meta content="en-gb" http-equiv="Content-Language" />
    <title>Random Customer Generator</title>
    <link href="favicon.ico" rel="SHORTCUT ICON" />
    <link href="style.css" type="text/css" rel="stylesheet" />
  </head>
  <!-- Begin Body -->
  <body>
    <div id="border">
      <div id="container">
        <!-- navbar -->
        <?php include 'include-navbar.php'; ?>
        <!-- header backround image is in the style sheet-->
        <div id="header"> <a href="index.php"></a> </div>
        <!-- content -->
        <div id="content">
          <div class="splitleft">
            <p><span class="style4">Dustin's Customer Generator</span></p>
            <p>&nbsp;</p>
            <p>This script uses REST calls to randomly generate customers that will have a billing address, shipping address and paymethod. Very handy if you need a large dataset for testing.</p>
			<br>
			<div>
			<form method="POST" action="../internal-toolbox/customers/REST_create_customers.php">
				<table class="" border="0" cellpadding="2" cellspacing="1">
					<tr><td colspan="3">Sandbox &nbsp;<input type="radio" name="base_url" value="https://sandbox.forte.net/API/v3" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Production &nbsp;<input type="radio" name="base_url" value="https://api.forte.net/v3"></td></tr>
					<tr><td><img border="0" src="images/spacer.gif" width="20" height="20"></td></tr>
					<tr><td align="right"><label for "genNum">Quantity:&nbsp;</label></td>
					<td><input type="number" name="genNum" min="0" max="5000" step="1" value="0" required="true"></td></tr>
					<tr><td align="right"><label for "apiID">API Access ID:&nbsp;</label></td>
					<td><input type="text" name="apiID" pattern="[A-Za-z0-9]{32}" maxlength="32" required="true" value=""></td></tr>
					<tr><td align="right"><label for "apiKey">API Secure Key:&nbsp;</label></td>
					<td><input type="text" name="apiKey" pattern="[A-Za-z0-9]{32}" maxlength="32" required="true" value=""></td></tr>
					<tr><td align="right"><label for "orgID" >Organization ID:&nbsp;</label></td>
					<td><input type="text" name="orgID" pattern="[0-9]{6}" maxlength="6" required="true" value=""></td></tr>
					<tr><td align="right"><label for "locID">Location ID:&nbsp;</label></td>
					<td><input type="text" name="locID" pattern="[0-9]{6}" maxlength="6" required="true" value=""></td></tr>
					<tr><td>&nbsp;</td><td align="right"><input align="right" class="button4" type="submit" value="Generate"></td></tr>
				</table>
			</form>
			</div>
            <p>&nbsp;</p>
            </div>
          <!-- Begin Page Menu  -->
          <?php include 'include-menu.php'; ?> </div>
      </div>
      <?php include 'include-footer.php'; ?>
    </div>
  </body>
</html>