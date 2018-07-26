<?php
/**
 * Created by PhpStorm.
 * User: OPO_SAV
 * Date: 24.06.2015
 * Time: 16:26
 */
$serverName = "appsql"; //serverName\instanceName
$connectionInfo = array( "Database"=>"otk_ru", "UID"=>"web_ert", "PWD"=>"1234Qwer");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if( $conn ) {
    
}else{
    echo "Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
}
?>