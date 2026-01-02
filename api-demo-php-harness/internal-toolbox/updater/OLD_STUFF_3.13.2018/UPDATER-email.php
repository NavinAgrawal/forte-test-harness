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