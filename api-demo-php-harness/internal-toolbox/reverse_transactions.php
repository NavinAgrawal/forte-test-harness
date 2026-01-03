<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<!-- Template Design by www.studio7designs.com. -->

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
<meta content="en-gb" http-equiv="Content-Language" />
<title>Reverse Transactions</title>
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
        <p><span class="style4">Reverse Transactions</span></p>
        <p>&nbsp;</p>
        <p>Shone wrote this REST script for reversing transactions. You can do a single 
		transaction, or multiple transactions using a csv file.</p><br>
		

        <b><u>Reverse a single transaction</u></b>
        <form class method="POST" action="../internal-toolbox/reverse_transactions/reverse_single.php">
          <div align="center" style="margin-left:0px; width:90%; ">
            <div style="margin-left:70px">
              <table align="left" class border="0" cellpadding="2" cellspacing="1">
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="20"></td>
                </tr>
                <tr>
                  <td colspan="3">
                  <p align="center">Sandbox&nbsp;
                  <input type="radio" name="base_url" value="https://sandbox.forte.net/API/v3" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                  Production&nbsp;
                  <input type="radio" name="base_url" value="https://api.forte.net/v3"></p>
                  </td>
                </tr>
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="15"></td>
                </tr>
                <tr>
                  <td align="right"><span>Organization ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input class="input2" type="text" name="organization_id" style="background-color:#FFFFFF; color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td align="right"><span>Location ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="location_id" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td align="right"><span>API Access ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="api_access_id" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td align="right"><span>API Secure Key: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="api_secure_key" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td align="right"><span>Transaction ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="trans_id" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td align="right"><span>Authorization Code: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="auth_code" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td align="right"><span>Amount: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="amount" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
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
        </form><br><br><br><br><br><br><br><br><br><br><br>






		
		<b><u>Reverse multiple transactions</u></b><br><br>
		<p>Do a transaction export in Virtual Terminal, and create 
		a csv file with only the transaction ID, authorization code, and amount -- in that order.</p><br>
		<form align="left" style="margin-left:70px" action="upload.php" method="post" enctype="multipart/form-data">
			Select csv file to upload:
			<input type="file" name="fileToUpload" id="fileToUpload"><br>
			<p style="margin-left:280px"><input class="button6" type="submit" value="Upload csv file" name="submit">
		</form>
		</p>
        <form class method="POST" action="../internal-toolbox/reverse_transactions/reverse_list.php">
          <div align="center" style="margin-left:30px; width:90%; ">
            <div style="margin-left:70px">
              <table align="left" class border="0" cellpadding="2" cellspacing="1">
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="20"></td>
                </tr>
                <tr>
                  <td colspan="3">
                  <p align="center">Sandbox&nbsp;
                  <input type="radio" name="base_url" value="https://sandbox.forte.net/API/v3" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                  Production&nbsp;
                  <input type="radio" name="base_url" value="https://api.forte.net/v3"></p>
                  </td>
                </tr>
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="15"></td>
                </tr>
                <tr>
                  <td align="right"><span>Organization ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input class="input2" type="text" name="organization_id" style="background-color:#FFFFFF; color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td align="right"><span>Location ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="location_id" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td align="right"><span>API Access ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="api_access_id" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
                </tr>
                <tr>
                  <td align="right"><span>API Secure Key: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="api_secure_key" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20"></td>
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
      </div>
      <!-- Begin Page Menu  -->
      <?php include 'include-menu.php'; ?>
    </div>
  </div>
  <?php include 'include-footer.php'; ?>
</div>

</body>

</html>