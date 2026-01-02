<?php

$filename = "test.xlsx";
$filename2 = "AU.Report.pdf";
$inline = chunk_split(base64_encode(file_get_contents('banner010.jpg')));
$sep = sha1(date('r', time()));

$uid = md5(uniqid(time()));

$subject = "Sharewood Lija";
$mailto = "iveyjames1@sbcglobal.net";

$message = '<img src="cid:image_identifier" alt="SWLBanner" /><br><br>';
$message .="<div>html message</div>";

$header = "From: asdf <asdf@calligraphydallas.com>\r\n";
$header .= "Reply-To: asdf@calligraphydallas.com\r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-Type: multipart/related; boundary=\"".$uid."\"\r\n\r\n";

$header .= "This is a multi-part message in MIME format.\r\n";  

$header .= "--".$uid."\r\n";    
$header .= "Content-Type: text/html; charset=uft-8\r\n";
$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
$header .= $message."\r\n\r\n";

//image
$header .= "--".$uid."\r\n";
$header .= "Content-Type: image/jpg;\r\n";
$header .= "name=\"banner010.jpg\"\r\n";
$header .= "Content-Transfer-Encoding: base64\r\n";
$header .= "Content-ID: <image_identifier>\r\n";
$header .= "Content-Disposition: inline;\r\n";
$header .= "filename=\"banner010.jpg\"\r\n\r\n";

$header .= $inline."\r\n";

//cjenik
$file = "test.xlsx";
$file_size = filesize($file);
$handle = fopen($file, "r");
$content = fread($handle, $file_size);
fclose($handle);
$content = chunk_split(base64_encode($content));  
$name = basename($file);

$header .= "--".$uid."\r\n";
$header .= "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; name=\"".$filename."\"\r\n"; // use different content types here
$header .= "Content-Transfer-Encoding: base64\r\n";
$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
$header .= $content."\r\n\r\n";

//ponuda
$file2 = "AU.Report.pdf";
$file_size2 = filesize($file2);
$handle2 = fopen($file2, "r");
$content2 = fread($handle2, $file_size2);
fclose($handle2);
$content2 = chunk_split(base64_encode($content2));
$name2 = basename($file2);

$header .= "--".$uid."\r\n";
$header .= "Content-Type: application/pdf; name=\"".$filename2."\"\r\n"; // use different content types here
$header .= "Content-Transfer-Encoding: base64\r\n";
$header .= "Content-Disposition: attachment; filename=\"".$filename2."\"\r\n\r\n";
$header .= $content2."\r\n\r\n";
$header .= "--".$uid."--";



if (mail($mailto, $subject, $message, $header)) {
    echo "mail send ... OK"; // or use booleans here
} else {
    echo "mail send ... ERROR!";
}
?>