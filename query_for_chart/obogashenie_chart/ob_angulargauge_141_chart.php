<?php
$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$last_year = date("Y-M-D");

$Query = "SELECT F_Q_9 AS Total FROM otk_ru.ru_mtn.WM_OF2 WHERE IDRecNo = 1";
$ResultArray = array_fill(1, 1, 0); // fill the Result array with 0 values for each month        
       
function construct_diagram_speedometer(){ 
   global $value_speedometr;     
   $Output = "<chart caption='  Мел. 141' captionfont='Arial' animation='0' placeTicksInside='1' captionfontcolor='#076717' refreshInterval='180' manageresize='1' origw='300' origh='300' tickvaluedistance='5' bgcolor='#FFFFFF' upperlimit='200' lowerlimit='0' basefontcolor='#FFFFFF' majortmnumber='9' majortmcolor='#FFFFFF' majortmheight='8' majortmthickness='5' minortmnumber='5' minortmcolor='#FFFFFF' minortmheight='3' minortmthickness='2' pivotradius='10' pivotbgcolor='#000000' pivotbordercolor='#FFFFFF' pivotborderthickness='2' tooltipbordercolor='#FFFFFF' tooltipbgcolor='#333333' gaugeouterradius='114' gaugestartangle='220' gaugeendangle='-40' gaugealpha='0' decimals='0' showcolorrange='0' placevaluesinside='1' pivotfillmix='' showpivotborder='1' annrenderdelay='0' gaugeoriginx='160' gaugeoriginy='110' showborder='0'>";
   $Output .= '<dials>'; // циферблаты
   $Output .= '<dial value="'.$value_speedometr.'" bgcolor="000000" valueY="50" bordercolor="#FFFFFF" borderalpha="100" basewidth="5" topwidth="1" borderthickness="2" valuey="900" radius="70" baseRadius="30"/>';
   $Output .= '</dials>';
   $Output .= '<annotations>';
   $Output .= '<annotationgroup x="160" y="110">';
   $Output .= '<annotation type="circle" fillasgradient="1" fillcolor="#076717,#076717" fillalpha="100,100" fillratio="55,5" />';
   $Output .= '<annotation type="circle" x="0" y="0" radius="80" showborder="1" bordercolor="#060606" fillasgradient="1" fillcolor="#ffffff,#000000" fillalpha="50,50" fillratio="1,99" />';
   $Output .= '</annotationgroup>';
   $Output .= '<annotationgroup x="160" y="100" showbelow="0" scaletext="1">';
   $Output .= '<annotation type="text" y="60" label="'.$value_speedometr.'" fontcolor="#F5F705" fontsize="15" bold="0" />';
   $Output .= '</annotationgroup>';
   $Output .= '</annotations>';
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
    setcookie("nagruzka_value_141", $value_speedometr, time() + 180); 
} 
construct_diagram_speedometer();
