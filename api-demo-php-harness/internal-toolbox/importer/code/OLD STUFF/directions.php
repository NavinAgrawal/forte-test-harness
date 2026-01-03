<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <!-- Template Design by www.studio7designs.com. -->
  <head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
    <meta content="en-gb" http-equiv="Content-Language" />
    <title>Importer++</title>
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
              the records that fail, and an error log of the reason why they failed.
            </p>
            <table border="0" cellpadding="1" cellspacing="0">
              <tr>
                <td><img border="0" src="images/spacer.gif" width="20" height="15"></td>
              </tr>
              <tr>
                <td>
                  <a class="link" target="_blank" style="font-size:13pt" href="importer/sample_data/Template.xlsx">
                  Excel Template</a> - <i>
                  <font style="color:red; font-family:Book Antiqua; font-size:14pt;">The 
                  import file must be in this format, columns in the same order, without 
                  the header row. Do not add or delete any columns.</font></i>
                </td>
              </tr>
            </table>
            <img border="0" src="images/spacer.gif" width="20" height="10"><br>
            <u>Directions</u>
            <ol class="importer">
              <li class="importer">Massage the raw data and copy-paste it into the Excel 
                template. Be mindful that you select the <u>correct tab</u> in the template.
              </li>
              <li class="importer"><u>Without the header row</u>, save your file as CSV. (anywhere you want and any name you want)</li>
              <li class="importer">In the Toolbox UI, run "Delete the Leftovers" to get rid of any leftover files from the last import.
                <i><u>Do not forget to do this,</u></i> otherwise you run the risk of getting a jacked up tokens file.
              </li>
              <li class="importer">Upload your data file.</li>
              <li class="importer">Import the data into an empty, live mid.</li>
              <li class="importer">If there are failures, open the failure log file 
                and failure data file in Notepad++. Find these files in the /internal-toolbox/importer folder.
              </li>
              <li class="importer">Use the failure log file to fix the errors in the failure data file.</li>
              <li class="importer">Import the failure data file.</li>
              <li class="importer">Grab the token file from the /internal-toolbox/importer folder and save it.</li>
              <li class="importer"><font style="color:red"><b>Do a Data Explorer APM export just in case. Save it.</b></font></li>
              <li class="importer">Lastly, move the data to the target mid using the DX job 
                &quot;Transfer Client Data&quot;.
              </li>
            </ol>
            <img border="0" src="images/spacer.gif" width="20" height="10"><br>
            <u>Multiple Instances</u>
            <ul class="importer">
              <li class="importer">Multiple instances can only be managed from Toolbox 1.</li>
              <li class="importer">Chrome limit is 6 instances, Firefox lets you do 9.</li>
              <li class="importer">If you have any failures, you must fix them in the instance folder in which they occurred.</li>
              <li class="importer">Firefox is actually better for multiple instances, for reasons that will become apparent if you play around with both.</li>
              <li class="importer">If you are doing a single instance, use the file uploader. If multiple instances, use the splitter.</li>
              <li class="importer">For multiple instances, you must save the dataset from Excel as "dataset.csv" into the /internal-toolbox/importer/code folder.</li>
              <li class="importer">Run "Delete the Leftovers" in <u>every</u> instance.</li>
            </ul>
            <center>
              <img border="0" src="images/spacer.gif" width="20" height="30">
              <br>
              <div align="center" style="width:85%; background-color:#F9F9F9; padding:10px 0px 10px 0px; border-style:solid; border-width:thick; border-color:gray;">
                <img border="0" src="images/spacer.gif" width="20" height="20">
                <center><font color="0A1495" style="font-family:Algerian; font-size:22pt">TOOLBOX 1</font></CENTER>
                <BR>
                <form align="center" style="margin-left:20px; text-align:center;" action="upload.php" method="post" enctype="multipart/form-data">
                  Select file to upload:
                  <input type="file" name="fileToUpload" id="fileToUpload">
                  <p style="margin-left:270px">
                    <input class="button9" type="submit" value="Upload file" name="submit">
                  </p>
                </form>
                <img border="0" src="images/spacer.gif" width="20" height="0">
                <div>><font size="5pt" style="font-family:Times New Roman"><b></b></font><</div>
                <img border="0" src="images/spacer.gif" width="20" height="10">
                <form align="center" style="text-align:center;" action="../internal-toolbox/importer/code/splitter.php" method="post">
                  Number of instances:
                  <input type="text" style="width:20px" name="splitter" value="">&nbsp;
                  <input class="button6" type="submit" value="Dataset Splitter" name="Splitter"></p>
                </form>
                <img border="0" src="images/spacer.gif" width="20" height="6">
                <form align="center" style="text-align:center;" action="../internal-toolbox/importer/code/combiner.CC.php" method="post">
                  Number of instances:
                  <input type="text" style="width:20px" name="combinerCC" value="">&nbsp;
                  <input class="button6" type="submit" value="CC Combiner" name="CC Combiner"></p>
                </form>
                <img border="0" src="images/spacer.gif" width="20" height="6">
                <form align="center" style="text-align:center;" action="../internal-toolbox/importer/code/combiner.ACH.php" method="post">
                  Number of instances:
                  <input type="text" style="width:20px" name="combinerACH" value="">&nbsp;
                  <input class="button6" type="submit" value="ACH Combiner" name="ACH Combiner"></p>
                </form>
                <img border="0" src="images/spacer.gif" width="20" height="10">
                <div>><font size="5pt" style="font-family:Times New Roman"><b></b></font><</div>
                <img border="0" src="images/spacer.gif" width="20" height="10">
                <form align="center" class method="POST" action="../internal-toolbox/importer/code/IMPORT-engine.php">
                  <div style="margin-left:60px">
                    <table align="left" class border="0" cellpadding="2" cellspacing="1">
                      <tr>
                        <td>
                          <img border="0" src="images/spacer.gif" width="20" height="0">
                        </td>
                      </tr>
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
                          <img border="0" src="images/spacer.gif" width="20" height="15">
                        </td>
                      </tr>
                      <tr>
                        <td align="right"><span>Organization ID: </span></td>
                        <td>&nbsp;</td>
                        <td>
                          <input class="input2" type="text" name="organization_id" value=""
                            style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20">
                        </td>
                      </tr>
                      <tr>
                        <td align="right"><span>Location ID: </span></td>
                        <td>&nbsp;</td>
                        <td>
                          <input type="text" name="location_id" value=""
                            style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20">
                        </td>
                      </tr>
                      <tr>
                        <td align="right"><span>API Access ID: </span></td>
                        <td>&nbsp;</td>
                        <td>
                          <input type="text" name="api_access_id" value=""
                            style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20">
                        </td>
                      </tr>
                      <tr>
                        <td align="right"><span>API Secure Key: </span></td>
                        <td>&nbsp;</td>
                        <td>
                          <input type="text" name="api_secure_key" value=""
                            style="color: #696969; padding-left:4px; width:230px; height:20px; font-family:Calibri;" size="20">
                        </td>
                      </tr>
                      <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">
                          <button class="button6" type="reset" value="reset">Clear the fields
                          </button>
                        </td>
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
                          Delete the Leftovers</button>&nbsp; 
                        </td>
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
                          Import CC Failures</button>&nbsp; 
                        </td>
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
                          Import ACH Failures</button>
                        </td>
                      </tr>
                      <!--tr>
                        <td>
                        <img border="0" src="images/spacer.gif" width="20" height="10"></td>
                        </tr>
                        <!--tr>
                        <td>
                        <button class="button2" type="submit" name="recpro_CC" value="recpro_CC">
                        RecPro CC Customers</button>&nbsp;
                        <button class="button2" type="submit" name="recpro_CC_failures" value="recpro_CC_failures">
                        RecPro CC Failures</button></td>
                        </tr>
                        <tr>
                        <td>
                        <img border="0" src="images/spacer.gif" width="20" height="10"></td>
                        </tr>				
                        <tr>
                        <td>
                        <button class="button2" type="submit" name="recpro_ACH" value="recpro_ACH">
                        RecPro ACH Customers</button>&nbsp;
                        <button class="button2" type="submit" name="recpro_ACH_failures" value="recpro_ACH_failures">
                        RecPro ACH Failures</button></td>
                        </tr -->
                      <!--tr>
                        <td>
                        <img border="0" src="images/spacer.gif" width="20" height="20"></td>
                        </tr>                        
                        <!--tr>
                        <td>Import Schedules<br>
                        <button class="button2" type="submit" name="CCschedules" value="CCschedules">
                        Import CC Schedules</button>&nbsp;
                        <button class="button2" type="submit" name="CCcustFails" value="CCcustFails">
                        CC Fails Customers</button>&nbsp;
                        <button class="button2" type="submit" name="CCschedFails" value="CCschedFails">
                        CC Fails Schedules</button>&nbsp; </td>
                        </tr>
                        <tr>
                        <td>
                        <img border="0" src="images/spacer.gif" width="20" height="10"></td>
                        </tr>
                        <tr>
                        <td>
                        <button class="button2" type="submit" name="ACHschedules" value="card">
                        Import ACH Schedules</button>&nbsp;
                        <button class="button2" type="submit" name="ACHcustFails" value="echeck">
                        ACH Fails Customers</button>&nbsp;
                        <button class="button2" type="submit" name="ACHschedFails" value="export">
                        ACH Fails Schedules</button>&nbsp; </td>
                        </tr-->
                      <tr>
                        <td>
                          <img border="0" src="images/spacer.gif" width="20" height="20">
                        </td>
                      </tr>
                      <tr>
                        <td>Exports<br>
                          <button class="button2" type="submit" name="card" value="card">
                          Export CC Customers</button>&nbsp;
                          <button class="button2" type="submit" name="echeck" value="echeck">
                          Export ACH Customers</button>&nbsp;
                          <button class="button2" type="submit" name="export" value="export">
                          Export ALL Customers</button>&nbsp; 
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <img border="0" src="images/spacer.gif" width="20" height="10">
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <button class="button2" type="submit" name="schedules" value="schedules">
                          Export Schedules</button>&nbsp;
                          <button class="button2" type="submit" name="paymethods" value="paymethods">
                          Export Paymethods</button>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <img border="0" src="images/spacer.gif" width="20" height="10">
                        </td>
                      </tr>
                      <!--tr>
                        <td>
                        <button class="button2" type="submit" name="alarmbillerCC" value="alarmbillerCC">&nbsp;<b>AlarmBiller</b> 
                        CC Export</button>&nbsp;
                        <button class="button2" type="submit" name="alarmbillerACH" value="alarmbillerACH">
                        <b>AlarmBiller</b> ACH Export</button></td>
                        </tr>
                        <tr>
                        <td>
                        <img border="0" src="images/spacer.gif" width="20" height="10"></td>
                        </tr>
                        <tr>
                        <td>
                        <button class="button2" type="submit" name="sedonaOffice" value="sedonaOfficeCC">
                        <b>SedonaOffice</b> CC and ACH</button>&nbsp;</td>
                        </tr>
                        <tr>
                        <td>
                        <img border="0" src="images/spacer.gif" width="20" height="10"></td>
                        </tr>                        
                        <tr>
                        <td>
                        <button class="button2" type="submit" name="recpro_export" value="recpro_export">
                        <b>RecPro</b><br>CC and ACH</button>&nbsp;</td>
                        </tr>
                        <tr>
                        <td>
                        <img border="0" src="images/spacer.gif" width="20" height="10"></td>
                        </tr>
                        <tr>
                        <td>
                        <button class="button2" type="submit" name="myalarm_export" value="myalarm_export">
                        <b>MyAlarm</b><br>CC Only</button>&nbsp;</td>
                        </tr>
                        <tr>
                        <td>
                        <img border="0" src="images/spacer.gif" width="20" height="10"></td>
                        </tr -->				
                      <tr>
                        <td>
                          <img border="0" src="images/spacer.gif" width="20" height="20">
                        </td>
                      </tr>
                      <tr>
                        <td>Delete Stuff<br>
                          <button class="button5" type="submit" name="delete_customers" value="delete_customers">
                          Delete Customers</button>&nbsp;
                          <button class="button5" type="submit" name="delete_paymethods" value="delete_paymethods">
                          Delete Paymethods</button>&nbsp;
                          <button class="button5" type="submit" name="delete_schedules" value="delete_schedules">
                          Delete Schedules</button>&nbsp; 
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <img border="0" src="images/spacer.gif" width="20" height="10">
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <button class="button5" type="submit" name="delete_everything" value="delete_everything">
                          NUKE<br>
                          THE MID</button>&nbsp;
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <img border="0" src="images/spacer.gif" width="20" height="30">
                        </td>
                      </tr>
                    </table>
                  </div>
                </form>
              </div>
            </center>
            <img border="0" src="images/spacer.gif" width="20" height="25"><br>
            <u>General Notes</u>
            <ul class="importer">
              <li class="importer">All the files you need are in the /internal-toolbox/importer 
                folder.
              </li>
              <li class="importer">&quot;Check Contents&quot; checks the number of customers,  
                paymethods and schedules currently in the mid.
              </li>
              <li class="importer">&quot;Delete the Leftovers&quot; deletes the failure logs, token files and 
                export files. Do this before you get started.
              </li>
              <li class="importer">The file uploader doesn't like large files. I'm not sure what the limit is,
                but if it gives an error, save your dataset as "data.csv" in the /internal-toolbox/importer/uploads folder. (when doing single instance)
              <li class="importer">The import rate is 5.7 calls per second per instance. 
                Delete rate is about the same.
              </li>
            </ul>
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