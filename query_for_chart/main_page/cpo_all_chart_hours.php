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
$hours_period = array('21 (1s)', '22 (1s)', '23 (1s)', '00 (1s)', '01 (1s)', '02 (1s)', '03 (1s)', '04 (1s)', '05 (1s)', '06 (1s)', '07 (1s)', '08 (2s)', '09 (2s)', '10 (2s)', '11 (2s)', '12 (2s)', '13 (2s)', '14 (2s)', '15 (2s)', '16 (2s)', '17 (2s)', '18 (2s)', '19 (2s)', '20 (2s)');
$today_all_date = date('Y-m-d');
$today_year = date('Y');
$last_year = $today_year - 1;
$today_month = date('m');
$today_month_date = date('n');
$last_month = $today_month_date - 1;
$today_day = date('j');
$last_day = $today_day - 1;
$today_day_date = date('d');

function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
$number_of_days_in_the_month = monthdays($last_month, $today_year);
if($today_day == 1 AND $today_month_date != 1) {
   $last_day = $number_of_days_in_the_month;
   $month_of_the_previous_day = $last_month; 
   $year_of_the_previous_day = $today_year; 
}
else {
   $month_of_the_previous_day = $today_month_date;
   $year_of_the_previous_day = $today_year;    
}
if($today_day == 1 AND $today_month_date == 1){
   $number_of_days_in_the_month = monthdays($last_month, $last_year);
   $last_day = $number_of_days_in_the_month;
   $month_of_the_previous_day = $last_month;
   $year_of_the_previous_day = $last_year;  
}

for ($i = 1; $i <= 4; $i++) {
   $Query[$i] = "SELECT Chas AS label, SUM( $line_link[$i] ) AS Total 
                 FROM cpo.dbo.RsProizvCPO 
                 WHERE YEAR( Dat ) = $year_of_the_previous_day AND MONTH( Dat ) = $month_of_the_previous_day AND DAY( Dat ) = $last_day
                 GROUP BY Chas ORDER BY Chas";     
}
$ChartHeading = 'Pelletizing lines productivity: '.$last_day.'-'.$MonthsNames[$month_of_the_previous_day].'-'.$year_of_the_previous_day;
$XaxisName = 'Hours';

 //Connect to database
require 'sql_srv_conect.php';

//Query the database

for ($i = 1; $i <= 4; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);  

//Fetch results in the Result Array 

   while($Row = sqlsrv_fetch_array($QueryResult[$i]))       
      $ResultArray[$i][$Row['label']]=$Row['Total'];
           
}
$min_value = 500;
$max_value = 0; 
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
$Output = '<chart exportenabled="1" exportAtClientSide="1" labelheight="1" slantlabels="1" legendItemFontSize="14" alternateHGridColor="#BFF9BF" showBorder="0" BorderThickness="0" borderColor="#246624" caption="'.$ChartHeading.'" subcaption= "" xaxisname="'.$XaxisName.'" showHoverEffect="1" plotHoverEffect="1" baseFontSize="14" yAxisMinValue="'.$min_value.'" yAxisMaxValue="'.$max_value.'" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" rotateLabels="1" yaxisname="Productivity, tn/h" numberSuffix="" numberScaleValue = "2" decimals="2" valuefontcolor="#000000" bgcolor="#BBD4A4" showborder="0" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" showshadow="0" divlinecolor="#999999" divlinedashed="1"  divLineColor="#395F39" divlinethickness="1" divlinedashlen="1" divlinegaplen="0" showAlternateHGridColor="1" palettecolors="#246624,#0220F7,#FF1800,#040404" theme="carbon">'; 

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
header('Content-type: text/xml');

//Send output
echo $Output;


