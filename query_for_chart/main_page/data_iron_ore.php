<?php
/**
 * Created by PhpStorm.
 * User: opo_sav
 * Date: 21.05.2015
 * Time: 8:24
 */
$day_dat = array();
$last_year = date("Y");
$today_date = date('Y-m-d'); 
$today_day = date('j'); 
$current_month = date('m');
$MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$current_month = $MonthsNames[$current_month];
$current_month .= ' '.date('Y');

function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01")); // Функция опредиления количество дней в определенном месяце
}

$Query = "SELECT DAY(OrderDate) AS label, (isnull(sum(K22_sm_tn), 0) + isnull(sum(K23_sm_tn), 0) + isnull(sum(K25_sm_tn), 0)) as Total
          FROM otk_ru.ru_mtn.OtherParam_OTK
          WHERE (OrderDate >= '2015-12-01') AND (OrderDate <= '$today_date')
          GROUP BY OrderDate";
$ResultArray = array_fill(1, $today_day, 0); // fill the Result array with 0 values for each month
$y_positions = '0';
$ChartHeading = 'Current month: '.$current_month;
$XaxisName = 'Годы';
 
//Connect to database
require 'sql_srv_conect.php';

//Query the database
$QueryResult = sqlsrv_query($conn, $Query);

//Fetch results in the Result Array
while($Row = sqlsrv_fetch_array($QueryResult))
    $ResultArray[$Row['label']]=$Row['Total'];

//Generate Chart XML: Head Part  
 
$Output = '<chart caption="MTD Ore" subcaption= "'.$ChartHeading.'" valueFontSize="14" showBorder="1" BorderThickness="3" borderColor="#246624" usePlotGradientColor="0" showAlternateHGridColor="1" alternateHGridColor="#BFF9BF" divLineColor="#246624" canvasbgcolor="#ffffff" outCnvBaseFontSize="14" baseFontSize="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" xaxisname="Days" rotateLabels="'.$y_positions.'" yaxisname="The volume of iron ore, thousand tons" numberSuffix="" numberScaleValue = "2" decimals="2" palettecolors="#246624" valuefontcolor="#ffffff" bgcolor="#BBD4A4" showborder="1" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" theme="fint" >';

//Generate Chart XML: Main Body
  
$i = 1;        
  foreach($ResultArray as $DayNumber => $value) {  // DayNumber is day (1-31)
     $value = round($value, 2);
     $value = $value/1000;     
     $day_dat[$i] = $DayNumber;     
     $Output .= '<set label="' .$day_dat[$i]. '" value="' .$value. '" />';
     $i++;
  }

//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;
