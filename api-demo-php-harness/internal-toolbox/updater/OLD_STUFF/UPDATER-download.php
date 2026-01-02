<?php

header('Content-Type: text/html');
header('Content-Disposition: attachment; filename=' . $filename );	
header("Content-Length: " . filesize($filename));
fpassthru($filename);
 
?>
