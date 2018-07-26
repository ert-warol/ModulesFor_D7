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
$today_all_date = date('d-m-Y');
$today_year = date('Y');
$today_month = date('m');
$today_day = date('j');
$MonthsNames = array(NULL, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des');
$hours_period = array('21', '22', '23', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
 
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01")); // Функция опредиления количество дней в определенном месяце
}

function construction_diagrams() {
   global $Query, $ResultArray, $today_year, $today_month, $today_day, $today_all_date, $ChartHeading, $XaxisName;
   if($today_day == 1) {
      $today_month = $today_month - 1;
      $today_day  = monthdays($today_month, $today_year);
      $last_day = $today_day;
   } 
   else {
      $last_day = $today_day;
      $last_day = $last_day - 1;
   }   
   $Query = "SELECT Chas AS Value, SUM( PrLine1 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR( Dat )= $today_year AND MONTH( Dat ) = $today_month AND DAY( Dat ) = $last_day
   GROUP BY Chas ORDER BY Chas";
   $ResultArray = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
   $y_positions = '1';
   $ChartHeading = 'This day '.$last_day.'-'.$today_month.'-'.$today_year;
   $XaxisName = 'Hours';         
     
}

construction_diagrams();
  
//Connect to database
require 'sql_srv_conect.php';

//Query the database
$QueryResult = sqlsrv_query($conn, $Query);

//Fetch results in the Result Array
unset($ResultArray);
while($Row = sqlsrv_fetch_array($QueryResult))
    $ResultArray[$Row['Value']]=$Row['Total'];
    
//Generate Chart XML: Head Part  
   

$Output = '<chart caption="'.$ChartHeading.'" subcaption= "" xaxisname="'.$XaxisName.'" baseFontSize="14" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" rotateLabels="'.$y_positions.'" yaxisname="Productivity, tn/h" numberSuffix="" numberScaleValue = "2" decimals="2" palettecolors="#246624" valuefontcolor="#ffffff" bgcolor="#ffffff" showborder="0" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" canvasbgcolor="#ffffff">';

//Generate Chart XML: Main Body
   
        $i = 0;   
        foreach($ResultArray as $value) {  // HourNumber is hour (0-23)
            $value = round($value, 1);
            //$value = $value/1000;
            $Output .= '<set label="'.$hours_period[$i].'" value="'.$value.'"/>';
            $i++;
        }

//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;



