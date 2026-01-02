<html>
<?php

//set some variables for the email... date, subject line, etc.
date_default_timezone_set('America/Chicago');
$heading_s_date = date("F j, Y",strtotime($start_date));
$heading_e_date = date("F j, Y",strtotime($end_date));
$subject_s_date = date("M j, Y",strtotime($start_date));
$subject_e_date = date("M j, Y",strtotime($end_date));
$subject = 'Account Updater Report for ' . $merchant_name . ' -- ' . $subject_s_date . ' to ' . $subject_e_date;; 
$random_hash = md5(date('r', time())); 
$headers = "From: customerservice@calligraphydallas.com\r\nReply-To: customerservice@forte.net"; 
$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 
$attachment = chunk_split(base64_encode(file_get_contents('files/'.$filename))); 
ob_start();
?>

<? //MIME statements for the body of the email ?>
--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>"  

<? //if plain text is all the recipient wants to see displayed ?>
--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/plain; charset="iso-8859-1"  
Content-Transfer-Encoding: 8bit  

Attached please find your Account Updater report.

For: <?php echo $merchant_name?>  
<?php echo $heading_s_date . ' to ' . $heading_e_date; ?>  
Created on: <?php echo date("F j, Y") . ' at ' . date("g:i a"); ?> CST  

Forte Customer Service:
     7:00 am - 7:00 pm CST
     866.290.5400 option 1
     customerservice@forte.net
     www.forte.net  

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/html; charset="iso-8859-1"  
Content-Transfer-Encoding: 8bit  

<? //if recipient has html emails enabled, display this ?>
<a href="https://www.forte.net/"><img border="0" src="http://www.calligraphydallas.com/updater/images/banner06.png" alt="Forte Payment Systems | www.forte.net" width="821" height="182"></a><br>
<p><font face="Calibri"><span style="font-size: 15pt;"><b>Attached please find your Account Updater report.</b></span></font></p>
<p><font face="Calibri"><span style="font-size: 12pt;">For: <?php echo $merchant_name?></span></font><br>
<font face="Calibri"><span style="font-size: 12pt;"><?php echo $heading_s_date . ' to ' . $heading_e_date; ?></span></font><br>
<font face="Calibri"><span style="font-size: 12pt;">Created on: <?php echo date("F j, Y") . ' at ' . date("g:i a"); ?> CST</span></font><br><br>
<font face="Calibri"><span style="font-size: 13pt;"><b>Forte Customer Service:</b></font><br>
<font face="Calibri"><span style="font-size: 12pt;">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;7:00 am - 7:00 pm CST<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;866.290.5400 option 1<br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:customerservice@forte.net">customerservice@forte.net</a><br>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.forte.net">www.forte.net</a></span></font></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><font style="font-size:10pt;" color="white">Created on: <?php echo date("F j, Y") . ' at ' . date("g:i a"); ?> CST</font></p>
<p>&nbsp;</p>
<p>&nbsp;</p>

--PHP-alt-<?php echo $random_hash; ?>--  

<? //MIME for the email attachment ?>
--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: application/text; name="<?php echo $filename; ?>"  
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

<?php echo $attachment; ?>  
--PHP-mixed-<?php echo $random_hash; ?>--  

<?php 
//mail that bad boy
$message = ob_get_clean(); 
$mail_sent = @mail( $sendto, $subject, $message, $headers ); 
?>
<html>
body {
  font-family: Arial, sans-serif;
  background: url(http://www.shukatsu-note.com/wp-content/importer/2014/12/computer-564136_1280.jpg) no-repeat;
  background-size: cover;
  height: 100vh;
}

h1 {
  text-align: center;
  font-family: Tahoma, Arial, sans-serif;
  color: #06D85F;
  margin: 80px 0;
}

.box {
  width: 40%;
  margin: 0 auto;
  background: rgba(255,255,255,0.2);
  padding: 35px;
  border: 2px solid #fff;
  border-radius: 20px/50px;
  background-clip: padding-box;
  text-align: center;
}

.button {
  font-size: 1em;
  padding: 10px;
  color: #fff;
  border: 2px solid #06D85F;
  border-radius: 20px/50px;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.3s ease-out;
}
.button:hover {
  background: #06D85F;
}

.overlay {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.7);
  transition: opacity 500ms;
  visibility: hidden;
  opacity: 0;
}
.overlay:target {
  visibility: visible;
  opacity: 1;
}

.popup {
  margin: 70px auto;
  padding: 20px;
  background: #fff;
  border-radius: 5px;
  width: 30%;
  position: relative;
  transition: all 5s ease-in-out;
}

.popup h2 {
  margin-top: 0;
  color: #333;
  font-family: Tahoma, Arial, sans-serif;
}
.popup .close {
  position: absolute;
  top: 20px;
  right: 30px;
  transition: all 200ms;
  font-size: 30px;
  font-weight: bold;
  text-decoration: none;
  color: #333;
}
.popup .close:hover {
  color: #06D85F;
}
.popup .content {
  max-height: 30%;
  overflow: auto;
}

@media screen and (max-width: 700px){
  .box{
    width: 70%;
  }
  .popup{
    width: 70%;
  }
}
<h1>Popup/Modal Windows without JavaScript</h1>
<div class="box">
	<a class="button" href="#popup1">Let me Pop up</a>
</div>

<div id="popup1" class="overlay">
	<div class="popup">
		<h2>Here i am</h2>
		<a class="close" href="#">&times;</a>
		<div class="content">
			Thank to pop me out of that button, but now i'm done so you can close this window.
		</div>
	</div>
</div>
</html>