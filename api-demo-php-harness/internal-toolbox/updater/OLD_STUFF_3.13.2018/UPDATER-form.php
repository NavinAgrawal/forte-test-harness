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
	<table border="2" cellpadding="30" style="border-collapse: collapse;" bordercolor="#111111" bgcolor="#F3F3F3">
	<tr><td>
	<center>
	<table align="center" border="0" cellpadding="1" cellspacing="0">
		<form method="POST" action="UPDATER-dostuff.php">
			<tr><td colspan="3" align="center">
            <font color="#000088" face="Cambria" style="font-size:16pt;"><b>Account Updater Reports</b></font></td><tr>
			<td colspan="3">
			<p align="center"><font face="Times New Roman" color="#000088" style="font-size:12pt;"><b>----›§‹----</b></font></td></tr>
			<tr><td align="center">Start Date:</td><td>&nbsp;</td><td align="center">End Date:</td></tr>
			<tr><td>
			<select size="1" name="s_day">
			  <option selected>01</option>
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
			  <option>31</option>
			  </select>
			  <select size="1" name="s_month">
			  <option value="01">January</option>
			  <option value="02" selected>February</option>
			  <option value="03">March</option>
			  <option value="04">April</option>
			  <option value="05">May</option>
			  <option value="06">June</option>
			  <option value="07">July</option>
			  <option value="08">August</option>
			  <option value="09">September</option>
			  <option value="10">October</option>
			  <option value="11">November</option>
			  <option value="12">December</option>
			  </select>
			  <select size="1" name="s_year">
			  <option>2017</option>
			  <option selected>2018</option>
			  <option>2019</option>
			  <option>2020</option>		
			  <option>2021</option>		
			  <option>2022</option>		
			</td>

			<td align="center" width="40">to</td>
			
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
			  <option selected>28</option>
			  <option>29</option>
			  <option>30</option>
			  <option>31</option>
			  </select>
			  <select size="1" name="e_month">
			  <option value="01">January</option>
			  <option value="02" selected>February</option>
			  <option value="03">March</option>
			  <option value="04">April</option>
			  <option value="05">May</option>
			  <option value="06">June</option>
			  <option value="07">July</option>
			  <option value="08">August</option>
			  <option value="09">September</option>
			  <option value="10">October</option>
			  <option value="11">November</option>
			  <option value="12">December</option>
			  </select>
			  <select size="1" name="e_year">
			  <option>2017</option>
			  <option selected>2018</option>
			  <option>2019</option>
			  <option>2020</option>
			  <option>2021</option>
			  <option>2022</option>
			</td></tr>
			<tr><td colspan="3"><p align="center"><font style="font-size:10pt">&nbsp;</font></td></tr>
		</table>
		<table border="0" cellpadding="1" cellspacing="0">
			<!-- tr><td align="right"><font style="font-size: 12pt">Name or mid:&nbsp;</font></td><td><input type="text" name="merchant_name" style="color: #919191; padding-left:3; width:240px;" value="(anything you want)" onFocus="if(this.value==this.defaultValue) this.value=''; " onBlur="if(this.value=='') this.value=this.defaultValue;">&nbsp;&nbsp;</td></tr -->
			<tr><td align="right"><font style="font-size: 12pt">Organization ID*:&nbsp;</font></td><td><input type="text" size="32" name="organization_id" style="color: #919191; padding-left:3; width:240px;" value="123456" onFocus="if(this.value==this.defaultValue) this.value=''; " onBlur="if(this.value=='') this.value=this.defaultValue;">&nbsp;&nbsp;</td></tr>
			<tr><td align="right"><font style="font-size: 12pt">Location ID*:&nbsp;</font></td><td><input type="text" size="32" name="location_id" style="color: #919191; padding-left:3; width:240px;" value="654321" onFocus="if(this.value==this.defaultValue) this.value=''; " onBlur="if(this.value=='') this.value=this.defaultValue;">&nbsp;&nbsp;</td></tr>
			<tr><td align="right"><font style="font-size: 12pt">API Access ID*:&nbsp;</font></td><td><input type="text" size="32" name="api_access_id" style="color: #919191; padding-left:3; width:240px;" value="1111kkkk5555YYYY3333pppp8888" onFocus="if(this.value==this.defaultValue) this.value=''; " onBlur="if(this.value=='') this.value=this.defaultValue;">&nbsp;&nbsp;</td></tr>
			<tr><td align="right"><font style="font-size: 12pt">API Secure Key*:&nbsp;</font></td><td><input type="password" size="32" name="api_secure_key" style="color: #919191; padding-left:3; width:240px;" value="********************************************" onFocus="if(this.value==this.defaultValue) this.value=''; " onBlur="if(this.value=='') this.value=this.defaultValue;">&nbsp;&nbsp;</td></tr>
			<!-- tr><td align="right"><font style="font-size: 12pt">Email Address:&nbsp;</font></td><td><input type="text" size="32" name="sendto_email" style="color: #919191; padding-left:3; width:240px;" value="bill@forte.net, cathy@forte.net" onFocus="if(this.value==this.defaultValue) this.value=''; " onBlur="if(this.value=='') this.value=this.defaultValue;">&nbsp;&nbsp;</td></tr>
			<tr><td align="right" valign="top"><font style="font-size: 12pt">Message:&nbsp;</font></td><td><textarea rows="3" name="email_note" style="color: #919191; padding-left:3; width:240px;" value="Hi Bill. Here is the Updater report you asked for...." onFocus="if(this.value==this.defaultValue) this.value=''; " onBlur="if(this.value=='') this.value=this.defaultValue;">Hi Bill. Here is the Updater report you asked for.....</textarea></td></tr -->
		</table>
		<table border="0" cellspacing="0" width="350">
			<tr><td align="right"><span style="font-size: 8pt">&nbsp;</span></td></tr>
			<tr><td align="right"><p style="font-size:12pt; text-align:justify">This script uses REST calls to create a report of all the credit cards that were updated by Account Updater.<br>
            <span style="font-size: 6pt"><br></span>To reconcile with your Forte invoice, this report must be run on the 1st day of the month.<font style="font-size: 6pt">&nbsp;</font></td></tr>
		</table>
		<!-- table border="0" cellpadding="1" cellspacing="0">
		    <tr><td><span style="font-size: 6pt">&nbsp;</span></td></tr>
		    <tr><td><input type="radio" name="option" value="download"> Download the report</td></tr>
			<tr><td><input type="radio" name="option" value="email"> Email the report</td></tr>
			<tr><td><input type="radio" name="option" value="both"> Both</td></tr>
			<tr><td colspan="2"><span style="font-size: 10pt">&nbsp;</span></td></tr>
		</table -->		
		<table border="0" cellpadding="1" cellspacing="0">
			<tr><td colspan="2"><span style="font-size: 14pt">&nbsp;</span></td></tr>
			<tr><td colspan="2" align="center"><button type="submit" name="csv" value="csv">CSV file</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name="xml" value="xml">XML file</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" name="pdf" value="pdf">PDF file</button></td></tr>
			<tr><td colspan="2"><span style="font-size: 10pt">&nbsp;</span></td></tr>
		</table>
		</form>
		<!-- table width="350" align="center" border="0" cellpadding="0" cellspacing="0">
			<tr><td><span style="font-size: 11pt">*The XML file displays nicely in Excel. When prompted, select the option &quot;As an XML table&quot;.</span></td></tr>
		</table -->
	</table>
    </center>
</html>