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
$first_year = 2010;
$last_year = date("Y");
$Year_interval = array();
$today = date('Y'); 
$j = 0;
while ($i < $today) {
   $i = $first_year + $j;   
   $Year_interval[$j] = $i;
   $j++;    
}

function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01")); // Функция опредиления количество дней в определенном месяце
}
switch($period)
{
    default:
      
    case 'yearly':
        $Query = "SELECT YEAR( OrderDate ) AS Value, SUM(M_All) AS Total FROM otk_ru.ru_mtn.RudaS_WM_OF1 WHERE YEAR( OrderDate ) >=$first_year GROUP BY YEAR( OrderDate ) ORDER BY YEAR( OrderDate )";
        $ResultArray = array_fill($first_year, 6, 0); // fill the Result array with 0 values for each month
        $ChartHeading = 'Годовой показатель по производству 1-й фабрики обогащение: '.$first_year.'-'.$last_year;
        $XaxisName = 'Годы';
        
        break;
        
    case 'monthly':
        $Query = "SELECT MONTH( OrderDate ) AS Value, SUM( M_All ) AS Total FROM otk_ru.ru_mtn.RudaS_WM_OF1 WHERE YEAR( OrderDate )=$Year GROUP BY MONTH( OrderDate ) ORDER BY MONTH( OrderDate ) ";
        $ResultArray = array_fill(1, 12, 0); // fill the Result array with 0 values for each month
        $ChartHeading = 'Месячный показатель по производству 1-й фабрики обогащение: '.$Year;
        $XaxisName = 'Месяцы';

        break;

    case 'daily':
        $Query = "SELECT DAY( OrderDate ) AS Value, SUM( M_All ) AS Total FROM otk_ru.ru_mtn.RudaS_WM_OF1 WHERE YEAR( OrderDate )=$Year AND MONTH( OrderDate )={$Month} GROUP BY DAY( OrderDate ) ORDER BY DAY( OrderDate )";
        $number_of_days = monthdays($Month, 2014);
        $ResultArray = array_fill(1, $number_of_days, 0);  // fill the Result array with 0 values for each day
        $ChartHeading = 'Дневной показатель по производству 1-й фабрики обогащение: '.$MonthsNames[$Month].'/'.$Year;
        $XaxisName = 'Дни';
        
        break;

    case 'hourly':
        $Query = "SELECT TekChasSm AS Value, SUM( M_All ) AS Total, OrderSm, OrderNo FROM otk_ru.ru_mtn.RudaS_WM_OF1 WHERE YEAR( OrderDate )=$Year AND MONTH( OrderDate )={$Month} AND DAY( OrderDate )={$Day}
        GROUP BY OrderNo, OrderSm, TekChasSm ORDER BY OrderNo";
        $ResultArray = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
        $ChartHeading = 'Часавой показатель по производству 1-й фабрики обогащение: '.$Day.'/'.$MonthsNames[$Month].'/'.$Year;
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
   

$Output = '<chart caption="'.$ChartHeading.'" subcaption= "" xaxisname="Временной период" yaxisname="Произведено тыс. тонн" palettecolors="#246624" valuefontcolor="#ffffff" basefont="Helvetica Neue,Arial" captionfontsize="14" subcaptionfontsize="14" subcaptionfontbold="0" placevaluesinside="1" rotatevalues="1" showshadow="0" divlinecolor="#999999" divlinedashed="1" divlinethickness="1" divlinedashlen="1" divlinegaplen="1" canvasbgcolor="#ffffff">';

//Generate Chart XML: Main Body
switch($period)
{
    default:
      
    case 'yearly':
        foreach($ResultArray as $yearly => $value) {  // Years is month number (2010-2015)
            $value = round($value, 2);
            $Output .= '<set label="'.$yearly.'" value="'.$value.'" link="newchart-xmlurl-ob_1_chart.php?type=monthly&amp;year='.$yearly.'"/>';
        }
        break;
        
    case 'monthly':      
        foreach($ResultArray as $MonthNumber => $value)  // MonthNumber is month number (1-12)
            $Output .= '<set label="'.$MonthsNames[$MonthNumber].'" value="'.$value.'" link="newchart-xmlurl-ob_1_chart.php?type=daily&amp;year='.$Year.'&amp;month='.$MonthNumber.'"/>';
        break;
        
    case 'daily':      
        foreach($ResultArray as $DayNumber => $value)  // DayNumber is day (1-31)
            $Output .= '<set label="'.$DayNumber.'" value="'.$value.'" link="newchart-xmlurl-ob_1_chart.php?type=hourly&amp;year='.$Year.'&amp;month='.$Month.'&amp;day='.$DayNumber.'"/>';

        break;
    case 'hourly':      
        foreach($ResultArray as $HourNumber => $value)  // HourNumber is hour (0-23)
            $Output .= '<set label="'.$HourNumber.'" value="'.$value.'"/>';
}

//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;
?>