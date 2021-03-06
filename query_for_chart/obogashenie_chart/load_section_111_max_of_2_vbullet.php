<?php

$today_all_date = date('Y-m-d');

function construction_diagrams() {
   global $today_all_date, $min_value;
   unset($ResultArray);         
   $ResultArray = array();               
   $Query = "SELECT OrderNo AS Value, OrderDate, OrderSm, M_111 AS Total 
             FROM otk_ru.ru_mtn.RudaS_WM_OF2
             WHERE OrderDate = '$today_all_date'  
             GROUP BY OrderSm, OrderDate, M_111, OrderNo 
             ORDER BY OrderSm, OrderNo";
   $ResultArray = array_fill(1, 24, 0); // fill the Result array with 0 values for each month 
//Connect to database
require 'sql_srv_conect.php';

//Query the database

unset($ResultArray);
$QueryResult = sqlsrv_query($conn, $Query);  
   
//Fetch results in the Result Array 
while($Row = sqlsrv_fetch_array($QueryResult))
   $ResultArray[$Row['label']] = $Row['Total'];        
   $max_value_all_day = array();
   $i = 1;     
foreach($ResultArray as $value) {                     
   $max_value_all_day[$i] = $value; 
   $i++;                              
}      
$max_value_1_smen = 0;
for($i = 1; $i <= 12; $i++) {
   if($max_value_1_smen < $max_value_all_day[$i]) {
      $max_value_1_smen = $max_value_all_day[$i];
   }
}     
$max_value_2_smen = 0;
for($i = 13; $i <= 24; $i++) {
   if($max_value_2_smen < $max_value_all_day[$i]) {
      $max_value_2_smen = $max_value_all_day[$i];
   }
}
if($max_value_2_smen > 0) {
   $max_value = $max_value_2_smen;
}   
else {
   $max_value = $max_value_1_smen;
}
$max_value = round($max_value, 2);
   return $max_value;
}

function construct_diagram_speedometer(){ 
 global $value_speedometr, $min_value;
   $value_label = construction_diagrams();   
   if ( $min_value = 0 or $min_value <= 50) {
      $min_value = 0;
   }
   else {
      $min_value = $min_value - 50;
   } 
   $lowerlimit = $min_value + 20;    
   $Output = '<chart theme="fint" caption="Mill # 111," subcaption="tn/h" captionFont="calibri" subCaptionFont="calibri" baseFont="calibri" baseFontSize="14" captionFontSize="14" subCaptionFontSize="14" captionfontcolor="#000000" subcaptionfontbold="10" bgcolor="#ffffff" showborder="0" lowerlimit="'.$min_value.'" upperlimit="200" numbersuffix="" valuefontsize="16" valuefontbold="0" gaugefillratio="40,20,40" refreshinterval="180" targetThickness="4">';
   $Output .= '<colorrange>';
   $Output .= '<color minvalue="'.$min_value.'" maxvalue="120" code="#e44a00" alpha="25" />';
   $Output .= '<color minvalue="120" maxvalue="160" code="#f8bd19" alpha="25" />';
   $Output .= '<color minvalue="160" maxvalue="200" code="#6baa01" alpha="25" />';
   $Output .= '</colorrange>';  
   $Output .= '<value>'.$value_speedometr.'</value>'; 
   $Output .= '<target>'.$value_label.'</target>';     
   $Output .= '</chart>';

   //Set the output header to XML
   header('Content-type: text/xml');

   //Send output
echo $Output;
}

$Query = "SELECT IDRecNo, F_Q_3 AS Total FROM otk_ru.ru_mtn.WM_OF2 WHERE IDRecNo = 1";
$ResultArray = array_fill(1, 1, 0); // fill the Result array with 0 values for each month
     
//Connect to database
require 'sql_srv_conect.php';

//Query the database
$QueryResult = sqlsrv_query($conn, $Query);

while($Row = sqlsrv_fetch_array($QueryResult)) {
    $value_speedometr = $Row['Total'];    
     
} 
construct_diagram_speedometer();


