<?php

date_default_timezone_set('America/Chicago');
$heading_s_date = date("F j, Y",strtotime($start_date));  //to get format 2018-11-31 only
$heading_e_date = date("F j, Y",strtotime($end_date));    //to get format 2018-11-31 only
$subject = 'Account Updater Report for: ' . $merchant_name . '--' . $start_date . ' to ' . $end_date;;
$random_hash = md5(date('r', time()));
$headers = "From: customerservice@calligraphydallas.com\r\nReply-To: customerservice@forte.net";
$headers .= "\r\nContent-Type: multipart/mixed; boundary=\"------------3E63476E002B0B7838725D46"\"";
//$image = 'banner010.jpg';
//$attachment = chunk_split(base64_encode(file_get_contents($filename)));
//$banner = chunk_split(base64_encode(file_get_contents($image)));
ob_start();
?>

This is a multi-part message in MIME format.
--------------3E63476E002B0B7838725D46
Content-Type: multipart/alternative;
 boundary="------------6B230407148A1F2D7258034B"


--------------6B230407148A1F2D7258034B
Content-Type: text/plain; charset=utf-8; format=flowed
Content-Transfer-Encoding: 7bit


here is your account updater report.

--------------6B230407148A1F2D7258034B
Content-Type: multipart/related;
 boundary="------------B7D996C5C5CA86324A907002"


--------------B7D996C5C5CA86324A907002
Content-Type: text/html; charset=utf-8
Content-Transfer-Encoding: 7bit

<html>
  <head>

    <meta http-equiv="content-type" content="text/html; charset=utf-8">
  </head>
  <body text="#000000" bgcolor="#FFFFFF">
    <img moz-do-not-send="false"
      src="cid:part1.FEC17194.4EE558D7@sbcglobal.net" alt="" height="42"
      width="115"><br>
    here is your account updater report.<br>
  </body>
</html>

--------------B7D996C5C5CA86324A907002
Content-Type: image/jpeg;
 name="logo04.jpg"
Content-Transfer-Encoding: base64
Content-ID: <part1.FEC17194.4EE558D7@sbcglobal.net>
Content-Disposition: inline;
 filename="logo04.jpg"

/9j/4AAQSkZJRgABAgAAZABkAAD/7AARRHVja3kAAQAEAAAAUAAA/+4AJkFkb2JlAGTAAAAA
AQMAFQQDBgoNAAAEOwAACBoAAArgAAAOjv/bAIQAAgICAgICAgICAgMCAgIDBAMCAgMEBQQE
BAQEBQYFBQUFBQUGBgcHCAcHBgkJCgoJCQwMDAwMDAwMDAwMDAwMDAEDAwMFBAUJBgYJDQsJ
Cw0PDg4ODg8PDAwMDAwPDwwMDAwMDA8MDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwM/8IA
EQgAKgBzAwERAAIRAQMRAf/EANoAAAEEAwEBAAAAAAAAAAAAAAcABQYIAQIEAwkBAQADAQEB
AAAAAAAAAAAAAAAEBQYDAQIQAAEEAgECBgIDAAAAAAAAAAUCAwQGAAEwERIQMTITIwczFDQV
NhEAAgECBAIIBAQFBQAAAAAAAQIDEQQAMRIFIQYQQVGRIjITFGGBQlJxoTQHMLHBM0OCU3QV
FhIAAQIEBQUBAAAAAAAAAAAAAQACEDAREiFBUWEiMXGBwRPREwEAAQMDAwQCAwEAAAAAAAAB
EQAhMUFRYRAwcfCBkaGxwdHh8SD/2gAMAwEAAhEDEQAAAbo1lixxZpHvKHb0hCMCMiMAby+q
K2izXf25DagvpRZVrXNh1PLqm5sYK5llBpHkGWe0M1t6d4lRgnk9YVtHmwHJjgQdTlGIsQVW
CaBk+ix701zzfH2RLuiH1HePs6HBp8AQEZGs8y/R89yElhQ6nFnNHP7ujc+/AfUt15fPSbXt
DqMR0noSEhxICDBJAlktfO9HmX2VGdyOQpsimwthCEIQhGHkdgznWbC7fX//2gAIAQEAAQUC
MkNjYIhkw4riJLOCVRXkyY+Wlv3BIt33h1hLJCBqHBPHHH5UWIhmQxJQp9lK1PspVUbpKPGO
5Pa0SHvu5bHOwUPb9qDljlzN7hNtMxPs8HFJVyogAoqqVimu3lmyVyT9bLvRCTPtg36zHR4t
KpAewl7I9LOHzIysSJNWjHIgaxsxnhgGaQktZZ1JQIDo2gXfv8bVBWzn1RRrtCr8G8Wpq54Y
HIG37KiaHVK33kdF/vh91+uKwIqxxyxB7cnexkNWlxMssV2UMDG4ktMqNHmsRIcSBHJAAhjB
wYSHQ6KGSJuEa8DLr3Eiqis06qsP+WjxiPNZSt0dC/Z30zX8zijfmd9Of//aAAgBAgABBQKU
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
/9k=
--------------B7D996C5C5CA86324A907002--

--------------6B230407148A1F2D7258034B--

--------------3E63476E002B0B7838725D46
Content-Type: application/vnd.ms-excel;
 name="AU.Report.csv"
Content-Transfer-Encoding: base64
Content-Disposition: attachment;
 filename="AU.Report.csv"

TUlELE5hbWVfb25fQ2FyZCxDYXJkX1R5cGUsTGFzdF80LE1vLFllYXIsVXBkYXRlZCxBVV9D
b2RlLEFVX0Rlc2NyaXB0aW9uLFBheW1ldGhvZF9Ub2tlbixDdXN0b21lcl9Ub2tlbg0KMjA2
NzgzLFNhbmRyYSBNY011cnBoeSx2aXNhLCoqKio3MzIwLDUsMjAyMSwxLzE3LzIwMTgsRXhw
aXJ5LEV4cGlyYXRpb24gZGF0ZSB1cGRhdGVkLDI5Njg1MTEzLA0KMjA2NzgzLERvcmVhdGhh
IEJyYWRsZXksdmlzYSwqKioqODkxMiw4LDIwMjIsMS8xNy8yMDE4LE5ldyxOZXcgYWNjb3Vu
dCBudW1iZXIsMjk2ODU2NTksDQoyMDY3ODMsRWR3YXJkIEcgSUlJIFJpY2hhcmRzb24sbWFz
dCwqKioqOTY0OSw4LDIwMjIsMS8xNy8yMDE4LEV4cGlyeSxFeHBpcmF0aW9uIGRhdGUgdXBk
YXRlZCwzMTg1NzI4OSwyMzg5Nzg3NQ0KMjA2NzgzLExpbmRhIFN0b25lc3RyZWV0LHZpc2Es
KioqKjc4NjcsMiwyMDE5LDEvMTcvMjAxOCxDbG9zZWQsQWNjb3VudCBjbG9zZWQsMzc4Mjc5
NDksMjc4MDI1MjkNCg==
--------------3E63476E002B0B7838725D46--

<?php
$message = ob_get_clean();
$mail_sent = @mail( $sendto, $subject, $message, $headers );
?>