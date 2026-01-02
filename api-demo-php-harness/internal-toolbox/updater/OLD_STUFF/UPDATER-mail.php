<?php
$to = 'james.ivey@forte.net'; 
$subject = 'Account Updater Report for: ' . $merchant_name . ' -- ' . $start_date . ' to ' . $end_date;; 
$random_hash = md5(date('r', time())); 
$headers = "From: info@calligraphydallas.com\r\nReply-To: info@calligraphydallas.com"; 
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

<h2>Dude. Here is your Account Updater report.</h2> 
<p>For: <?php echo $merchant_name . ' -- from ' . $start_date . ' to ' . $end_date; ?></p> 

--PHP-alt-<?php echo $random_hash; ?>-- 

--PHP-mixed-<?php echo $random_hash; ?>  
Content-Type: application/text; name="<?php echo $filename; ?>"
Content-Transfer-Encoding: base64  
Content-Disposition: attachment  

<?php echo $attachment; ?> 
--PHP-mixed-<?php echo $random_hash; ?>-- 

<?php 
$message = ob_get_clean(); 
$mail_sent = @mail( $to, $subject, $message, $headers ); 
echo '<br><br><center>';
echo $mail_sent ? "Check your email, Brohlms.<p><p><a href=\"http://www.calligraphydallas.com/stuff/REST-updater.form.me.php\">Back to the form</a></p>" : "Mail failed"; 
?>