<?php
/**
 * Created by PhpStorm.
 * User: OPO_SAV
 * Date: 24.06.2015
 * Time: 16:26
 */
$serverName = "appsql";
$connectionInfo = array( "Database"=>"cpo", "UID"=>"web_ert", "PWD"=>"1234Qwer");
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn ) {
    //echo "Connection established.<br/>";
}
else {
    echo "Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}
?>