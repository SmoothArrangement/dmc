<?php
     $siteurl = "http://shop.jamparts.com";
     
     $host = 'localhost';
     $username = 'root';
     $pass = '';
     $db = 'jampartsscript';
     $conn = mysql_connect($host, $username, $pass);
     $db = mysql_select_db($db, $conn) or die(mysql_error());
?>