<html>

<?php
date_default_timezone_set('America/Chicago');
$now = new DateTime();
$prevMonth = $now->modify('previous month');
$prevMonthValue = $prevMonth->format('m');
$prevMonthDisplay = $prevMonth->format('F');
$lastDay = date("t", strtotime("last month"));
$year = $now->format('Y');
?>

<head>
<title>Account Updater Reports</title>
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
					<tr><td colspan="3" align="center"><font color="#000088" face="Cambria" style="font-size:16pt;"><b>Account Updater Reports</b></font></td></tr>
					<tr><td colspan="3"><p align="center"><font face="Times New Roman" color="#000088" style="font-size:12pt;"><b>----›§‹----</b></font></td></tr>
					<tr><td align="center">Start Date:</td><td>&nbsp;</td><td align="center">End Date:</td></tr>
					<tr>
					  <td>
						<select size="1" name="s_day" style="color: #696969; height:20px;">
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
						<select size="1" name="s_month" style="color: #696969; height:20px;">
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
						  <option value="12">December</option>
						  <option value="<?php echo $prevMonthValue; ?>" selected><?php echo $prevMonthDisplay; ?></option>
						</select>
						<select size="1" name="s_year" style="color: #696969; height:20px;">
						  <option>2017</option>
						  <option>2018</option>
						  <option>2019</option>
						  <option>2020</option>
						  <option>2021</option>
						  <option>2022</option>
						  <option selected><?php echo $year; ?></option>
						</select>
					  </td>
					  
					  <td align="center" width="40">to</td>
					  
					  <td>
						<select size="1" name="e_day" style="color: #696969; height:19px;">
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
						  <option>31</option>
						  <option selected><?php echo $lastDay; ?></option>
						</select>
						<select size="1" name="e_month" style="color: #696969; height:19px;">
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
						  <option value="12">December</option>
						  <option value="<?php echo $prevMonthValue; ?>" selected><?php echo $prevMonthDisplay; ?></option>
						</select>
						<select size="1" name="e_year" style="color: #696969; height:19px;">
						  <option>2017</option>
						  <option>2018</option>
						  <option>2019</option>
						  <option>2020</option>
						  <option>2021</option>
						  <option>2022</option>
						  <option selected><?php echo $year; ?></option>
						</select>
					  </td>
					</tr>
				</table>
				<table border="0" cellpadding="1" cellspacing="0">
					<tr><td colspan="2" align="center"><font style="font-size: 12pt">&nbsp;</font></td></tr>
					<tr><td align="right"><font style="font-size: 12pt">Organization ID:&nbsp;</font></td><td><input type="text" name="organization_id" style="color: #696969; padding-left:4; width:260px; height:19px; font-family:Consolas;">&nbsp;&nbsp;</td></tr>
					<tr><td align="right"><font style="font-size: 12pt">Location ID:&nbsp;</font></td><td><input type="text" name="location_id" style="color: #696969; padding-left:4; width:260px; height:19px; font-family:Consolas;">&nbsp;&nbsp;</td></tr>
					<tr><td align="right"><font style="font-size: 12pt">API Access ID:&nbsp;</font></td><td><input type="text" name="api_access_id" style="color: #696969; padding-left:4; width:260px; height:19px; font-family:Consolas;">&nbsp;&nbsp;</td></tr>
					<tr><td align="right"><font style="font-size: 12pt">API Secure Key:&nbsp;</font></td><td><input type="text" name="api_secure_key" style="color: #696969; padding-left:4; width:260px; height:19px; font-family:Consolas;">&nbsp;&nbsp;</td></tr>
				</table>
				<table border="0" cellspacing="0" width="360">
					<tr><td align="right"><span style="font-size: 8pt">&nbsp;</span></td></tr>
					<tr><td align="right"><p style="font-size:12pt; text-align:justify">This application uses REST calls to create a list of all the credit card updates made by Forte's Account Updater service during the selected date range.</td></tr>
					<tr><td align="center"><font style="font-size: 6pt">&nbsp;</font></td></tr>
					<!--tr><td align="right"><p style="font-size:12pt; text-align:justify">To reconcile with your Forte invoice, this report must be run on the 1st day of the month.</td></tr>
					<tr><td align="center"><font style="font-size: 6pt">&nbsp;</font></td></tr -->
				</table>
				<table>
					<tr><td align="center"><b><font style="font-size: 12pt; text-align:center; color:#000088">The PDF can take a few seconds.</font></b></td></tr>
					<tr><td align="center"><font style="font-size: 8pt">&nbsp;</font></td></tr>
				</table>
				<table border="0" cellpadding="1" cellspacing="0">
					<tr>
					  <td colspan="2" align="center">
						<button type="submit" name="csv" value="csv">CSV file</button>&nbsp;&nbsp;&nbsp;&nbsp;
						<button type="submit" name="xml" value="xml">XML file</button>&nbsp;&nbsp;&nbsp;&nbsp;
						<button type="submit" name="pdf" value="pdf">PDF file</button>
					  </td>
					</tr>
					<tr><td colspan="2"><span style="font-size: 10pt">&nbsp;</span></td></tr>
				</table>
				<table>
					<tr><td align="center"><font style="font-size: 12pt; text-align:center; color:#000088">(The XML file displays nicely in Excel)</font></td></tr>
					<tr><td align="center"><font style="font-size: 12pt">&nbsp;</font></td></tr>
				</table>
				<table width="400" align="center" border="0" cellpadding="0" cellspacing="0">
					<tr><td><p align="justify"><span style="font-size: 11pt; color:#000088;"><b>Disclaimer: </b>This report generator is provided without warranty and is not supported by or guaranteed by Forte Payment Systems.</span></p></td></tr>
					<!--tr><td align="center"><font style="font-size: 6pt">&nbsp;</font></td></tr>
					<tr><td align="right"><a href="https://www.forte.net"><img border="0" align="center" src="images/forte.logo.png" width="114" height="39"></a></td></tr -->
				</table -->
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