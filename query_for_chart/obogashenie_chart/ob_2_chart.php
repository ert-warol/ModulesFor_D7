<?php

$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$MonthsNames = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$MonthsNames_double = array(NULL, 'Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек');
$MonthsNames_double_for_day = array(NULL, 'Янв', 'Фев', 'Мар', 'Апр', 'Мая', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек');
$line = array(NULL, '1-я линия', '2-я линия', '3-я линия', '4-я линия');
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
    return date("t", strtotime($year . "-" . $month . "-01"));
}
/*function construction_interval_diagrams_year() {
   global $period, $Year, $Month, $Day, $MonthsNames, $line, $Year_interval, $line_link, $Query, $ResultArray, $FontSize, $y_positions;
      if($_COOKIE["period"] = 1) {
        $begin = $_COOKIE['begin'];
        $end = $_COOKIE['end'];
        $year_begin = date("Y", strtotime($_COOKIE["begin"]));
        $year_end = date("Y", strtotime($_COOKIE["end"]));
        $FontSize = '11px';

          $Query = "SELECT YEAR( Dat ) AS label, SUM( PrLine1 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR( Dat ) >= $year_begin AND YEAR( Dat ) <= $year_end GROUP BY YEAR( Dat ) ORDER BY YEAR( Dat ) ";
          $range_year = $year_end - $year_begin;
          $ResultArray = array_fill($year_begin, $range_year, 0); // fill the Result array with 0 values for each month

        $y_positions = '0';
        $ChartHeading = 'Показатель по производству 4-х линий ЦПО в период: '.$year_begin.'-'.$year_end;
        $XaxisName = 'Временной период- Годы';
        $Year_interval = array();
        for ($i = 0; $i <= $range_year; $i++) {
            $Year_interval[$i] = $year_begin + $i;
        }
      }
}
function construction_interval_diagrams_month() {
    global $period, $Year, $Month, $Day, $MonthsNames, $FontSize, $line, $Year_interval, $MonthsNames_double, $line_link, $Query, $ResultArray, $y_positions;
      if($_COOKIE["period"] = 2 ) {
        $period = 'monthly';
        $begin = date("Y-m-d", strtotime($_COOKIE['begin']));
        $end = date("Y-m-d", strtotime($_COOKIE['end']));
        $year_begin = date("Y", strtotime($_COOKIE["begin"]));
        $year_end = date("Y", strtotime($_COOKIE["end"]));
        $month_begin = date("n", strtotime($_COOKIE["begin"]));
        $month_end = date("n", strtotime($_COOKIE["end"]));
        $range_year = $year_end - $year_begin;
        $range_month = $range_year*12+($month_end-$month_begin);
        unset($ResultArray);
        $ResultArray = array();
        $MonthsNames = array();
        $FontSize = '0px';
        if($year_begin == $year_end) {
           for ($i = $month_begin; $i <=$month_end; $i++) {
                 $MonthsNames[$i] = $MonthsNames_double[$i].'-'.$year_begin;
              }
        }
        else {
           $k = 1;
           while ( $k <= $range_month) {
              for($j = $year_begin; $j <= $year_end; $j++ ) {
                 if ($j == $year_end) {
                    $stop_i = $month_end;
                 }
                 else {
                    $stop_i = 12;
                 }
                 if ($j > $year_begin ) {
                   $begin_i = 1;
                 }
                 else {
                   $begin_i = $month_begin;
                 }
                 for ($i = $begin_i; $i <=$stop_i; $i++) {
                    $MonthsNames[$k] = $MonthsNames_double[$i].'-'.$j;
                    $k++;
                 }
              }
           }
        }

          $Query = "SELECT convert(char(6), Dat, 112) AS label,  SUM( PrLine1 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE (Dat BETWEEN '$begin' AND '$end' ) AND NOT Dat IN ('$end') GROUP BY convert(char(6), Dat, 112) ORDER BY convert(char(6), Dat, 112)";
          $ResultArray = array_fill(1, $range_month, 0); // fill the Result array with 0 values for each month
          //print_r($ResultArray);

        $y_positions = '1';
        $ChartHeading = 'Показатель по производству 4-х линий ЦПО в период: '.$begin.'-'.$end;
        $XaxisName = 'Временной период- Месяцы';
      }
}
function construction_interval_diagrams_day() {
   global $period, $FontSize, $number_of_days, $line_link, $Query, $ResultArray, $day_interval, $MonthsNames_double_for_day, $y_positions;
      if($_COOKIE["period"] = 3 ) {
        $period = 'daily';
        $begin = date("Y-m-d", strtotime($_COOKIE['begin']));
        $end = date("Y-m-d", strtotime($_COOKIE['end']));
        $year_begin = date("Y", strtotime($_COOKIE["begin"]));
        $year_end = date("Y", strtotime($_COOKIE["end"]));
        $data_begin = strtotime($_COOKIE["begin"]);
        $data_end = strtotime($_COOKIE["end"]);
        $data_sum = $data_end - $data_begin;
        $month_begin = date("n", strtotime($_COOKIE["begin"]));
        $month_end = date("n", strtotime($_COOKIE["end"]));
        $data_sum = $data_end - $data_begin;
        $day_begin = date("j", strtotime($_COOKIE["begin"]));
        $day_end = date("j", strtotime($_COOKIE["end"]));
        $range_days = $data_sum/86400;
        $range_days = ceil($range_days);
        unset($ResultArray);
        $ResultArray = array();
        $FontSize = '0px';
        $number_of_days = $range_days;

        if($month_begin == $month_end AND $year_begin == $year_end ): {
           for ($i = $day_begin; $i <=$day_end; $i++) {
                 $day_interval[$i] = $i.'-'.$MonthsNames_double_for_day[$month_begin].'/'.$year_begin;
           }
        }
        elseif($month_begin <= $month_end AND $year_begin == $year_end):{
           while ( $k <= $range_days) {
              for ($i_month = $month_begin; $i_month <= $month_end; $i_month++ ) {
                 if ($i_month == $month_begin) {
                    $begin_i = $day_begin;
                    $stop_i = monthdays($month_begin, $year_begin);
                 }
                 else {
                    $begin_i = 1;
                    if($i_month == $month_end) {
                       $stop_i = $day_end;
                    }
                    else {
                       $stop_i = monthdays($i_month, $year_begin);
                    }
                 }
                 for ($i = $begin_i; $i <=$stop_i; $i++) {
                    $day_interval[$k] = $i.'-'.$MonthsNames_double_for_day[$i_month].'/'.$year_begin;
                    $k++;
                 }
              }
           }
        }
        endif;

          $Query = "SELECT convert(char(8), Dat, 112) AS label, SUM( PrLine1 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE Dat BETWEEN '$begin' AND '$end' GROUP BY convert(char(8), Dat, 112) ORDER BY convert(char(8), Dat, 112)";
          $ResultArray = array_fill(1, $range_days, 0); // fill the Result array with 0 values for each month

        $y_positions = '1';
        $ChartHeading = 'Показатель по производству 4-х линий ЦПО в период: '.$begin.'-'.$end;
        $XaxisName = 'Временной период- Дни';
      }
}*/
function construction_diagrams() {
    global $period, $Year, $Month, $Day, $MonthsNames, $line, $first_year, $Year_interval, $last_year, $line_link, $Query, $ResultArray, $number_of_days, $FontSize, $y_positions;
    switch($period)
    {
        default:
        case 'yearly':

            $Query = "SELECT YEAR( Dat) AS label, SUM( PrLine1 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR(Dat) >=$first_year GROUP BY YEAR( Dat) ORDER BY YEAR(Dat)";
            $ResultArray = array_fill($first_year, 4, 0); // fill the Result array with 0 values for each month

            $y_positions = '0';
            $FontSize = '11px';
            $ChartHeading = 'Годовой развернутый показатель по производству 4-х линий ЦПО: '.$first_year.'-'.$last_year;
            $XaxisName = 'Период времени - Годы';
            break;
        case 'monthly':

            $Query = "SELECT MONTH( Dat) AS label, SUM( PrLine1 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR(Dat) = $Year GROUP BY MONTH( Dat) ORDER BY MONTH(Dat)";
            $ResultArray = array_fill(1, 12, 0); // fill the Result array with 0 values for each month

            $y_positions = '1';
            $FontSize = '11px';
            $ChartHeading = 'Месячный развернутый показатель по производству 4-х линий ЦПО: '.$Year;
            $XaxisName = 'Период времени - Месяцы';
            break;

        case 'daily':

            $Query = "SELECT DAY( Dat ) AS label, SUM( PrLine1 ) AS Total FROM  cpo.dbo.RsProizvCPO WHERE YEAR( Dat )= $Year AND MONTH( Dat )= $Month GROUP BY DAY( Dat ) ORDER BY DAY(Dat)";
            $number_of_days = monthdays($Month, $Year);
            $ResultArray = array_fill(1, $number_of_days, 0);  // fill the Result array with 0 values for each day

            $y_positions = '1';
            $FontSize = '0px';
            $ChartHeading = 'Дневной развернутый показатель по производству 4-х линий ЦПО: '.$MonthsNames[$Month].'/'.$Year;
            $XaxisName = 'Период времени - Дни';
            break;

        case 'hourly':
            $Query = "SELECT Hours_Smena AS Value, SUM( 1_line + 2_line + 3_line + 4_line ) AS Total FROM  cpo.dbo.RsProizvCPO WHERE YEAR(Dat)=$Year AND MONTH( Dat )={$Month} AND DAY(Dat)={$Day}
         GROUP BY MONTH( Dat ) , DAY( Dat ) , Value";
            $y_positions = '1';
            $ResultArray = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
            $ChartHeading = 'Hourly New Production figures for the Date: '.$Day.'/'.$MonthsNames[$Month].'/'.$Year;
            $XaxisName = 'Период времени - Часы';
            break;
    }
}
/*if(isset($_COOKIE["interval_chart"]) AND $_COOKIE["interval_chart"] == 1 AND $_COOKIE["node_name"] === "node/1") {
  switch($_COOKIE["period"]) {
    default:
    case 1:
    {
       construction_interval_diagrams_year();
    }
    break;

    case 2:
    {
       construction_interval_diagrams_month();
    }
    break;

    case 3:
    {
       construction_interval_diagrams_day();
    }
    break;
  }
}
else {
    construction_diagrams();
}*/
construction_diagrams();
//Connect to database
require 'sql_srv_conect.php';

