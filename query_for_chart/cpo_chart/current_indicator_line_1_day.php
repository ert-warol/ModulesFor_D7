<?php

$today_all_date = date('Y-m-d');
$today_year = date('Y');
$today_month = date('m');
$today_day = date('j');
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01")); // Функция опредиления количество дней в определенном месяце
}
if($today_day > 1){
   $today_day = $today_day - 1; 
}
else {
   $today_day = monthdays($today_month - 1, $today_year);
   $today_month = $today_month - 1;
}
function construction_diagrams() {
   global $today_all_date, $today_day, $today_month, $today_year;
   unset($ResultArray);   
   if($today_day == 1) {
      $today_month = $today_month - 1;
      $today_day  = monthdays($today_month, $today_year);
      $last_day = $today_day;
   } 
   else {
      $last_day = $today_day;
      $last_day = $last_day - 1;
   }        
   $ResultArray = array();
   $Query = "SELECT DAY(Dat) AS label, SUM(PrLine1) AS Total 
FROM cpo.dbo.RsProizvCPO 
WHERE YEAR(Dat) = $today_year AND MONTH(Dat) = $today_month AND DAY(Dat) = $last_day 
GROUP BY DAY(Dat) 
ORDER BY DAY(Dat)";               
   
   $ResultArray = array_fill(1, 1, 0); // fill the Result array with 0 values for each month 
//Connect to database
require 'sql_srv_conect.php';

//Query the database
unset($ResultArray);
$QueryResult = sqlsrv_query($conn, $Query);  
   
//Fetch results in the Result Array 
while($Row = sqlsrv_fetch_array($QueryResult))
   $max_value = $Row['Total']; 
   
   return $max_value ;
}

function construct_diagram_speedometer() { 
 global $value_speedometr;
   $value_label = construction_diagrams();
   $value_label = $value_label / 1000;
   $value_label = round($value_label, 1);   
       
   $Output = '<chart theme="fint" caption="Line # 1," subcaption="thousand tons" formatNumber="0" captionFont="calibri" subCaptionFont="calibri" baseFont="calibri" baseFontSize="14" captionFontSize="14" subCaptionFontSize="14" captionfontcolor="#000000" subcaptionfontbold="10" bgcolor="#ffffff" showborder="0" lowerlimit="0" upperlimit="15" numbersuffix="" valuefontsize="11" valuefontbold="0" gaugefillratio="40,20,40" refreshinterval="180" targetThickness="4">';
   $Output .= '<colorrange>';
   $Output .= '<color minvalue="" maxvalue="" code="#e44a00" alpha="25" />';
   $Output .= '<color minvalue="6" maxvalue="8" code="#f8bd19" alpha="25" />';
   $Output .= '<color minvalue="8" maxvalue="11" code="#6baa01" alpha="25" />';
   $Output .= '</colorrange>';  
   $Output .= '<value >'.$value_speedometr.'</value>'; 
   $Output .= '<target>'.$value_label.'</target>';     
   $Output .= '</chart>';

   //Set the output header to XML
   header('Content-type: text/xml');

   //Send output
echo $Output;
}

$Query = "SELECT DAY(Dat) AS label, SUM(PrLine1) AS Total 
FROM cpo.dbo.RsProizvCPO 
WHERE YEAR(Dat) = $today_year AND MONTH(Dat) = $today_month AND DAY(Dat) = $today_day 
GROUP BY DAY(Dat) 
ORDER BY DAY(Dat)";
$ResultArray = array_fill(1, 1, 0); // fill the Result array with 0 values for each month
     
//Connect to database
require 'sql_srv_conect.php';

//Query the database
$QueryResult = sqlsrv_query($conn, $Query);

while($Row = sqlsrv_fetch_array($QueryResult)) {    
    $value_speedometr = $Row['Total'];
    $value_speedometr = $value_speedometr / 1000;    
    $value_speedometr = round($value_speedometr, 0);    
     
} 
construct_diagram_speedometer();



