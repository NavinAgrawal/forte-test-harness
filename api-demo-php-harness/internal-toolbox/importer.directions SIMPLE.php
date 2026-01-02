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
    <center>
<div align="center">
  <center>
  <table border="10" bgcolor="white" bordercolor="gray" style="border-collapse: collapse" width="700" cellspacing="0">
    <tr>
      <td style="padding:80 60  50 60; text-align:left;">
      <div style="position:relative; left:460px; top:-40px">
        <font face="Trebuchet MS" style="font-size: 11pt">
        <input type="button" style="width:105px" onClick="window.print()" value="Print This Page"></font>
      </div>	  
	  <center><font face="Calibri" size="5">How to use the Importer tool</font></center>
      <p><font face="Calibri" size="4">
      <a class="link" target="_blank" href="importer/sample_data/Template.xlsx">
      Excel Template</a> - <i><font style="color:red; ">The import file must be 
      in this format, columns in the same order, without the header row. Do not 
      add or delete any columns.</font></i> </font></p>
      <p><font face="Calibri" size="4"><u>Directions</u> </font></p>
      <ol class="importer">
        <li class="importer"><font face="Calibri" size="4">Massage the raw data 
        and copy-paste it into the Excel template.</font></li>
        <li class="importer"><font face="Calibri" size="4"><u>Without the header 
        row</u>, save your file as CSV. (anywhere you want and any name you want)</font></li>
        <li class="importer"><font face="Calibri" size="4">In the Toolbox UI, run 
        &quot;Delete the Leftovers&quot; to get rid of any leftover files from the last import.
        <font style="color:red"><i><u>Do not forget to do this</u>,</i></font> otherwise you 
		run the risk of getting a jacked up tokens file. </font></li>
        <li class="importer"><font face="Calibri" size="4">Upload your data file.</font></li>
        <li class="importer"><font face="Calibri" size="4">Import the data into 
        an empty, live mid.</font></li>
        <li class="importer"><font face="Calibri" size="4">If there are failures, 
        open the failure log file and failure data file in Notepad++. Find these 
        files in the /toolbox/importer folder. </font></li>
        <li class="importer"><font face="Calibri" size="4">Use the failure log file 
        to fix the errors in the failure data file.</font></li>
        <li class="importer"><font face="Calibri" size="4">Import the failure data 
        file.</font></li>
        <li class="importer"><font face="Calibri" size="4">Grab the token file from 
        the /toolbox/importer folder and save it.</font></li>
        <li class="importer"><font style="color:red" face="Calibri" size="4">
        Do a Data Explorer APM export <b><u>just in case</u></b>. Save it.</font></li>
        <li class="importer"><font face="Calibri" size="4">Lastly, move the data 
        to the target mid using the DX job &quot;Transfer Client Data&quot;. </font></li>
      </ol>
      <p><font face="Calibri" size="4"><u>Multiple Instances</u> </font></p>
      <ul class="importer">
        <li class="importer"><font face="Calibri" size="4">Use Toolbox 1 to manage
		multiple instances. That is where the splitter and the combiner live.</font></li>
        <li class="importer"><font face="Calibri" size="4">Chrome limit is 6 instances, 
        Firefox lets you do 9.</font></li>
        <li class="importer"><font face="Calibri" size="4">Firefox is actually better 
        than Chrome for multiple instances.</font></li>
        <li class="importer"><font face="Calibri" size="4">If you have any failures, 
        you must fix them in the instance folder in which they occurred.</font></li>
        <li class="importer"><font face="Calibri" size="4">If you are doing a single 
        instance, use the file uploader. If multiple instances, use the splitter.</font></li>
        <li class="importer"><font face="Calibri" size="4">For multiple instances, 
        you must save the dataset from Excel as &quot;dataset.csv&quot; into the /toolbox/importer/code 
        folder.</font></li>
        <li class="importer"><font face="Calibri" size="4">Run &quot;Delete the Leftovers&quot; 
        in <u>every</u> instance.</font></li>
      </ul>
      <p><font face="Calibri" size="4"><u>General Notes</u> </font></p>
      <ul class="importer">
        <li class="importer"><font face="Calibri" size="4">All the files you need 
        are in the /toolbox/importer folder. </font></li>
        <li class="importer"><font face="Calibri" size="4">&quot;Check Contents&quot; checks 
        the number of customers, paymethods and schedules currently in the mid.
        </font></li>
        <li class="importer"><font face="Calibri" size="4">&quot;Delete the Leftovers&quot; 
        deletes the failure logs, token files and export files. Do this before you 
        get started. </font></li>
        <li class="importer"><font face="Calibri" size="4">The file uploader doesn&#39;t 
        like large files. I&#39;m not sure what the limit is, but if it gives an error, 
        save your dataset as &quot;data.csv&quot; in the /toolbox/importer/uploads folder. 
        (applies only when doing single instance) </font></li>
        <li class="importer"><font face="Calibri" size="4">The import rate is 5.7 
        calls per second per instance. Delete rate is about the same.</font></li>
      </ul>
	  <!--p align="right"><font face="Calibri" size="4">May 27, 2019</p -->
      </td>
    </tr>
  </table>
  </center>
</div>
    </center>
  </body>
</html>