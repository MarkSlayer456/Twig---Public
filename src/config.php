<?php

 $db_host = "localhost"; // host
 $db_user = "admin"; // admin username
 $db_pass = "password"; // admin password
 $db_db   = "database"; // database name

 $myconn = new mysqli($db_host, $db_user, $db_pass, $db_db);
 if ($myconn->connect_error) {
   die ("Failed to connect to database (" . $myconn->connect_errono . "): " .
   $myconn->connect_error);
 }

?>

