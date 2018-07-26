<?php

$today_all_date = date('Y-m-d');

function construction_diagrams() {
   global $today_all_date, $min_value;
   unset($ResultArray);         
   $ResultArray = array();               
   $Query = "SELECT Dat, Chas AS label, PrLine1 AS Total
                    FROM cpo.dbo.RsProizvCPO
                    WHERE Dat = '$today_all_date'
                    GROUP BY Dat, Chas, PrLine1";
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
   $value_label = round($value_label, 1);   
   if ( $min_value = 0 or $min_value <= 50) {
      $min_value = 0;
   }
   else {
      $min_value = $min_value - 50;
   } 
   $lowerlimit = $min_value + 20;    
   $Output = '<chart theme="fint"  caption="Line # 1," subcaption="tn/h" captionFont="calibri" subCaptionFont="calibri" baseFont="calibri" baseFontSize="14" captionFontSize="14" subCaptionFontSize="14" captionfontcolor="#000000" subcaptionfontbold="10" bgcolor="#ffffff" showborder="0" lowerlimit="'.$min_value.'" upperlimit="450" numbersuffix="" valuefontsize="20" valuefontbold="0" gaugefillratio="40,20,40" refreshinterval="180" targetThickness="4">';
   $Output .= '<colorrange>';
   $Output .= '<color minvalue="200" maxvalue="250" code="#e44a00" alpha="25" />';
   $Output .= '<color minvalue="300" maxvalue="350" code="#f8bd19" alpha="25" />';
   $Output .= '<color minvalue="400" maxvalue="450" code="#6baa01" alpha="25" />';
   $Output .= '</colorrange>';  
   $Output .= '<value valuefontsize="20">'.$value_speedometr.'</value>'; 
   $Output .= '<target>'.$value_label.'</target>';     
   $Output .= '</chart>';

   //Set the output header to XML
   header('Content-type: text/xml');

   //Send output
echo $Output;
}

$Query = "SELECT a.*, PrLine1 as Total                
          FROM cpo.dbo.RsProizvCPO a
          WHERE (a.Dat='$today_all_date') AND  
          (a.Chas = (SELECT MAX(Chas) as Chas
              FROM cpo.dbo.RsProizvCPO 
              WHERE (Dat='$today_all_date') ))";
$ResultArray = array_fill(1, 1, 0); // fill the Result array with 0 values for each month
     
//Connect to database
require 'sql_srv_conect.php';

//Query the database
$QueryResult = sqlsrv_query($conn, $Query);

while($Row = sqlsrv_fetch_array($QueryResult)) {    
    $value_speedometr = $Row['Total'];    
    $value_speedometr = round($value_speedometr, 1);    
     
} 
construct_diagram_speedometer();



