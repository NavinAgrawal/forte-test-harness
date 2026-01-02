<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
  <!-- Template Design by www.studio7designs.com. -->
  <head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=windows-1252" />
    <meta content="en-gb" http-equiv="Content-Language" />
    <title>Multi-Instance Admin</title>
    <link href="../../favicon.ico" rel="SHORTCUT ICON" />
    <link href="../../style.css" type="text/css" rel="stylesheet" />
  </head>
  <!-- Begin Body -->
  <body>
    <center>
      <img border="0" src="../../images/spacer.gif" width="20" height="30">
      <br>
      <div align="center" style="width:480px; background-color:#F9F9F9; padding:10px 0px 10px 0px; border-style:solid; border-width:thick; border-color:gray;">
        <img border="0" src="../../images/spacer.gif" width="20" height="20">
        <center><font color="0A1495" style="font-family:Algerian; font-size:22pt">MULTI-INSTANCE ADMIN</font></CENTER>
        <BR>
		<!--a class="link" target="_blank" style="font-size:13pt" href="importer.directions.php">
        How to Use the Importer tool</a><br-->
        <!--img border="0" src="../../images/spacer.gif" width="20" height="10"><br-->
		Save your data in the /internal-toolbox/importer/ folder<br>as "dataset.CC.csv" or "dataset.ACH.csv".<br>
        <img border="0" src="../../images/spacer.gif" width="20" height="20"><br>
        <form align="center" style="text-align:center;" action="IMPORT-splitter.CC.php" method="post">
          Number of instances:
          <input type="text" style="width:20px" name="splitter_CC" value="">&nbsp;
          <input class="button6" type="submit" name="splitterCC" value="Splitter CC"></p>
        </form>		
        <img border="0" src="../../images/spacer.gif" width="20" height="6"><br>
        <form align="center" style="text-align:center;" action="IMPORT-splitter.ACH.php" method="post">
          Number of instances:
          <input type="text" style="width:20px" name="splitter_ACH" value="">&nbsp;
          <input class="button6" type="submit" name="splitterACH" value="Splitter ACH"></p>
        </form>	
        <img border="0" src="../../images/spacer.gif" width="20" height="6">
        <form align="center" style="text-align:center;" action="IMPORT-combiner.CC.php" method="post">
          Number of instances:
          <input type="text" style="width:20px" name="combinerCC" value="">&nbsp;
          <input class="button6" type="submit" value="Combiner CC" name="CC Combiner"></p>
        </form>
        <img border="0" src="../../images/spacer.gif" width="20" height="6">
        <form align="center" style="text-align:center;" action="IMPORT-combiner.ACH.php" method="post">
          Number of instances:
          <input type="text" style="width:20px" name="combinerACH" value="">&nbsp;
          <input class="button6" type="submit" value="Combiner ACH" name="ACH Combiner"></p>
        </form>
        <img border="0" src="../../images/spacer.gif" width="20" height="30"><br>
        <a class="link" target="_blank" style="font-family:Algerian; font-size:16pt" href="../../../internal-toolbox/importer.php">
        TOOLBOX 1</a><br><br>
		<a class="link" target="_blank" style="font-family:Algerian; font-size:16pt" href="../../../toolbox2/importer.php">
        TOOLBOX 2</a><br><br>
        <a class="link" target="_blank" style="font-family:Algerian; font-size:16pt" href="../../../toolbox3/importer.php">
        TOOLBOX 3</a><br><br>
        <a class="link" target="_blank" style="font-family:Algerian; font-size:16pt" href="../../../toolbox4/importer.php">
        TOOLBOX 4</a><br><br>
        <a class="link" target="_blank" style="font-family:Algerian; font-size:16pt" href="../../../toolbox5/importer.php">
        TOOLBOX 5</a><br><br>
        <a class="link" target="_blank" style="font-family:Algerian; font-size:16pt" href="../../../toolbox6/importer.php">
        TOOLBOX 6</a><br><br>
        <a class="link" target="_blank" style="font-family:Algerian; font-size:16pt" href="../../../toolbox7/importer.php">
        TOOLBOX 7</a><br><br>
        <a class="link" target="_blank" style="font-family:Algerian; font-size:16pt" href="../../../toolbox8/importer.php">
        TOOLBOX 8</a><br><br>
        <a class="link" target="_blank" style="font-family:Algerian; font-size:16pt" href="../../../toolbox9/importer.php">
        TOOLBOX 9</a><br>
        <img border="0" src="../../images/spacer.gif" width="20" height="30"><br>
      </div>
    </center>
  </body>
</html>