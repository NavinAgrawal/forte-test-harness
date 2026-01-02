<?php
# result.php
session_start();
if (isset($_SESSION['searchresult'])) print_r($_SESSION['searchresult']);
unset($_SESSION['searchresult']);
?>