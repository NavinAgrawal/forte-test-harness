<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<!-- Template Design by www.studio7designs.com. -->

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
<meta content="en-gb" http-equiv="Content-Language" />
<title>Toolbox 1</title>
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
        <p><span class="style4">James&#39; Importer++</span></p>
        <p>&nbsp;</p>
        <p>This is a REST app for importing 3rd-party data and creating token files 
        that include the customer and paymethod tokens. It creates a csv file of 
        the records that fail, and an error log of the reason why they failed. The 
        token files are created during the import in folder /internal-toolbox/importer/.
        </p>
        <table border="0" cellpadding="1" cellspacing="0">
          <tr>
            <td><img border="0" src="images/spacer.gif" width="20" height="15"></td>
          </tr>
          <tr>
            <td>
            <a class="link" target="_blank" style="font-size:13pt" href="importer/code/IMPORT-directions.php">
            How to Use the Importer tool</a> </td>
          </tr>
          <tr>
            <td><img border="0" src="images/spacer.gif" width="20" height="5"></td>
          </tr>
          <tr>
            <td>
            <a class="link" target="_blank" style="font-size:13pt" href="importer/code/IMPORT-admin.php">
            Multi-Instance Admin</a> </td>
          </tr>
          <tr>
            <td><img border="0" src="images/spacer.gif" width="20" height="5"></td>
          </tr>
          <tr>
            <td>
            <a class="link" target="_blank" style="font-size:13pt" href="importer/sample_data/Template.xlsx">
            Excel Template</a> - <i>
            <font style="color:red; font-family:Book Antiqua; font-size:14pt;">The 
            import file must be in this format, columns in the same order, without 
            the header row. Do not add or delete any columns.</font></i> </td>
          </tr>
        </table>
        <center><img border="0" src="images/spacer.gif" width="20" height="30">
        <br>
        <div align="center" style="width:85%; background-color:#F9F9F9; padding:10px 0px 0px 0px; border-style:solid; border-width:thick; border-color:gray;">
          <img border="0" src="images/spacer.gif" width="20" height="20">
          <center>
          <font color="0A1495" style="font-family:Algerian; font-size:22pt">TOOLBOX 1</font> </center><br>
          <form align="center" style="margin-left:0px; text-align:center;" action="importer/code/IMPORT-upload.php" method="post" enctype="multipart/form-data">
            UPLOAD YOUR DATA FILE:<br>
            <!-- Select Credit Card or ACH > Choose your file > Upload<br -->
			<img border="0" src="images/spacer.gif" width="20" height="5">
            <p align="center">Credit Card&nbsp;
            <input type="radio" name="CC_or_ACH" value="CC" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
            ACH&nbsp; <input type="radio" name="CC_or_ACH" value="ACH"> </p>
            <img border="0" src="images/spacer.gif" width="20" height="10"><br>
            <input type="file" name="fileToUpload" id="fileToUpload"><input class="button9" type="submit" value="Upload" name="submit">
          </form>
          <img border="0" src="images/spacer.gif" width="20" height="5"><br>
          <span style="font-family:Times New Roman; font-size: 14pt;"><b>——›§‹——</b></span><br>
          <img border="0" src="images/spacer.gif" width="20" height="8"><br -->
          <form align="center" class method="POST" action="../internal-toolbox/importer/code/IMPORT-engine.php">
            <div style="margin-left:60px">
              <table align="left" class border="0" cellpadding="2" cellspacing="1">
                <tr>
                  <td colspan="3">
                  <p align="center">Sandbox&nbsp;
                  <input type="radio" name="base_url" value="https://sandbox.forte.net/API/v3" checked>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                  Production&nbsp;
                  <input type="radio" name="base_url" value="https://api.forte.net/v3">
                  </p>
                  </td>
                </tr>
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="10">
                  </td>
                </tr>
                <tr>
                  <td align="right"><span>Organization ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input class="input2" type="text" name="organization_id" value style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20">
                  </td>
                </tr>
                <tr>
                  <td align="right"><span>Location ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="location_id" value style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20">
                  </td>
                </tr>
                <tr>
                  <td align="right"><span>API Access ID: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="api_access_id" value style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20">
                  </td>
                </tr>
                <tr>
                  <td align="right"><span>API Secure Key: </span></td>
                  <td>&nbsp;</td>
                  <td>
                  <input type="text" name="api_secure_key" value style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20">
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td align="right">
                  <button class="button6" type="reset" value="reset">Clear the fields
                  </button></td>
                </tr>
              </table>
            </div>
            <div style="margin-left:100px;text-align:left">
              <table border="0" cellpadding="1" cellspacing="0" width="100%">
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="10">
                  </td>
                </tr>
                <tr>
                  <td>
                  <button class="button2" type="submit" name="inventory" value="inventory">
                  Check<br>
                  Contents</button>&nbsp;
                  <button class="button2" type="submit" name="leftovers" value="leftovers">
                  Delete the Leftovers</button>&nbsp; </td>
                </tr>
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="20">
                  </td>
                </tr>
                <tr>
                  <td>Import Customers<br>
                  <button class="button2" type="submit" name="import_CC" value="import_CC">
                  Import CC Customers</button>&nbsp;
                  <button class="button2" type="submit" name="errors_CC" value="errors_CC">
                  Import CC Failures</button>&nbsp; </td>
                </tr>
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="10">
                  </td>
                </tr>
                <tr>
                  <td>
                  <button class="button2" type="submit" name="import_ACH" value="import_ACH">
                  Import ACH Customers</button>&nbsp;
                  <button class="button2" type="submit" name="errors_ACH" value="errors_ACH">
                  Import ACH Failures</button></td>
                </tr>
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="20">
                  </td>
                </tr>
                <tr>
                  <td>Exports<br>
                  <button class="button2" type="submit" name="export" value="export">
                  Export Customers</button>&nbsp;
                  <button class="button2" type="submit" name="schedules" value="schedules">
                  Export Schedules</button>&nbsp;
                  <button class="button2" type="submit" name="paymethods" value="paymethods">
                  Export Paymethods</button>&nbsp; </td>
                </tr>
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="20">
                  </td>
                </tr>
                <tr>
                  <td>Delete Stuff<br>
                  <button class="button5" type="submit" name="delete_everything" value="delete_everything">
                  NUKE<br>
                  THE MID</button>&nbsp;
                  <button class="button5" type="submit" name="undo_import_CC" value="undo_import_CC">
                  Undo The<br>
                  Import CC</button>&nbsp;
                  <button class="button5" type="submit" name="undo_import_ACH" value="undo_import_ACH">
                  Undo The<br>
                  Import ACH</button></td>
                </tr>
                <tr>
                  <td>
                  <img border="0" src="images/spacer.gif" width="20" height="20">
                  </td>
                </tr>
              </table>
            </div>
            <p align="left">
            <button type="submit" name="nuke_the_files" value="nuke_the_files">
            <img border="0" src="images/spacer.gif" width="20" height="20">
            </button></p>
          </form>
        </div>
        </center><br>
        <p></p>
        <ul class="importer">
          <li class="importer"><b>&quot;Check Contents&quot;</b> checks the number of customers, 
          paymethods and schedules currently in the mid.</li>
          <li class="importer"><b>&quot;Delete the Leftovers&quot;</b> deletes the failure 
          logs, export files and token files from previous imports.</li>
        </ul>
        <p></p>
        <img border="0" src="images/spacer.gif" width="20" height="20">
      </div>
      <!-- Begin Page Menu -->
      <?php include 'include-menu.php'; ?>
    </div>
  </div>
  <?php include 'include-footer.php'; ?>
</div>

</body>

</html>
