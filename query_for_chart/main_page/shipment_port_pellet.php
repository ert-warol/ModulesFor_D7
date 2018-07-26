<?php
/**
 * Created by PhpStorm.
 * User: opo_sav
 * Date: 21.05.2015
 * Time: 8:24
 */
$period	= $_GET['type'];
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$MonthsNames = array(NULL, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$MonthsNames_double = array(NULL, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
$line = array(NULL, 'Pellet 62%', 'Pellet 65%');
$pellet_type = array(NULL, '100', '200');
$hours_period = array('21', '22', '23', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
$today_date = date('Y-m-d');
$today_year = date('Y');
$today_month = date('m'); 
$today_day = date('j');
$today_day_date = date('d');
$today_month_date = date('n');
  
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_diagrams() {
   global $ResultArray, $Query, $ChartHeading, $XaxisName, $today_day_date, $today_month_date,  $Month, $Day, $MonthsNames, $line_link, $number_of_days, $FontSize, $y_positions, $today_year, $today_month, $today_day, $today_date, $pellet_type;
      
   for ($i = 1; $i <= 2; $i++) {           
      $Query[$i] = "SELECT DAY(label) AS label, SUM(total) as Total, RTRIM(LTRIM(type_pellet))
                    FROM web_interface.dbo.shipment_port_pellet
                    WHERE MONTH(label) = MONTH(GETDATE())  AND YEAR(label) = YEAR(GETDATE()) AND RTRIM(LTRIM(type_pellet)) = $pellet_type[$i]
                    GROUP BY label, RTRIM(LTRIM(type_pellet))";
      //$number_of_days = monthdays($Month, $Year);          
      $ResultArray[$i] = array_fill(1, $today_day, 0);  // fill the Result array with 0 values for each day
      }        
      $y_positions = '1';
      $FontSize = '0px';      
      $ChartHeading = 'Shipping in ports, '.$MonthsNames[$today_month_date].'-'.$today_year;
      $XaxisName = 'Day';             
   }
 
//Connect to database

require 'sql_srv_conect.php';
construction_diagrams();
//Query the database
//print_r($Query);
for ($i = 1; $i <= 2; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);  

//Fetch results in the Result Array 

while($Row = sqlsrv_fetch_array($QueryResult[$i]))
    $ResultArray[$i][$Row['label']]=$Row['Total'];           
}
//print_r($ResultArray);
//Generate Chart XML: Head Part
$Output = '<chart slantlabels="1" exportenabled="1" exportAtClientSide="1" labeldisplay="rotate" legendItemFontSize="14" caption="'.$ChartHeading.'" xaxisname="'.$XaxisName.'" showHoverEffect="1" showBorder="0" BorderThickness="3" borderColor="#246624" usePlotGradientColor="0" showAlternateHGridColor="1" alternateHGridColor="#BFF9BF" divLineColor="#246624" baseFontSize="14" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFontSize="14" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" rotateLabels="1" yaxisname="Pellets, thousand tons" numberSuffix="" numberScaleValue = "2" decimals="2" valuefontcolor="#000000" bgcolor="#BBD4A4" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" canvasbgcolor="#ffffff" palettecolors="#246624,#C18484" theme="fint">';
/*<chart alternateHGridColor="#BFF9BF" showBorder="0" BorderThickness="1" borderColor="#246624" caption="'.$ChartHeading.'" subcaption= "" xaxisname="'.$XaxisName.'" showHoverEffect="1" plotHoverEffect="1" drawAnchors="1" anchorRadius="4" baseFontSize="14" yAxisMinValue="'.$min_value.'" yAxisMaxValue="'.$max_value.'" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" rotateLabels="'.$y_positions.'" yaxisname="Productivity, tn/h" numberSuffix="" numberScaleValue = "2" decimals="2" valuefontcolor="#000000" bgcolor="#BBD4A4" showborder="0" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divLineColor="#395F39" divlinethickness="1" divlinedashlen="0" divlinegaplen="0" showAlternateHGridColor="1" palettecolors="#246624,#FF1800" theme="carbon">*/

//Generate Chart XML: Main Body

   $Output .= '<categories>';
      $j = 1;
     foreach($ResultArray[2] as $value) {           
        $date[$j] = $value;    
        $j++;   
     }                   
     for ($day_value = 1; $day_value <= $today_day; $day_value++) {
        $value_date[$day_value] = $day_value.' '.$MonthsNames[$today_month_date];
        $Output .='<category label="'.$value_date[$day_value].'" />';        
     }   
   $Output .= '</categories>'; 
      for ($i = 1; $i <= 2; $i++) {               
         $Output .= '<dataset seriesname="'.$line[$i].'" legendItemFontSize="14">';                             
         foreach($ResultArray[$i] as $value) {
            //$value = $value/1000;
            $value = round($value, 0);
            if($value == 0) {
               unset($value);
            }
            $Output .= '<set value="'.$value.'" />';         
         }     
         $Output .= '</dataset>';   	      
     }          
        
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;

