<?php
$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$MonthsNames = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$MonthsNames_double = array(NULL, 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$MonthsNames_double_for_day = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$hours_period = array('21', '22', '23', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
$day_interval = array();
$last_year = date("Y"); 
$Year_interval = array();
$today_all_date = date('Y-m-d');
$today_year = date('Y');
$today_day = date('d');
$today_day = $today_day;
$today_month = date('m'); 
$today_month = $today_month - 1;
$previous_month = $today_month;
$today_m = date('m'); 
if ($today_month <=10) {
  $today_month = '0'.$today_month;
}
$today_all = $today_year.'-'.$today_month.'-'.$today_day; 
//echo '<br>'.'$today--'.$today_all.'</br>';
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_diagrams() {
   global $period, $Year, $Month, $Day, $ChartHeading, $XaxisName, $MonthsNames, $namber_base, $line, $first_year, $Year_interval, $last_year, $line_link, $Query, $ResultArray, $number_of_days, $FontSize, $y_positions, $today_m, $today_year_all, $today_all_date, $today_year, $today_all;
      
          unset($ResultArray);         
          $ResultArray = array();               
          $Query = "SELECT OrderNo AS Value, OrderDate, OrderSm, M_101 AS Total 
                       FROM otk_ru.ru_mtn.RudaS_WM_OF2
                       WHERE OrderDate = '$today_all_date'  
                       GROUP BY OrderSm, OrderDate, M_101, OrderNo 
                       ORDER BY OrderSm, OrderNo";
              
          $number_of_days = monthdays($today_m, $today_year);              
          $ResultArray = array_fill(1, 24, 0); // fill the Result array with 0 values for each month 
          $y_positions = '0'; 
          $FontSize = '14px';                  
          $ChartHeading = 'The Mill # 101 productivity for '.$today_all_date;
          $XaxisName = 'Hours';      
}

construction_diagrams();
 
//Connect to database
require 'sql_srv_conect.php';

//Query the database


   $QueryResult = sqlsrv_query($conn, $Query);  
   //print_r($Query);
//Fetch results in the Result Array 
   while($Row = sqlsrv_fetch_array($QueryResult))
      $ResultArray[$Row['Value']] = $Row['Total'];         
   $max_value = 0;
   $min_value = 400;  
foreach($ResultArray as $yearly => $value) {
   if($value == NULL): {
      unset($value);
   }
   elseif($value > 0 AND $value != NULL): {
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
if($min_value < 0 OR $min_value == 396) {
   unset($min_value);
}
//Generate Chart XML: Head Part
$Output ="<chart caption='$ChartHeading' xaxisname='$XaxisName' baseFontSize='14' tickValueDistance='14' yAxisNameFont='Calibri' captionFont='Calibri' valueFont='Calibri' subCaptionFont='calibri' baseFont='calibri' baseChartMessageFont='Calibri' yAxisMinValue='$min_value' yAxisMaxValue='$max_value' rotateLabels='0' yaxisname='Productivity, tn/h' xAxisNameFontSize='14' yAxisNameFontSize='14' thousandSeparatorPosition='0' numberScaleValue = '2' showlabels='1' showvalues='1' decimals='2' formatNumberScale='0' FormatNumber='0' numberprefix='' numberSuffix=' ' placevaluesinside='1' rotatevalues='1' valueFontSize='$FontSize' bgcolor='FFFFFF' legendshadow='0' legendborderalpha='50' canvasborderthickness='1' canvasborderalpha='50' palettecolors='#246624' showborder='0' theme='carbon'>"; 

//Generate Chart XML: Main Body
           
            $Output .= '<categories>';
            for($j = 0; $j <= 23; $j++) {
               if( $j < 12) {
                  $smena = '/s1';
               }            
               else {
                  $smena = '/s2';
               }            
               $value = $hours_period[$j].'h'.$smena;
               $Output .='<category label="'.$value.'" fontSize="14" />';
               }                                       
               $Output .= '</categories>';            
               $Output .= '<dataset seriesname="Mill 101" alpha="100" valuePosition="BELOW" anchorRadius="4">';                             
                   foreach($ResultArray as $yearly => $value) {                                        
                   $value = round($value, 2); 
                   if($value == NULL) {
                     unset($value);
                   }
                   $Output .= '<set value="'.$value.'"/>';                            
                   }     
                $Output .= '</dataset>';    	           
            
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;




