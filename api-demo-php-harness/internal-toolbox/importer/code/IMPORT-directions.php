<html>
  <head>
    <meta http-equiv="Content-Language" content="en-us">
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
    <link href="favicon.ico" rel="SHORTCUT ICON" />
    <title>Directions</title>
  </head>
  <style> 
  body { 
  background-image: url('../../images/bg02.gif'); 
  background-repeat: repeat-x; 
  margin-top:30px 
  }
  ol {font-family:calibri;}
  </style>
  <body>
    <div align="center">
      <center>
        <table border="4" bordercolor="gray" bordercolor="#111111" bgcolor="white" width="750" cellspacing="0">
          <tr>
            <td style="padding:50 80  50 80; text-align:left;">
              <div style="position:relative; left:460px; top:-10px"> <font face="Trebuchet MS" style="font-size: 11pt"> <input type="button" style="width:105px; height:30px" onClick="window.print()" value="Print This Page"></font></div>
              <center><font face="Calibri" size="5">How to use the Importer tool</font></center>
              <p><font face="Calibri" size="4"> <a class="link" target="_blank" href="../sample_data/Template.xlsx"> Excel Template</a> - <i><font style="color:red; font-family:Book Antiqua; font-size:13.5pt;">The import file must be in this format, columns in the same order, without the header row. Do not add or delete any columns.</font></i></font></p>
              <p><font face="Calibri" size="4"><u>Directions</u></font></p>
              <ol class="importer">
                <li class="importer"><font face="Calibri" size="4">Clean the raw data and copy-paste it into the Excel template.</font></li>
                <li class="importer"><font face="Calibri" size="4">Without the header row, save your file as CSV.</font></li>
                <li class="importer"><font face="Calibri" size="4">Run "Delete the Leftovers".</font></li>
                <li class="importer"><font face="Calibri" size="4">Upload your data file.</font></li>
                <li class="importer"><font face="Calibri" size="4">Import the data into an empty, live mid.</font></li>
                <li class="importer"><font face="Calibri" size="4">Use the failure log file to fix the errors in the failure data file.</font></li>
                <li class="importer"><font face="Calibri" size="4">Import the failures.</font></li>
                <li class="importer"><font face="Calibri" size="4">Save the tokens file (find it in the /internal-toolbox/importer/ folder.)</font></li>
                <li class="importer"><font style="color:red" face="Calibri" size="4"> Do a Data Explorer APM export <b><u>just in case</u></b>. Save it.</font></li>
                <li class="importer"><font face="Calibri" size="4">Move the data to the target mid using the DX job &quot;Transfer Client Data&quot;. </font></li>
              </ol>
              <p><font face="Calibri" size="4"><u>Files</u> </font></p>
              <ul class="importer">
                <li class="importer"><font face="Calibri" size="4"><b>"data.CC.csv"</b> and <b>"data.ACH.csv"</b> -- the customer records to be imported.</font></li>
                <li class="importer"><font face="Calibri" size="4"><b>"tokens.CC.csv"</b> and <b>"tokens.ACH.csv"</b> -- the tokens file including customer data.</font></li>
                <li class="importer"><font face="Calibri" size="4"><b>"dataset.CC.csv"</b> and <b>"dataset.ACH.csv"</b> -- the dataset for the Multi-Instance file splitter.</font></li>
                <li class="importer"><font face="Calibri" size="4"><b>"failure.CC.data.csv"</b> and <b>"failure.ACH.data.csv"</b> -- the failure data files.</font></li>
                <li class="importer"><font face="Calibri" size="4"><b>"failure.CC.log.txt"</b> and <b>"failure.ACH.log.txt"</b> -- the failure error messages.</font></li>
                <li class="importer"><font face="Calibri" size="4"><b>"undo.import.CC.csv"</b> and <b>"undo.import.ACH.csv"</b> -- the file needed to Undo the Import.</font></li>
                <!--li class="importer"><font face="Calibri" size="4"><b>"undo.import.CC.OLD.csv"</b> and <b>"undo.import.ACH.OLD.csv"</b> -- the Undo Import file from the previous import.</font></li -->
                <li class="importer"><font face="Calibri" size="4"><b>"combined.tokens.CC.csv"</b> and <b>"combined.tokens.ACH.csv"</b> -- the tokens file created by the Multi-Instance combiner.</font></li>
                <!--li class="importer"><font face="Calibri" size="4"><b>"combined.tokens.CC.OLD.csv"</b> and <b>"combined.tokens.ACH.OLD.csv"</b> -- the combined tokens file from the previous import.</font></li -->
              </ul>			 
              <p><font face="Calibri" size="4"><u>Multiple Instances</u></font></p>
              <ul class="importer">
                <li class="importer"><font face="Calibri" size="4">Save the dataset from Excel as &quot;dataset.CC.csv&quot; or &quot;dataset.ACH.csv&quot; into the /internal-toolbox/importer/ folder.</font></li>
                <li class="importer"><font face="Calibri" size="4">Firefox works better than Chrome for multiple instances.</font></li>
                <li class="importer"><font face="Calibri" size="4">If you have any failures, you must fix them in the instance folder in which they occurred.</font></li>
                <li class="importer"><font face="Calibri" size="4">If you are doing a single instance, use the file uploader. If multiple instances, use the file splitter.</font></li>
              </ul>
              <p><font face="Calibri" size="4"><u>Notes</u></font></p>
              <ul class="importer">
                <li class="importer"><font face="Calibri" size="4">All the files you need are in the /internal-toolbox/importer/ folder. </font></li>
                <li class="importer"><font face="Calibri" size="4">If the file uploader throws an error, save your dataset as &quot;data.CC.csv&quot; or &quot;data.ACH.csv&quot; in the /internal-toolbox/importer/ folder.</font></li>
                <li class="importer"><font face="Calibri" size="4">The import rate is 5.7 calls per second per instance. Delete rate is about the same.</font></li>
              </ul>
            </td>
          </tr>
        </table>
      </center>
    </div>
  </body>
</html>