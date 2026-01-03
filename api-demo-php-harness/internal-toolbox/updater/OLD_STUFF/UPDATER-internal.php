<html>
	<head>
    <style>
		body { font-family:Calibri; margin:20 0 0 0; }
		table tr td { font-size:12pt; }
		form { font-size:12pt; }
	</style>
	</head>
	<body>
	<center>
	<br>
	<table border="2" cellpadding="30" style="border-collapse: collapse;" bordercolor="#111111" bgcolor="#F3F3F3">
	<tr><td>
	<center>
	<table align="center" border="0" cellpadding="1" cellspacing="0">
	<form method="POST" action="UPDATER-reports.internal.php">
		<tr><td colspan="3" align="center"><font style="font-size:16pt"><b>Account Updater Report Generator</b></font><br>
		  <!--b><font color="#FF5820">
          <span style="font-size: 16pt; letter-spacing: 1pt">{</span></font><span style="font-size: 14pt">forte</span><font color="#FF5820"><span style="font-size: 16pt; letter-spacing: 1pt">}</span></font>
          </b><span style="font-size: 13pt"> INTERNAL</span></td></tr-->
		<tr><td colspan="3"><font style="font-size:6pt">&nbsp;</font></td></tr>
		<tr><td align="center">Start Date:</td><td>&nbsp;</td><td align="center">End Date:</td></tr>
		<tr><td>
		<select size="1" name="s_day">
		  <option>01</option>
		  <option>02</option>
		  <option>03</option>
		  <option>04</option>
		  <option selected>05</option>
		  <option>06</option>
		  <option>07</option>
		  <option>08</option>
		  <option>09</option>
		  <option>10</option>
		  <option>11</option>
		  <option>12</option>
		  <option>13</option>
		  <option>14</option>
		  <option>15</option>
		  <option>16</option>
		  <option>17</option>
		  <option>18</option>
		  <option>19</option>
		  <option>20</option>
		  <option>21</option>
		  <option>22</option>
		  <option>23</option>
		  <option>24</option>
		  <option>25</option>
		  <option>26</option>
		  <option>27</option>
		  <option>28</option>
		  <option>29</option>
		  <option>30</option>
		  <option>31</option>
		  </select> <select size="1" name="s_month">
		  <option value="01">January</option>
		  <option value="02">February</option>
		  <option value="03">March</option>
		  <option value="04">April</option>
		  <option value="05">May</option>
		  <option value="06">June</option>
		  <option value="07">July</option>
		  <option value="08">August</option>
		  <option value="09">September</option>
		  <option value="10">October</option>
		  <option value="11">November</option>
		  <option value="12" selected>December</option>
		  </select> <select size="1" name="s_year">
		  <option selected>2017</option>
		  <option>2018</option>
		  <option>2019</option>
		  <option>2020</option>		
		</td>

		<td align="center" width="50">to</td>
		
		<td>
		<select size="1" name="e_day">
		  <option>01</option>
		  <option>02</option>
		  <option>03</option>
		  <option>04</option>
		  <option>05</option>
		  <option>06</option>
		  <option>07</option>
		  <option>08</option>
		  <option>09</option>
		  <option>10</option>
		  <option>11</option>
		  <option>12</option>
		  <option>13</option>
		  <option>14</option>
		  <option>15</option>
		  <option>16</option>
		  <option>17</option>
		  <option>18</option>
		  <option>19</option>
		  <option>20</option>
		  <option>21</option>
		  <option>22</option>
		  <option>23</option>
		  <option>24</option>
		  <option>25</option>
		  <option>26</option>
		  <option>27</option>
		  <option>28</option>
		  <option>29</option>
		  <option>30</option>
		  <option selected>31</option>
		  </select> <select size="1" name="e_month">
		  <option value="01">January</option>
		  <option value="02">February</option>
		  <option value="03">March</option>
		  <option value="04">April</option>
		  <option value="05">May</option>
		  <option value="06">June</option>
		  <option value="07">July</option>
		  <option value="08">August</option>
		  <option value="09">September</option>
		  <option value="10">October</option>
		  <option value="11">November</option>
		  <option value="12" selected>December</option>
		  </select> <select size="1" name="e_year">
		  <option selected>2017</option>
		  <option>2018</option>
		  <option>2019</option>
		  <option>2020</option>
		</td></tr>
		<tr><td colspan="3"><p align="center"><span style="font-size: 9pt">&nbsp;</span></td></tr>
	</table>
	<table border="0" cellpadding="1" cellspacing="0">
		<tr><td align="right"><font style="font-size: 12pt">Name or mid:&nbsp;</font></td><td><input type="text" size="32" name="merchant_name"></td></tr>
		<tr><td align="right"><font style="font-size: 12pt">Organization ID:&nbsp;</font></td><td><input type="text" size="32" name="organization_id"></td></tr>
		<tr><td align="right"><font style="font-size: 12pt">Location ID:&nbsp;</font></td><td><input type="text" size="32" name="location_id"></td></tr>
		<tr><td align="right"><font style="font-size: 12pt">API Access ID:&nbsp;</font></td><td><input type="text" size="32" name="api_access_id"></td></tr>
		<tr><td align="right"><font style="font-size: 12pt">API Secure Key:&nbsp;</font></td><td><input type="text" size="32" name="api_secure_key"></td></tr>
		<tr><td align="right"><font style="font-size: 12pt">Email Address:&nbsp;</font></td><td><input type="text" size="32" name="sendto_email"></td></tr>
		</table>
		<table border="0" cellspacing="0" width="380">
		<tr><td align="right"><span style="font-size: 8pt">&nbsp;</span></td></tr>
		<tr><td align="right"><p style="font-size:12pt; text-align:justify">This script uses REST calls to create a list of all the credit cards that were updated by Account Updater between the dates above 
          and emails it to the address above.<br>
		<font style="font-size: 8pt">&nbsp;</font></td></tr>
		<tr><td align="center"><b><font style="font-size: 12pt; text-align:center; color:#000088">The PDF file can take 
          up to a minute if you<br>
          are working on a month's worth of Gexa.</font></b></td></tr>
		<tr><td align="center"><font style="font-size: 12pt">&nbsp;</font></td></tr>
	</table>
	<table border="0" cellpadding="1" cellspacing="0">
		<tr><td colspan="2" align="center"><button type="submit" name="csv" value="csv">CSV File</button>&nbsp;&nbsp;&nbsp;<button type="submit" name="xml" value="xml">XML File</button>&nbsp;&nbsp;&nbsp;<button type="submit" name="pdf" value="pdf">PDF File</button>&nbsp;&nbsp;&nbsp;<button type="submit" name="webpage" value="webpage">Web Page</button></td></tr>
		<tr><td colspan="2"><p align="center"><font style="font-size:9pt">&nbsp;</font></td></tr>
	</table>
	</form>
	</table>
    </center>
</html>