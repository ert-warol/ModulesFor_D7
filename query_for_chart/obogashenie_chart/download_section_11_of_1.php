<?php
$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$MonthsNames = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$MonthsNames_double = array(NULL, 'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек');
$MonthsNames_double_for_day = array(NULL, 'Янв', 'Фев', 'Мар', 'Апр', 'Мая', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек');
$day_dat = array();
$day_interval = array();
$first_year = 2012;
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
function construction_diagrams() {
   global $period, $Year, $Month, $Day, $MonthsNames,  $first_year, $Year_interval, $last_year,  $Query, $ResultArray, $number_of_days, $FontSize, $y_positions;    
          $Query = "SELECT YEAR( Dat ) AS Value, SUM( PrLine1+PrLine2+PrLine3+PrLine4 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR( Dat ) >=$first_year GROUP BY YEAR( Dat ) ORDER BY YEAR( Dat )";
          $ResultArray = array_fill($first_year, 4, 0); // fill the Result array with 0 values for each month
          $y_positions = '0';
          $ChartHeading = 'Годовой показатель по производству ЦПО: '.$first_year.'-'.$last_year;
          $XaxisName = 'Годы';        
} 
    construction_diagrams();
       
//Connect to database
require 'sql_srv_conect.php';

//Query the database
$QueryResult = sqlsrv_query($conn, $Query);

//Fetch results in the Result Array
while($Row = sqlsrv_fetch_array($QueryResult))
    $ResultArray[$Row['Value']]=$Row['Total'];

//Generate Chart XML: Head Part  
   

$Output = "<chart caption='$ChartHeading' xaxisname='$XaxisName' rotateLabels='0' yaxisname='chemical analysis of ores' xAxisNameFontSize='14' yAxisNameFontSize='14' thousandSeparatorPosition='0' numberScaleValue = '2' showlabels='1' showvalues='1' decimals='2' formatNumberScale='0' FormatNumber='0' numberprefix='' numberSuffix=' tons' placevaluesinside='1' rotatevalues='1' valueFontSize='$FontSize' bgcolor='FFFFFF' legendshadow='0' legendborderalpha='50' canvasborderthickness='1' canvasborderalpha='50' palettecolors='#AFD8F8,#F6BD0F,#E8B989' showborder='0' theme='ocean'>";
//Generate Chart XML: Main Body
      
        $Output .='<categories>';        
           foreach($Year_interval as $value) {           
              $Output .='<category label="'.$value.'" />';
           }
        $Output .='</categories>';
        $Output .='<dataset seriesname="">';
           foreach($ResultArray as $yearly => $value) {
              $value = $value/1000;  
              $value = round($value, 2);            
              $Output .= '<set value="'.$value.'"/>';
        }
        $Output .='</dataset>';
       
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;

