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
$today_day = date('j');
  
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_diagrams() {
   global $period, $Year, $Month, $Day, $MonthsNames, $line_link, $Query, $ResultArray, $number_of_days, $FontSize, $y_positions, $today_year, $today_month, $today_day;
   unset($ResultArray);
   $today_day = $today_day - 1;
   for ($i = 1; $i <= 4; $i++) {
     $Query[$i] = "SELECT Chas AS label, SUM( $line_link[$i] ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR( Dat )=$today_year AND MONTH( Dat )=$today_month AND DAY( Dat )=$today_day
     GROUP BY Chas ORDER BY Chas";
     $y_positions = '1';
     $ResultArray[$i] = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
   }
   $ChartHeading = 'Hourly New Production figures for the Date: '.$Day.'-'.$MonthsNames[$Month].'-'.$today_year;
   $XaxisName = 'Период времени - Часы';
}
 
//Connect to database
//unset($ResultArray);
require 'sql_srv_conect.php';
construction_diagrams();
//Query the database

for ($i = 1; $i <= 4; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);  

//Fetch results in the Result Array 

while($Row = sqlsrv_fetch_array($QueryResult[$i]))
    $ResultArray[$i][$Row['label']]=$Row['Total'];
           
}
//print_r($ResultArray[$i]);
//Generate Chart XML: Head Part
$Output = '<chart caption="'.$ChartHeading.'" subcaption= "" xaxisname="'.$XaxisName.'" baseFontSize="14" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" rotateLabels="'.$y_positions.'" yaxisname="Productivity, tn/h" numberSuffix="" numberScaleValue = "2" decimals="2" valuefontcolor="#000000" bgcolor="#ffffff" showborder="0" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" canvasbgcolor="#A6BF85" palettecolors="#246624,#AFD8F8,#F6BD0F,#E8B989" theme="fint">'; 

//Generate Chart XML: Main Body
$Output .= '<categories>';            
for ($hours = 0; $hours <= 23; $hours++) {            
  $Output .='<category label="'.$hours_period[$hours].'" />';
}  
$Output .= '</categories>';
for ($i = 1; $i <= 4; $i++) {
   $Output .= '<dataset seriesname="'.$line[$i].'">';
   foreach($ResultArray[$i] as $value) {  // HourNumber is hour (0-23)
     $value = $value/1000;
     $value = round($value, 3);
     $Output .= '<set value="'.$value.'"/>';
  }
  $Output .= '</dataset>';
}           

//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;


