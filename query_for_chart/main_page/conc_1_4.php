<?php
$MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$hours_period = array('21 h', '22 h', '23 h', '00 h', '01 h', '02 h', '03 h', '04 h', '05 h', '06 h', '07 h', '08 h', '09 h', '10 h', '11 h', '12 h', '13 h', '14 h', '15 h', '16 h',
 '17 h', '18 h', '19 h', '20 h');
$today_year = date('Y');
$last_year = $today_year - 1;
$today_month = date('n');
$last_month = $today_month - 1;
$today_day = date('j');
$last_day = $today_day - 1;
$today_day = date('d');
$today_day = $today_day;

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
               
$Query = "SELECT OrderNo AS Value, OrderDate, OrderSm, M_4 AS Total 
          FROM otk_ru.ru_mtn.RudaS_WM_OF1
          WHERE DAY(OrderDate) = $last_day AND MONTH(OrderDate) = $month_of_the_previous_day AND YEAR(OrderDate) = $year_of_the_previous_day  
          GROUP BY OrderSm, OrderDate, M_4, OrderNo 
          ORDER BY OrderSm, OrderNo";
              
          //$number_of_days = monthdays($today_m, $today_year);              
          //$ResultArray = array_fill(1, 24, 0); // fill the Result array with 0 values for each month 
          //$y_positions = ''; 
          $FontSize = '14px';                  
          $ChartHeading = 'The Section # 4 productivity tons, for '.$last_day.'-'.$MonthsNames[$month_of_the_previous_day].'/'.$year_of_the_previous_day.'   (last day)';                

//Connect to database
require 'sql_srv_conect.php';

//Query the database
$QueryResult = sqlsrv_query($conn, $Query);  
   //print_r($Query);
//Fetch results in the Result Array 
while($Row = sqlsrv_fetch_array($QueryResult))
   $ResultArray[$Row['Value']] = $Row['Total'];
            
$max_value = 0;
$min_value = 300;  
foreach($ResultArray as $yearly => $value) {
   if($value == 0): {
      unset($value);
   }
   elseif($value > 0 AND $value != 0): {
      if($max_value < $value) {
         $max_value = $value;       
      }
      if($min_value > $value) {
         $min_value = $value;
      }      
   }
   endif;
}
$max_value = $max_value + 4;
$min_value = $min_value - 3;
$sum_interval = $max_value - $min_value;
$sum_interval = fmod($sum_interval,2);
if($sum_interval != 0) {
   $min_value = $min_value - 1; 
}
if($min_value < 0 OR $min_value == 297) {
   $min_value = 0;
}
//Generate Chart XML: Head Part
$Output = '<chart yAxisMinValue="'.$min_value.'" yAxisMaxValue="'.$max_value.'"  plotHoverEffect="1" plotFillHoverColor="#2FF52F" labelheight="1" slantlabels="1" exportenabled="1" exportAtClientSide="1" legendItemFontSize="14" caption="'.$ChartHeading.'" valueFontSize="14" showHoverEffect="1"  usePlotGradientColor="0" showAlternateHGridColor="1" alternateHGridColor="#BFF9BF" divLineColor="#246624" baseFontSize="14" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri"  numberSuffix="" numberScaleValue = "2" decimals="2" valuefontcolor="#000000" bgcolor="#BBD4A4" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" canvasbgcolor="#ffffff" palettecolors="#246624" theme="carbon">';
 
//Generate Chart XML: Main Body
$i = 0;         
foreach($ResultArray as $yearly => $value) {
                                          
   $value = round($value, 2); 
   if($value == NULL) {
      unset($value);
   }
   $Output .= '<set showValue="0" label="'.$hours_period[$i].'" value="'.$value.'"/>';
   $i++;                            
}     
                 	           
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;

