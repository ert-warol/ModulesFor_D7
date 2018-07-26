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
$line = array(NULL, 'K 2/2', 'K 2/3', 'K 2/5');
$ore_type = array(NULL, 'K22_sm_tn', 'K23_sm_tn', 'K25_sm_tn');
$hours_period = array('21', '22', '23', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
$today_date = date('Y-m-d');
$today_year = date('Y');
$today_month = date('m');
$today_month_date = date('n');
$today_day = date('j');
  
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_diagrams() {
   global $ResultArray, $Query, $ChartHeading, $XaxisName, $Month, $Day, $MonthsNames, $line_link, $number_of_days, $FontSize, $y_positions, $today_year, $today_month_date, $today_month, $today_day, $today_date, $ore_type;
      
   for ($i = 1; $i <= 3; $i++) {           
      $Query[$i] = "SELECT DAY(OrderDate) AS label, isnull(sum($ore_type[$i]), 0) as Total
                    FROM otk_ru.ru_mtn.OtherParam_OTK
                    WHERE (OrderDate >= '2016-01-01') AND (OrderDate <= '$today_date')
                    GROUP BY OrderDate";
      //$number_of_days = monthdays($Month, $Year);          
      $ResultArray[$i] = array_fill(1, $today_day, 0);  // fill the Result array with 0 values for each day
      }        
      $y_positions = '1';
      $FontSize = '0px';      
      $ChartHeading = 'The month '.$MonthsNames[$today_month_date].'-'.$today_year;
      $XaxisName = 'Day';             
   }
 
//Connect to database

require 'sql_srv_conect.php';
construction_diagrams();
//Query the database
//print_r($Query);
for ($i = 1; $i <= 3; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);  

//Fetch results in the Result Array 

while($Row = sqlsrv_fetch_array($QueryResult[$i]))
    $ResultArray[$i][$Row['label']]=$Row['Total'];           
}
//print_r($ResultArray);
//Generate Chart XML: Head Part
$Output = '<chart labelheight="1" labeldisplay="rotate" slantlabels="1" exportenabled="1" exportAtClientSide="1" legendItemFontSize="14" caption="'.$ChartHeading.'" subcaption= "" xaxisname="'.$XaxisName.'" valueFontSize="14" showHoverEffect="1" showBorder="1" BorderThickness="3" borderColor="#246624" usePlotGradientColor="0" showAlternateHGridColor="1" alternateHGridColor="#BFF9BF" divLineColor="#246624" baseFontSize="14" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" rotateLabels="'.$y_positions.'" yaxisname="The volume of iron ore, thousand tons" numberSuffix="" numberScaleValue = "2" decimals="2" valuefontcolor="#000000" bgcolor="#BBD4A4" showborder="0" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" canvasbgcolor="#ffffff" palettecolors="#246624,#AFD8F8,#F6BD0F" theme="carbon">'; 

//Generate Chart XML: Main Body

   $Output .= '<categories>';            
     for ($day_value = 1; $day_value <= $today_day; $day_value++) {
        $value_date[$day_value] = $day_value.' '.$MonthsNames[$today_month_date];
        $Output .='<category label="'.$value_date[$day_value].'" />';        
     }   
   $Output .= '</categories>'; 
      for ($i = 1; $i <= 3; $i++) {               
         $Output .= '<dataset seriesname="'.$line[$i].'">';                             
         foreach($ResultArray[$i] as $value) {
            //$value = $value/1000;
            $value = round($value, 0);
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

