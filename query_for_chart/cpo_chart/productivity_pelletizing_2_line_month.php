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
$today_year = date('Y');
$today_month = date('m');
$MonthsNames = array(NULL, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des');
$MonthsNames_double = array(NULL, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Des');
$MonthsNames_double_for_day = array(NULL, 'Янв', 'Фев', 'Мар', 'Апр', 'Мая', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек');
$hours_period = array('21', '22', '23', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
 
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01")); // Функция опредиления количество дней в определенном месяце
}

function construction_diagrams() {
   global $period, $Year, $Month, $Day, $MonthsNames,  $first_year, $Year_interval, $last_year,  $Query, $ResultArray, $number_of_days, $FontSize, $y_positions, $today_year, $today_month, $ChartHeading, $XaxisName;
     switch($period) {
       default:
      
         case 'monthly':
            $Query = "SELECT MONTH( Dat ) AS Value, SUM( PrLine2 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR(Dat)=$today_year GROUP BY MONTH( Dat ) ORDER BY MONTH(Dat) ";
            $ResultArray = array_fill(1, $today_month, 0); // fill the Result array with 0 values for each month
            $y_positions = '1';
            $ChartHeading = 'This year '.$today_year;
            $XaxisName = 'Month';
         break;
         case 'daily':
            $Query = "SELECT DAY( Dat ) AS Value, SUM( PrLine2 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR( Dat )=$today_year AND MONTH( Dat )=$Month GROUP BY DAY( Dat ) ORDER BY DAY( Dat) ";
            $number_of_days = monthdays($Month, $today_year);
            $ResultArray = array_fill(1, $number_of_days, 0);  // fill the Result array with 0 values for each day
            $y_positions = '1';
            $ChartHeading = 'The month '.$MonthsNames[$Month].'-'.$today_year;
            $XaxisName = 'Day';
         break;
         case 'hourly':
            $Query = "SELECT Chas AS Value, SUM( PrLine2 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR( Dat )=$today_year AND MONTH( Dat )=$Month AND DAY( Dat )=$Day
            GROUP BY Chas ORDER BY Chas";
            $ResultArray = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
            $y_positions = '1';
            $ChartHeading = 'The day '.$Day.'-'.$MonthsNames[$Month].'-'.$today_year;
            $XaxisName = 'Hours';
         break;
     }
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
   

$Output = '<chart caption="'.$ChartHeading.'" subcaption= "" xaxisname="'.$XaxisName.'" baseFontSize="14" tickValueDistance="14" yAxisNameFont="Calibri" captionFont="Calibri" valueFont="Calibri" subCaptionFont="calibri" baseFont="calibri" baseChartMessageFont="Calibri" rotateLabels="'.$y_positions.'" yaxisname="Productivity, thousand tons/month" numberSuffix="" numberScaleValue = "2" decimals="2" palettecolors="#246624" valuefontcolor="#ffffff" bgcolor="#ffffff" showborder="0" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" canvasbgcolor="#ffffff">';

//Generate Chart XML: Main Body
switch($period)
{
    default:    
        
    case 'monthly':

        foreach($ResultArray as $MonthNumber => $value) {  // MonthNumber is month number (1-12)
            $value = round($value, 2);
            $value = $value/1000;
            $Output .= '<set label="' .$MonthsNames[$MonthNumber]. '" value="' .$value. '" link="newchart-xmlurl-productivity_pelletizing_2_line_month.php?type=daily&amp;year=' .$today_year. '&amp;month=' .$MonthNumber. '"/>';
        }
        break;
        
    case 'daily':  
        $i = 1;        
        foreach($ResultArray as $DayNumber => $value) {  // DayNumber is day (1-31)
            $value = round($value, 2);
            $value = $value/1000;  
            $Output .= '<set label="' .$DayNumber. '" value="' .$value. '" link="newchart-xmlurl-productivity_pelletizing_2_line_month.php?type=hourly&amp;year=' .$Year. '&amp;month=' .$Month. '&amp;day=' .$DayNumber. '"/>';
            $i++;
        }
        break;
    case 'hourly':   
        $i = 0;   
        foreach($ResultArray as $value) {  // HourNumber is hour (0-23)
            $value = round($value, 1);
            //$value = $value/1000;
            $Output .= '<set label="'.$hours_period[$i].'" value="'.$value.'"/>';
            $i++;
        }
}

//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;

