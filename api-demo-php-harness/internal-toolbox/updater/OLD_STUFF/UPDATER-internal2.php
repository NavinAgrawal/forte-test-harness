<html>
	<head>
	<script language="javascript" src="calendar/calendar.js"></script>
    <style>
		body { font-family:Calibri; }
		table tr td { font-size:13pt; }
		form { font-size:12pt; }
	</style>
	</head>
	<?php require_once('calendar/classes/tc_calendar.php'); ?>
	<center>
	<br>
	<table border="2" cellpadding="30" style="border-collapse: collapse;" bordercolor="#111111" bgcolor="#F3F3F3">
	<tr><td>
	<center>
	<table align="center" border="0" cellpadding="1" cellspacing="0">
	<form method="POST" action="UPDATER-reports.php">
		<tr><td colspan="3">
        <p align="center"><font style="font-size:16pt"><b>Account Updater Report Generator</b></font><br>
		All Your Updates Are Belong To Us.</p></td></tr>
		<tr><td colspan="3"><font style="font-size:10px">&nbsp;</font></td></tr>
		<tr><td align="center">Start Date:</td><td></td><td align="center">End Date:</td></tr>
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
		  </select> <select size="1" name="s_month">
		  <option value="01" selected>January</option>
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
		  </select> <select size="1" name="s_year">
		  <option>2017</option>
		  <option selected>2018</option>
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
		  <option value="01" selected>January</option>
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
		  </select> <select size="1" name="e_year">
		  <option>2017</option>
		  <option selected>2018</option>
		  <option>2019</option>
		  <option>2020</option>		
		</td></tr>
		<tr><td colspan="3">&nbsp;</td></tr>
	</table>
	<table border="0" cellpadding="1" cellspacing="0">
		<tr><td align="right">Organization ID:&nbsp;</td><td><input type="text" size="32" name="organization_id"></td></tr>
		<tr><td align="right">Location ID:&nbsp;</td><td><input type="text" size="32" name="location_id"></td></tr>
		<tr><td align="right">API Access ID:&nbsp;</td><td><input type="text" size="32" name="api_access_id"></td></tr>
		<tr><td align="right">API Secure Key:&nbsp;</td><td><input type="text" size="32" name="api_secure_key"></td></tr>
		<tr><td colspan="2" align="center"><span style="font-size:14px"><font>&nbsp;</font></span></td></tr>
		<tr><td colspan="2" align="center"><button type="submit" name="csv" value="CSV File">CSV File</button>&nbsp;&nbsp;&nbsp;<button type="submit" name="xml" value="XML File">XML File</button>&nbsp;&nbsp;&nbsp;<button type="submit" name="webpage" value="webpage File">Web Page</button></td></tr>
		<tr><td colspan="3"><font style="font-size:8px">&nbsp;</font></td></tr>
	</form>
	</table>
	</td></tr>
	</table>
    </center>
</html>