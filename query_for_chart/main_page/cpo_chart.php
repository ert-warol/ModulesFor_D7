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
$MonthsNames = array(null, 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$first_year = 2012;
$last_year = date("Y");


function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01")); // Функция опредиления количество дней в определенном месяце
}
switch($period)
{
    default:
      
    case 'yearly':
        $Query = "SELECT YEAR( Dat ) AS Value, SUM( PrLine1+PrLine2+PrLine3+PrLine4 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR(Dat) >=$first_year GROUP BY YEAR( Dat ) ORDER BY YEAR(Dat)";
        $ResultArray = array_fill($first_year, 4, 0); // fill the Result array with 0 values for each month
        $ChartHeading = 'Годовой показатель по производству 4-х линий ЦПО: '.$first_year.'-'.$last_year;
        $XaxisName = 'Годы';
        
        break;
        
    case 'monthly':
        $Query = "SELECT MONTH( Dat ) AS Value, SUM( PrLine1+PrLine2+PrLine3+PrLine4 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR(Dat)=$Year GROUP BY MONTH( Dat ) ORDER BY MONTH(Dat) ";
        $ResultArray = array_fill(1, 12, 0); // fill the Result array with 0 values for each month
        $ChartHeading = 'Месячный показатель по производству 4-х линий ЦПО: '.$Year;
        $XaxisName = 'Месяцы';

        break;

    case 'daily':
        $Query = "SELECT DAY( Dat ) AS Value, SUM( PrLine1+PrLine2+PrLine3+PrLine4 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR( Dat )=$Year AND MONTH( Dat )={$Month} GROUP BY DAY( Dat ) ORDER BY DAY( Dat) ";
        $number_of_days = monthdays($Month, $Year);
        $ResultArray = array_fill(1, $number_of_days, 0);  // fill the Result array with 0 values for each day
        $ChartHeading = 'Дневной показатель по производству 4-х линий ЦПО: '.$MonthsNames[$Month].'/'.$Year;
        $XaxisName = 'Дни';
        
        break;

    case 'hourly':
        $Query = "SELECT Chas/60 AS Value, SUM( PrLine1+PrLine2+PrLine3+PrLine4 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR( Dat )=$Year AND MONTH( Dat )={$Month} AND DAY( Dat )={$Day}
        GROUP BY Chas ORDER BY Chas";
        $ResultArray = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
        $ChartHeading = 'Часавой показатель по производству 4-х линий ЦПО: '.$Day.'/'.$MonthsNames[$Month].'/'.$Year;
        $XaxisName = 'Часы';
        
        break;
}

//Connect to database
require 'sql_srv_conect.php';

//Query the database
$QueryResult = sqlsrv_query($conn, $Query);

//Fetch results in the Result Array
while($Row = sqlsrv_fetch_array($QueryResult))
    $ResultArray[$Row['Value']]=$Row['Total'];

//Generate Chart XML: Head Part  
   

$Output = '<chart caption="'.$ChartHeading.'" subcaption= "" xaxisname="Временной период" yaxisname="Произведено тыс. тонн" palettecolors="#0075c2" valuefontcolor="#ffffff" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" canvasbgcolor="#ffffff">';

//Generate Chart XML: Main Body
switch($period)
{
    default:
      
    case 'yearly':
        foreach($ResultArray as $yearly => $value) {  // Years is month number (2012-2015)
            $value = round($value, 2);
            $Output .= '<set label="'.$yearly.'" value="'.$value.'" link="newchart-xmlurl-cpo_chart.php?type=monthly&amp;year='.$yearly.'"/>';
        }
        break;
        
    case 'monthly':

        foreach($ResultArray as $MonthNumber => $value) {  // MonthNumber is month number (1-12)
            $value = round($value, 2);
            $Output .= '<set label="' . $MonthsNames[$MonthNumber] . '" value="' . $value . '" link="newchart-xmlurl-cpo_chart.php?type=daily&amp;year=' . $Year . '&amp;month=' . $MonthNumber . '"/>';
        }
        break;
        
    case 'daily':      
        foreach($ResultArray as $DayNumber => $value) {  // DayNumber is day (1-31)
            $value = round($value, 2);
            $Output .= '<set label="' . $DayNumber . '" value="' . $value . '" link="newchart-xmlurl-cpo_chart.php?type=hourly&amp;year=' . $Year . '&amp;month=' . $Month . '&amp;day=' . $DayNumber . '"/>';
        }
        break;
    case 'hourly':      
        foreach($ResultArray as $HourNumber => $value) {  // HourNumber is hour (0-23)
            $value = round($value, 2);
            $Output .= '<set label="' . $HourNumber . '" value="' . $value . '"/>';
        }
}

//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;
?>