<?php
$db_host     = '192.168.0.23';
$db_database = 'cpo';
$db_username = 'web_ert';
$db_password = '1234Qwer';

if ( ! mysql_connect($db_host, $db_username, $db_password))
    die ("Could not connect to the database server.");

if (! mysql_select_db($db_database))
    die ("Could not select the database.");
?>