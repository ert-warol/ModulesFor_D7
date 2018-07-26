<?php
/**
 * Created by PhpStorm.
 * User: opo_sav
 * Date: 21.05.2015
 * Time: 8:24
 */
$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$MonthsNames = array(NULL, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$MonthsNames_double = array(NULL, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
$line = array(NULL, 'Line # 1', 'Line # 2', 'Line # 3', 'Line # 4');
$line_link = array(NULL, 'PrLine1', 'PrLine2', 'PrLine3', 'PrLine4');
$hours_period = array('21', '22', '23', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
$today_all_date = date('Y-m-d');
$today_year = date('Y');
$today_month = date('m');
$today_month_date = date('n');
$today_day = date('j');
$today_day_date = date('d');

function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_diagrams() {
   global $today_day_date, $today_month_date, $period, $Year, $Month, $Day, $MonthsNames, $line_link, $Query, $ResultArray, $number_of_days, $FontSize, $y_positions, $today_year, $today_month, $today_day, $ChartHeading, $XaxisName;
   //unset($ResultArray);
   $today_day = $today_day - 1;
   for ($i = 1; $i <= 4; $i++) {
     $Query[$i] = "SELECT OrderSm, IndexChas, NumChas AS label, OrderDate, Pulp_Fe AS Total
             FROM otk_ru.ru_mtn.HimA_CPO1
             WHERE DAY(OrderDate) = 10 AND MONTH(OrderDate) = 1 AND YEAR(OrderDate) = 2016
             ORDER BY OrderSm, IndexChas";
     $y_positions = '1';
     //$ResultArray[$i] = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
   }
   $ChartHeading = 'Hourly New Production figures for the Date: '.$today_day_date.'-'.$MonthsNames[$today_month_date].'-'.$today_year;
   $XaxisName = 'Hours';
}
 
//Connect to database
//unset($ResultArray);
require 'sql_srv_conect.php';
construction_diagrams();
//Query the database
print_r($Query);
for ($i = 1; $i <= 4; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);  

//Fetch results in the Result Array 

   while($Row = sqlsrv_fetch_array($QueryResult[$i]))       
      $ResultArray[$i][$Row['label']]=$Row['Total'];
           
}
print_r($ResultArray);
$min_value = 500;
$max_value = -1; 
for($i = 1; $i <= 4; $i++) {
   foreach($ResultArray[$i] as $value) {
      if (empty($value)) {
         $value = $min_value; 
      }
      if(isset($value) AND $value < $min_value): {
         $value = round($value, 0);
         $min_value = $value;
      }
      elseif(isset($value) AND $value > $max_value): {
         $value = round($value, 0);
         $max_value = $value;
      }      
      endif; 
   }   
}

$min_value = $min_value - 10;
$max_value = $max_value + 10;
if($min_value < 0 ){
   $min_value = 0;
}
//print_r($ResultArray[$i]);
//Generate Chart XML: Head Part
$Output = '<chart exportenabled="1" exportAtClientSide="1" legendItemFontSize="14" alternateHGridColor="#BFF9BF" showBorder="0" BorderThickness="0" borderColor="#246624" caption="'.$ChartHeading.'" subcaption= "" xaxisname="'.$XaxisName.'" showHoverEffect="1" plotHoverEffect="1"  baseFontSize="14" yAxisMinValue="'.$min_value.'" yAxisMaxValue="'.$max_value.'" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" rotateLabels="'.$y_positions.'" yaxisname="Productivity, tn/h" numberSuffix="" numberScaleValue = "2" decimals="2" valuefontcolor="#000000" bgcolor="#BBD4A4" showborder="0" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1"  divLineColor="#395F39" divlinethickness="1" divlinedashlen="1" divlinegaplen="0" showAlternateHGridColor="1" palettecolors="#246624,#0220F7,#FF1800,#040404" theme="carbon">'; 

//Generate Chart XML: Main Body
$Output .= '<categories>';            
for ($hours = 0; $hours <= 23; $hours++) {            
  $Output .='<category label="'.$hours_period[$hours].'" />';
}  
$Output .= '</categories>';
for ($i = 1; $i <= 4; $i++) {
   $Output .= '<dataset seriesname="'.$line[$i].'">';
   foreach($ResultArray[$i] as $value) {  // HourNumber is hour (0-23)
     //$value = $value/1000;
     $value = round($value, 1);
     if($value == 0) {
        unset($value);
     }
     $Output .= '<set showValue="0" value="'.$value.'"/>';
  }
  $Output .= '</dataset>';
}           

//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
//header('Content-type: text/xml');

//Send output
//echo $Output;