//Query the database


$QueryResult = sqlsrv_query($conn, $Query);

//Fetch results in the Result Array
while($Row = sqlsrv_fetch_array($QueryResult))
    $ResultArray[$Row['label']]=$Row['Total'];


//Generate Chart XML: Head Part
$Output ="<chart caption='$ChartHeading' xaxisname='$XaxisName' rotateLabels='$y_positions' yaxisname='Произведено тыс. тонн' xAxisNameFontSize='14' yAxisNameFontSize='14' thousandSeparatorPosition='0' numberScaleValue = '2' showlabels='1' showvalues='1' decimals='2' formatNumberScale='0' FormatNumber='0' numberprefix='' numberSuffix=' тыс.' placevaluesinside='1' rotatevalues='1' valueFontSize='$FontSize' bgcolor='FFFFFF' legendshadow='0' legendborderalpha='50' canvasborderthickness='1' canvasborderalpha='50' palettecolors='#246624,#AFD8F8,#F6BD0F,#E8B989' showborder='0' theme='fint'>";

//Generate Chart XML: Main Body
switch($period)
{
    default:
    case 'yearly':

        $Output .= '<categories>';
        foreach ( $Year_interval as $value) {
            //$value .=' год';
            $Output .='<category label="'.$value.'" />';
        }
        $Output .= '</categories>';

        $Output .= '<dataset seriesname="Line 1">';
        foreach($ResultArray as $yearly => $value) {
            $value = $value/1000;
            $value = round($value, 2);
            $Output .= '<set value="'.$value.'" link="newchart-xmlurl-cpo_all_chart.php?type=monthly&amp;year='.$yearly.'&amp;number_line=line_1"/>';

            $Output .= '</dataset>';
        }

        break;
    case 'monthly':

        $Output .= '<categories>';
        foreach ( $MonthsNames as $value) {
            $Output .='<category label="'.$value.'" />';
        }
        $Output .= '</categories>';

        $Output .= '<dataset seriesname="Line 1">';
        foreach($ResultArray as $MonthNumber => $value) {
            $value = $value/1000;
            $value = round($value, 2);
            $Output .= '<set value="'.$value.'" link="newchart-xmlurl-cpo_all_chart.php?type=daily&amp;year='.$Year.'&amp;month='.$MonthNumber.'&amp;number_line=line_1"/>';

            $Output .= '</dataset>';
        }

        break;
    case 'daily':
        $Output .= '<categories>';
        if ($_COOKIE["period"] == 3) {
            foreach ( $day_interval as $value) {
                $Output .='<category label="'.$value.'" />';
            }
        }
        else {
            for ($day_value = 1; $day_value <= $number_of_days; $day_value++) {
                $value = $day_value;
                $value .= ' '.$MonthsNames[$Month-1];
                $Output .='<category label="'.$value.'" />';
            }
        }
        $Output .= '</categories>';

        $Output .= '<dataset seriesname="Line 1">';
        foreach($ResultArray as $DayNumber => $value) {
            $value = $value/1000;
            $value = round($value, 2);
            $Output .= '<set value="'.$value.'" link="newchart-xmlurl-cpo_all_chart.php?type=hourly&amp;year='.$Year.'&amp;month='.$Month.'&amp;day='.$DayNumber.'&amp;number_line=line_1"/>';

            $Output .= '</dataset>';
        }

        break;
    case 'hourly':
        foreach($ResultArray as $HourNumber => $value)  // HourNumber is hour (0-23)
            $Output .= '<set label="'.$HourNumber.'" value="'.$value.'"/>';
        break;
}
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;
