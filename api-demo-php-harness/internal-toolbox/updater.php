<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <!-- Template Design by www.studio7designs.com. -->
  <head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
    <meta content="en-gb" http-equiv="Content-Language" />
    <title>Account Updater Report</title>
    <link href="favicon.ico" rel="SHORTCUT ICON" />
    <link href="style.css" type="text/css" rel="stylesheet" />
  </head>
  <style type="text/css"> 
    body {
        font-family:Calibri;
        text-align:center;
    }
    table.shadow {
        border-left:4px solid #DFDEDE;
        border-top:4px solid #DFDEDE;
        border-right:4px solid #737373;
        border-bottom:4px solid #737373;
    }
    td.main {
        padding: 30px 30px 40px 30px;
    }
    table tr td {
        font-size:12pt;
    }
    form {
        font-size:12pt;
    }
</style>
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
            <p><span class="style4">Account Updater Report</span></p>
            <p>&nbsp;</p>
			<table class="" border="1" bordercolor="#084f91" cellspacing="0" width="540">
				<tr><td align="center"><p style="text-align:left">
				This report uses REST calls to create a list of all the credit card updates made by our  
				Account Updater service during the selected date range.</td></tr>
				<tr><td><img border="0" src="images/spacer.gif" width="20" height="30"></td></tr>
			</table>
		
		<center>
		<form method="POST" action="../internal-toolbox/updater/UPDATER-engine.php">
        <table class="shadow" cellpadding="0" style="border-collapse: collapse;" bgcolor="#F3F3F3">
        <tr>
        <td>
            <table border="2" style="border-collapse: collapse;" bgcolor="#F3F3F3">
            <tr>
            <td class="main">
            <center>
                <table align="center" border="0" cellpadding="1" cellspacing="0">
                    <tr><td colspan="3" align="center"><span style="color:#000088; font-size:16pt; font-family:Cambria"><b>Account Updater Report</b></span></td></tr>
                    <tr><td colspan="3"><p align="center"><span style="color:#000088; font-size:12pt; font-family:Times New Roman"><b>----›§‹----</b></span></td></tr>
                    <tr><td align="center">Start Date:</td><td align="center">&nbsp;</td><td align="center">End Date:</td></tr>
                    <tr>
                      <td align="center"><input type="text" name="start_date" style="text-align:center; color: #696969; width:110px; height:20px; font-family:Calibri;"></td>
                      <td align="center" width="40">to</td>
                      <td align="center"><input type="text" name="end_date" style="text-align:center; color: #696969; width:110px; height:20px; font-family:Calibri;"></td>
                    </tr>
                    <tr><td valign="top" style="text-align:center; font-size: 11pt;">YYYY-MM-DD</td><td align="center">&nbsp;</td><td valign="top" style="text-align:center; font-size:11pt;">YYYY-MM-DD</td></tr>
                </table>
                <table border="0" cellpadding="1" cellspacing="0">
                    <tr><td colspan="3" align="center"><span style="font-size: 6pt">&nbsp;</span></td></tr>
                    <tr><td align="right"><span style="font-size: 12pt">Organization ID:&nbsp;</span></td><td><input type="text" name="organization_id" style="color: #696969; padding-left:4; width:230px; height:20px; font-family:Calibri;"></td><td>&nbsp;</td></tr>
                    <tr><td align="right"><span style="font-size: 12pt">Location ID:&nbsp;</span></td><td><input type="text" name="location_id" style="color: #696969; padding-left:4; width:230px; height:20px; font-family:Calibri;"></td><td>&nbsp;</td></tr>
                    <tr><td align="right"><span style="font-size: 12pt">API Access ID:&nbsp;</span></td><td><input type="text" name="api_access_id" style="color: #696969; padding-left:4; width:230px; height:20px; font-family:Calibri;"></td><td>&nbsp;</td></tr>
                    <tr><td align="right"><span style="font-size: 12pt">API Secure Key:&nbsp;</span></td><td><input type="text" name="api_secure_key" style="color: #696969; padding-left:4; width:230px; height:20px; font-family:Calibri;"></td><td>&nbsp;</td></tr>
                    <!--tr><td align="right"><span style="font-size: 12pt">Company Name:&nbsp;</span></td><td><input type="text" name="company_name" value="(optional)" style="color: #696969; padding-left:4; width:230px; height:20px; font-size:11pt; font-family:Calibri;"></td><td>&nbsp;</td></tr>
                    <tr><td align="right"><span style="font-size: 12pt">Email To:&nbsp;</span></td><td><input type="text" name="email_address" value="(optional)" style="color: #696969; padding-left:4; width:230px; height:20px; font-size:11pt; font-family:Calibri;"></td><td>&nbsp;</td></tr>
                    <tr><td align="right" valign="top"><span style="font-size: 12pt;">Email Note:&nbsp;</span></td><td><textarea rows="2"  cols="2" name="email_note" style="overflow:auto; color: #696969; padding-left:5; width:230px; font-size:11pt; font-family:Calibri;">(optional)</textarea></td><td>&nbsp;</td></tr -->
                </table>
                <table border="0" cellspacing="0" width="330">
                    <tr><td align="center"><span style="font-size: 6pt">&nbsp;</span></td></tr>
                    <tr><td align="center"><p style="font-size:12pt; text-align:center">
                    You will find the AU report in the htdocs/internal-toolbox/updater/ folder.  
                    <tr><td align="center"><span style="font-size: 4pt">&nbsp;</span></td></tr>
                </table>
                <!--table>
                    <tr><td align="center"><span style="font-size: 12pt; text-align:center; color:#000088">
                    <b>The PDF can take a few seconds.</b></span></td></tr>
                    <tr><td align="center"><span style="font-size: 6pt">&nbsp;</span></td></tr>
                </table -->
                <table border="0" cellpadding="1" cellspacing="0">
                    <tr>
                      <td align="center">
                      <button class="button10" type="submit" name="csv" value="csv">CSV file</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button class="button10" type="submit" name="xml" value="xml">XML file</button>
                      <!--button class="button10" type="submit" name="pdf" value="pdf">PDF file</button -->
                      </td>
                    </tr>
                </table>
            </center>
            </td>
            </tr>
            </table>
        </td>
        </tr>
        </table>
    </form>
	</center>		
			
			
			
			
			<!--form class="inputForm" method="POST" action="../internal-toolbox/updater/UPDATER-engine.php">
			<table width="100%" class="" cellpadding="0" style="border-collapse: collapse;" bgcolor="">
			<tr>
			<td>
				<table width="90%" border="1" bordercolor="#084f91"  style="border-collapse: collapse;" bgcolor="">
				<tr>
				<td align="center">
					<table class="" border="1" bordercolor="#084f91"  cellpadding="" cellspacing="0">
						<tr><td align="center">Start Date:</td><td align="center">&nbsp;</td><td align="center">End Date:</td></tr>
						<tr>
						  <td align="center"><input type="text" name="start_date" style="text-align:center; color: #696969; width:110px; height:20px; font-family:Calibri;"></td>
						  <td align="center" width="40">to</td>
						  <td align="center"><input type="text" name="end_date" style="text-align:center; color: #696969; width:110px; height:20px; font-family:Calibri;"></td>
						</tr>
						<tr><td valign="top" style="text-align:center; font-size: 11pt;">YYYY-MM-DD</td><td align="center">&nbsp;</td><td valign="top" style="text-align:center; font-size:11pt;">YYYY-MM-DD</td></tr>
						<tr><td><img border="0" src="images/spacer.gif" width="20" height="10"></td></tr>
					</table>
					<table class="" border="0" cellpadding="2" cellspacing="1">
						<tr><td align="right"><span>Organization ID: </span></td><td>&nbsp;</td><td><input type="text" name="organization_id" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;"></td></tr>
						<tr><td align="right"><span>Location ID: </span></td><td>&nbsp;</td><td><input type="text" name="location_id" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;"></td></tr>
						<tr><td align="right"><span>API Access ID: </span></td><td>&nbsp;</td><td><input type="text" name="api_access_id" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;"></td></tr>
						<tr><td align="right"><span>API Secure Key: </span></td><td>&nbsp;</td><td><input type="text" name="api_secure_key" style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;"></td></tr>
						<!-- tr><td align="right"><span>Email To: </span></td><td>&nbsp;</td><td><input type="text" name="email_address" value="(optional)" style="color: #696969; padding-left:4px; width:230px; height:20px; font-size:11pt; font-family:Calibri;"></td></tr>
						<tr><td align="right" valign="top"><span>Email Note: </span></td><td>&nbsp;</td><td><textarea rows="2"  cols="2" name="email_note" style="resize: none; border-radius: 3px; border: 1px solid lightSlategray; overflow:auto; color: #696969; padding-left:5px; width:230px; font-size:11pt; font-family:Calibri;">(optional)</textarea></td></tr -->
					    <!--tr><td colspan="3" align="right"><button class="button6" type="reset" value="reset">Clear the fields</button></td></tr>
					</table>
					<!-- table>
						<tr><td><img border="0" src="images/spacer.gif" width="20" height="15"></td></tr>
						<tr><td align="center"><span style="text-align:center;">The PDF can take a few seconds.</span></td></tr>
						<tr><td><img border="0" src="images/spacer.gif" width="20" height="15"></td></tr>
					</table -->
					<!--table border="0" cellpadding="1" cellspacing="0">
						<tr><td><img border="0" src="images/spacer.gif" width="20" height="30"></td></tr>
						<tr>
						  <td align="center">
						  <button class="button10" type="submit" name="csv" value="csv">CSV file</button>&nbsp;&nbsp;&nbsp;
						  <button class="button10" type="submit" name="xml" value="xml">XML file</button>
						  <!-- button class="button4" type="submit" name="pdf" value="pdf">PDF file</button -->
						  <!--/td>
						</tr>
					</table>
				</td>
				</tr>
				</table>
			</td>
			</tr>
			</table>
			</form -->
            <p>&nbsp;</p>
          </div>
          <!-- Begin Page Menu  -->
          <?php include 'include-menu.php'; ?> </div>
      </div>
      <?php include 'include-footer.php'; ?>
    </div>
  </body>
</html>