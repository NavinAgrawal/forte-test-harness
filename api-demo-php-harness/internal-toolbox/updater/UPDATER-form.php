<html>
<head>
<title>Account Updater Report</title>
<style type="text/css"> 
    body {
        font-family:Calibri;
        margin:30px 0px 0px 0px;
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
</head>
<body>
<center>
    <form method="POST" action="UPDATER-engine.php">
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
                    <tr><td align="right"><span style="font-size: 12pt">Company Name:&nbsp;</span></td><td><input type="text" name="company_name" value="(optional)" style="color: #696969; padding-left:4; width:230px; height:20px; font-size:11pt; font-family:Calibri;"></td><td>&nbsp;</td></tr>
                    <tr><td align="right"><span style="font-size: 12pt">Email To:&nbsp;</span></td><td><input type="text" name="email_address" value="(optional)" style="color: #696969; padding-left:4; width:230px; height:20px; font-size:11pt; font-family:Calibri;"></td><td>&nbsp;</td></tr>
                    <tr><td align="right" valign="top"><span style="font-size: 12pt;">Email Note:&nbsp;</span></td><td><textarea rows="2"  cols="2" name="email_note" style="overflow:auto; color: #696969; padding-left:5; width:230px; font-size:11pt; font-family:Calibri;">(optional)</textarea></td><td>&nbsp;</td></tr>
                </table>
                <table border="0" cellspacing="0" width="330">
                    <tr><td align="center"><span style="font-size: 6pt">&nbsp;</span></td></tr>
                    <tr><td align="center"><p style="font-size:12pt; text-align:justify">
                    This report uses REST calls to create a list of all the credit card updates made by 
                    <b><span style="color:#FF3300; font-size: 12pt; font-family:Calibri;">{</span><span style="font-size: 12pt; font-family:Calibri;">forte</span><span style="color:#FF3300; font-size: 12pt; font-family:Calibri;">}</span></b>'s  
                    Account Updater service during the selected date range.
                    <tr><td align="center"><span style="font-size: 4pt">&nbsp;</span></td></tr>
                </table>
                <table>
                    <tr><td align="center"><span style="font-size: 12pt; text-align:center; color:#000088">
                    <b>The PDF can take a few seconds.</b></span></td></tr>
                    <tr><td align="center"><span style="font-size: 6pt">&nbsp;</span></td></tr>
                </table>
                <table border="0" cellpadding="1" cellspacing="0">
                    <tr>
                      <td align="center">
                      <button type="submit" name="csv" value="csv">CSV file</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button type="submit" name="xml" value="xml">XML file</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <button type="submit" name="pdf" value="pdf">PDF file</button>
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
</body>
</html>