<?php
/**
 * Created by PhpStorm.
 * User: OPO_SAV
 * Date: 30.09.2015
 * Time: 09:23
 */
$serverName = "appsql";
$connectionInfo = array( "Database"=>"otk_ru", "UID"=>"web_ert", "PWD"=>"1234Qwer");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn ) {
    //echo "Connection established.<br/>";
}
else {
    echo "Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}
?>