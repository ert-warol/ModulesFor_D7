<?php
$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$last_year = date("Y-M-D");

$Query = "SELECT F_Q_1 AS Total FROM SVODKA.otk_ru.ru_mtn.WM_OF1 WHERE IDRecNo = 1";
$ResultArray = array_fill(1, 1, 0); // fill the Result array with 0 values for each month        
       
function construct_diagram_speedometer(){ 
 global $value_speedometr;     
   $Output = '<chart theme="fint" caption="Section 11" subcaption="Download section" captionfontcolor="#000000" subcaptionfontbold="10" bgcolor="#ffffff" showborder="0" lowerlimit="190" upperlimit="270" numbersuffix=" tons" valuefontsize="11" valuefontbold="0" gaugefillmix="{light-10},{light-70},{dark-10}" gaugefillratio="40,20,40" datastreamurl="http://web1.pgok.corp/drupal/sites/all/modules/query_for_chart/obogashenie_chart/ob_angulargauge_11_chart.php" refreshinterval="60">';
   $Output .= '<colorrange>';
   $Output .= '<color minvalue="160" maxvalue="200" label="Low" code="#1aaf5d" />';
   $Output .= '<color minvalue="200" maxvalue="240" label="Moderate" code="#f2c500" />';
   $Output .= '<color minvalue="240" maxvalue="280" label="High" code="#c02d00" />';
   $Output .= '</colorrange>';
   $Output .= '<dials>';
   $Output .= '<dial id="fd_dial" value="'.$value_speedometr.'" tooltext="load current : $value tons" />';
   $Output .= '<dial id="fd_ert" value="'.$value_speedometr.'" tooltext="load current : $value tons" />';
   $Output .= '</dials>';      
   $Output .= '</chart>';

   //Set the output header to XML
   header('Content-type: text/xml');

   //Send output
echo $Output;
}

//Connect to database
require 'sql_srv_conect.php';

//Query the database
$QueryResult = sqlsrv_query($conn, $Query);

while($Row = sqlsrv_fetch_array($QueryResult)) {
    $value_speedometr = $Row['Total'];    
     
} 
construct_diagram_speedometer();
