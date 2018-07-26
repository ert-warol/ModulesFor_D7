<?php
$MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$hours_smen = array(null, 21, 23, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19);
$day_interval = array(); 
$Year_interval = array();
$seriesname = array(null, 'Concentration 1', 'Concentration 2');
$today_all_date = date('Y-m-d');
$today_year = date('Y');
$last_year = $today_year - 1;
$today_month = date('n');
$last_month = $today_month - 1;
$today_day = date('j');
$last_day = $today_day - 1;
$today_all = $last_day.'-'.$MonthsNames[$today_month].'/'.$today_year;

function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
$number_of_days_in_the_month = monthdays($last_month, $today_year);
if($today_day == 1 AND $today_month != 1) {
   $last_day = $number_of_days_in_the_month;
   $month_of_the_previous_day = $last_month; 
   $year_of_the_previous_day = $today_year; 
}
else {
   $month_of_the_previous_day = $today_month;
   $year_of_the_previous_day = $today_year;    
}
if($today_day == 1 AND $today_month == 1){
   $number_of_days_in_the_month = monthdays($last_month, $last_year);
   $last_day = $number_of_days_in_the_month;
   $month_of_the_previous_day = $last_month;
   $year_of_the_previous_day = $last_year;  
}
  $Query[1] = "SELECT OrderSm, IndexChas, NumChas AS label, OrderDate, Pulp_Fe AS Total
             FROM otk_ru.ru_mtn.HimA_CPO1
             WHERE DAY(OrderDate) = $last_day AND MONTH(OrderDate) = $month_of_the_previous_day AND YEAR(OrderDate) = $year_of_the_previous_day
             ORDER BY OrderSm, IndexChas"; 
  $Query[2] = "SELECT OrderSm, IndexChas, NumChas AS label, OrderDate, Pulp_Fe AS Total
             FROM otk_ru.ru_mtn.HimA_CPO2
             WHERE DAY(OrderDate) = $last_day AND MONTH(OrderDate) = $month_of_the_previous_day AND YEAR(OrderDate) = $year_of_the_previous_day
             ORDER BY OrderSm, IndexChas";     
                       
  $ChartHeading = 'Quality concentrate: from '.$today_all;
  $XaxisName = 'Hours';
  
//Connect to database
require 'sql_srv_conect.php';

//Query the database
//print_r($Query);
for ($i = 1; $i <= 2; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);  
   
//Fetch results in the Result Array 
   while($Row = sqlsrv_fetch_array($QueryResult[$i]))
      $ResultArray[$i][$Row['label']] = $Row['Total'];         
}
$value_max = 0;
$value_min = 100;
$substitute_value_min = 100;
for($i = 1; $i <= 4; $i++){   
   foreach($ResultArray[$i] as $value) {      
      if (empty($value) == false) {
         $value_min = $substitute_value_min; 
      }    
      if($value_max < $value){
         $value_max = $value;
      } 
      if($value_min > $value){
         $value_min = $value;
      }
      if(isset($value)){
         $substitute_value_min = $value_min;
      }                              
   }
}
if($value_min > 0) {
   $value_min = $value_min - 0.1;
}
$value_max = $value_max + 0.1;   
//print_r($ResultArray);
//Generate Chart XML: Head Part
$Output ='<chart yAxisMinValue="'.$value_min.'" yAxisMaxValue="'.$value_max.'"  plotHoverEffect="1" plotFillHoverColor="#F52503" labelheight="1" slantlabels="1" exportenabled="1" exportAtClientSide="1" legendItemFontSize="14" caption="'.$ChartHeading.'" subcaption= "" xaxisname="'.$XaxisName.'" valueFontSize="14" showHoverEffect="1" showBorder="1" BorderThickness="3" borderColor="#246624" usePlotGradientColor="0" showAlternateHGridColor="1" alternateHGridColor="#BFF9BF" divLineColor="#246624" baseFontSize="14" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" rotateLabels="1" yaxisname="The iron content in the concentrate %" numberSuffix="" numberScaleValue = "2" decimals="2" valuefontcolor="#000000" bgcolor="#BBD4A4" showborder="0" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" canvasbgcolor="#ffffff" palettecolors="#246624,#FF1800" theme="carbon">'; 

//Generate Chart XML: Main Body            
   $Output .= '<categories>';        
   for ($hour = 1; $hour <= 12; $hour++) {
      $value = $hours_smen[$hour];
      if($hour <= 6) {
         $index_smena = ' sm-1';
      }
      else {
         $index_smena = ' sm-2';
      }
      $value = $value.$index_smena;                                   
      $Output .='<category label="'.$value.'" />';
   }
   $Output .= '</categories>';
   //$Output .= '<axis title="Fe Concentr 1" tickwidth="10" numberprefix="%" divlinedashed="1" numdivlines="4" minValue="66,2" maxValue="68,9" axisOnLeft="Fe in Konc">'; 
      for ($i = 1; $i <= 2; $i++) {
         $Output .= '<dataset seriesname="'.$seriesname[$i].'">';                             
         foreach($ResultArray[$i] as $value) {
            if($value == NULL OR $value == 0){
               unset($value);
            }                   
            $Output .= '<set showValue="0" value="'.$value.'"/>';                            
         }     
         $Output .= '</dataset>';                 	           
      }
   //$Output .= '</axis>';                  	
     
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;



