<?php

// this script creates a multipart email message, and uses PHP mail function to send it including the report as an attachment.

$subject        = $filename;
$heading_s_date = date("n/j/Y",strtotime($start_date));
$heading_e_date = date("n/j/Y",strtotime($end_date));
$random_hash    = md5(date('r', time())); 
$attachment     = chunk_split(base64_encode(file_get_contents($filename))); 
$headers        = 'From: AU.Report@calligraphydallas.com' . "\r\n" .
                  'Reply-To: customerservice@forte.net' . "\r\n" .
                  'X-Mailer: PHP/' . phpversion() . "\r\n" .
		          'Content-Type: multipart/mixed; boundary=PHP-mixed-'.$random_hash;
ob_start();
?>
--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: multipart/related; boundary="PHP-rel-<?php echo $random_hash; ?>"  

--PHP-rel-<?php echo $random_hash; ?>  
Content-Type: multipart/alternative; boundary="PHP-alt-<?php echo $random_hash; ?>"  

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/plain; charset="iso-8859-1"  
Content-Transfer-Encoding: quoted-printable  

<?php echo $email_note; ?>  

Attached please find your Account Updater report.

Merchant ID: <?php echo $merchant_id; ?>  
Cards Updated From: <?php echo $heading_s_date . ' - ' . $heading_e_date; ?>  
Report Created On: <?php echo date("M j Y, g:i a T"); ?>  

Forte Customer Service:  
      7:00 am - 7:00 pm CST  
      866-290-5400  option 1  
      customerservice@forte.net  
      www.forte.net  

--PHP-alt-<?php echo $random_hash; ?>  
Content-Type: text/html; charset="iso-8859-1"  
Content-Transfer-Encoding: quoted-printable  

<span style="font-size:13pt; font-family:Book Antiqua;"><i><?php echo $email_note; ?></i></span><br><br>
<span style="font-size:13pt; font-family:Calibri;"><b>Attached please find your Account Updater report.</b></span><br><br>
<span style="font-size:12pt; font-family:Calibri;"><b>Merchant ID:&nbsp;&nbsp;</b><?php echo $merchant_id; ?></span><br>
<span style="font-size:12pt; font-family:Calibri;"><b>Cards Updated From:&nbsp;&nbsp;</b><?php echo $heading_s_date . ' - ' . $heading_e_date; ?></span><br>
<span style="font-size:12pt; font-family:Calibri;"><b>Report Created On:&nbsp;&nbsp;</b><?php echo date("M j Y, g:i a T"); ?></span><br><br>
<span style="font-size:13pt; font-family:Calibri;"><b>Forte Customer Service:</b></span><br>
<span style="font-size:12pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;7:00 am - 7:00 pm CST</span><br>
<span style="font-size:12pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;866-290-5400 &nbsp;option 1</span><br>
<span style="font-size:12pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:customerservice@forte.net">customerservice@forte.net</a></span><br>
<span style="font-size:12pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.forte.net/">www.forte.net</a></span>
<br><br>

--PHP-alt-<?php echo $random_hash; ?>--  

--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: application/text; name="<?php echo $filename; ?>"  
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

<?php echo $attachment; ?>  
--PHP-mixed-<?php echo $random_hash; ?>--  

<?php 
$message = ob_get_clean(); 
@mail( $sendto, $subject, $message, $headers ); 
?>