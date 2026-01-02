<?php

date_default_timezone_set('America/Chicago');
$heading_s_date = date("F j, Y",strtotime($start_date));  //to get format 2018-11-31 only
$heading_e_date = date("F j, Y",strtotime($end_date));    //to get format 2018-11-31 only
$subject = 'Account Updater Report for: ' . $merchant_name . '--' . $start_date . ' to ' . $end_date;; 
$random_hash = md5(date('r', time())); 
$headers = "From: customerservice@calligraphydallas.com\r\nReply-To: customerservice@forte.net"; 
$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 
$attachment = chunk_split(base64_encode(file_get_contents($filename))); 
ob_start();
?>
--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>" 

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/plain; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

Here is your Account Updater report. 

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/html; charset="iso-8859-1" 
Content-Transfer-Encoding: 7bit

<a href="https://www.forte.net/"><img border="0" src="http://www.calligraphydallas.com/updater/banner02.png" alt="Forte Payment Systems" width="804" height="166"></a><br>
<p><font face="Calibri"><span style="font-size: 15pt;"><b>Attached please find your Account Updater report.</b></span></font></p>
<p><font face="Calibri"><span style="font-size: 12pt;">For: <?php echo $merchant_name?></span></font><br>
<font face="Calibri"><span style="font-size: 12pt;"><?php echo $heading_s_date . ' to ' . $heading_e_date; ?></span></font><br>
<font face="Calibri"><span style="font-size: 12pt;">Created on: <?php echo date("F j, Y") . ' at ' . date("g:i a"); ?> CST</span></font><br><br>
<font face="Calibri"><span style="font-size: 13pt;"><b>Forte Customer Service:</b></span><br>
<span style="margin-left:50px; font-size:12pt;">7:00 am - 7:00 pm CST</span><br>
<span style="margin-left:50px; font-size:12pt;">866.290.5400 &nbsp;option 1</span><br>
<span style="margin-left:50px; font-size:12pt;">customerservice@forte.net</span></font><br>
<span style="margin-left:50px; font-size:12pt;"><a href="https://www.forte.net/">https://www.forte.net</a></span></font></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>

--PHP-alt-<?php echo $random_hash; ?>-- 

--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: application/text; name="<?php echo $filename; ?>"
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

<?php echo $attachment; ?> 
--PHP-mixed-<?php echo $random_hash; ?>-- 

<?php 
$message = ob_get_clean(); 
$mail_sent = @mail( $sendto, $subject, $message, $headers ); 
?>