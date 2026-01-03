<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<?php session_start(); ?>
<?php error_reporting(E_ALL & ~E_NOTICE); ?>
<!-- Template Design by www.studio7designs.com. -->

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
<meta content="en-gb" http-equiv="Content-Language" />
<title>Validate Routing Number</title>
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
    <div id="header">
      <a href="index.php"></a>
    </div>
    <!-- content -->
    <div id="content">
      <div class="splitleft">
        <p><span class="style4">Validate Routing Number</span></p>
        <p>&nbsp;</p>
        <p>This is a PHP script that validates routing numbers.<br>
		</p>
        <form class method="POST" action="routing.number.php">
          <div style="width:90%; ">
            <div style="margin-left:70px">
              <table align="left" class border="0" cellpadding="2" cellspacing="1">
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="5"></td>
                </tr>
                <tr>
                  <td colspan="3">
                  <img border="0" src="images/spacer.gif" width="20" height="15"></td>
                </tr>
                <tr>
                  <td align="right"><span>Enter Routing Number: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input class="input3" type="text" name="routing_number" style="background-color:#FFFFFF; color: #696969; padding-left:4px; width:100px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td align="right">
                  <button class="button9" type="submit" value="reset">Submit
                  </button></td>
                </tr>
              </table>
            </div>
          </div>
        </form>			
 <table style="margin-left:20px;" class border="0" cellpadding="2" cellspacing="1">
<tr>
<td>
<?php
# result.php

echo '<pre>';
if (isset($_SESSION['searchresult']))
	$display = json_encode($_SESSION['searchresult']);
	echo '<br><br>';
	echo '<table style="font-family:trebuchet ms">';
	print_r('<tr><td width="200" align="right">Routing Number: </td>' . '<td>' . $_SESSION['searchresult']['rn'] . '</td></tr>');
	print_r('<tr><td width="200" align="right">Bank Name: </td>' . '<td>' . $_SESSION['searchresult']['customer_name'] . '</td></tr>');
	print_r('<tr><td width="200" align="right">City: </td>' . '<td>' . $_SESSION['searchresult']['city'] . '</td></tr>');
	print_r('<tr><td width="200" align="right">State: </td>' . '<td>' . $_SESSION['searchresult']['state'] . '</td></tr>');
	print_r('<tr><td width="200" align="right">Zipcode: </td>' . '<td>' . $_SESSION['searchresult']['zip'] . '</td></tr>');
	print_r('<tr><td width="200" align="right">Telephone: </td>' . '<td>' . $_SESSION['searchresult']['telephone'] . '</td></tr>');
	print_r('<tr><td width="200" align="right">Change Date: </td>' . '<td>' . $_SESSION['searchresult']['change_date'] . '</td></tr>');
	print_r('<tr><td width="200" align="right">Response: </td>' . '<td>' . $_SESSION['searchresult']['message'] . '</td></tr>');
	echo '</table>';
unset($_SESSION['searchresult']);
?>			  
</td>
</tr>
</table>
     </div>
      <!-- Begin Page Menu  -->
      <?php include 'include-menu.php'; ?>
    </div>
  </div>
  <?php include 'include-footer.php'; ?>
</div>
</body>

</html>