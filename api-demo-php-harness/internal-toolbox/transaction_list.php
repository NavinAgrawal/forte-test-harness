<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">

<!-- Template Design by www.studio7designs.com. -->

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
<meta content="en-gb" http-equiv="Content-Language" />
<title>Transaction List</title>
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
        <p><span class="style4">Transaction List</span></p>
        <p>&nbsp;</p>
        <p>This form is for listing in sequential order all the transactions in a batch file that contains multiple batches.
        </p>
        <center><img border="0" src="images/spacer.gif" width="20" height="30">
        <br>
		
		
<div style="display:inline-block; border: 3px solid gray; padding:20px; background-color:#F9F9F9">
<form action="trans_list.php" method="post" enctype="multipart/form-data">
    Select Batch file: <input type="file" name="fileToUpload" id="fileToUpload"><br>
	<br>
	Choose File Type
	<select class="select" name="format">
		<option value="NACHA">NACHA</option>
		<option value="CSV">CSV</option>
		<option value="FIXED">FIXED</option>
	</select>
	<br>
	<br>
    <input class="button6" type="submit" value="Get Records" name="SubmitButton">
</form>
</div>




        </center><br>
        <p></p>
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
