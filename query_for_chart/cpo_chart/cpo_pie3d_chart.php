<?php

$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$MonthsNames = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$line = array(NULL, '1-я линия', '2-я линия', '3-я линия', '4-я линия');
$line_link = array(NULL, 'PrLine1', 'PrLine2', 'PrLine3', 'PrLine4');
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
function construction_interval_diagrams_year() {
   global $period, $Year, $Month, $Day, $MonthsNames, $line, $Year_interval, $line_link, $Query, $ResultArray, $FontSize;
      if($_COOKIE["period"] = 1 ) {        
        $begin = $_COOKIE['begin'];
        $end = $_COOKIE['end'];
        $year_begin = date("Y", strtotime($_COOKIE["begin"]));
        $year_end = date("Y", strtotime($_COOKIE["end"]));
        $FontSize = '11px';
        for ($i = 1; $i <= 4; $i++) {        
          $Query[$i] = "SELECT convert(char(2), Dat, 112) AS label, SUM( $line_link[$i] ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR( Dat ) >= $year_begin AND YEAR( Dat ) <= $year_end GROUP BY convert(char(2), Dat, 112) ";          
          $range_year = $year_end - $year_begin;        
          $ResultArray[$i] = array_fill(1, 1, 0); // fill the Result array with 0 values for each month
        }                  
        $ChartHeading = 'Показатель по производству 4-х линий ЦПО в период: '.$year_begin.'-'.$year_end;
        $XaxisName = 'Временной период- Годы';               
      } 
}
function construction_interval_diagrams_month() {
    global $period, $FontSize, $line_link, $Query, $ResultArray;
      if($_COOKIE["period"] = 2 ) { 
        $period = 'monthly';       
        $begin = date("Y-m-d", strtotime($_COOKIE['begin']));
        $end = date("Y-m-d", strtotime($_COOKIE['end']));        
        for ($i = 1; $i <= 4; $i++) {        
          $Query[$i] = "SELECT convert(char(2), Dat, 112) AS label,  SUM( $line_link[$i] ) AS Total FROM cpo.dbo.RsProizvCPO WHERE (Dat BETWEEN '$begin' AND '$end' ) AND NOT Dat IN ('$end') GROUP BY convert(char(2), Dat, 112)";                   
          $ResultArray[$i] = array_fill(1, 1, 0); // fill the Result array with 0 values for each month          
        }                  
        $ChartHeading = 'Показатель по производству 4-х линий ЦПО в период: '.$begin.'-'.$end;
        $XaxisName = 'Временной период- Месяцы';                
      }       
}
function construction_interval_diagrams_day() {
    global $period, $FontSize, $line_link, $Query, $ResultArray;
      if($_COOKIE["period"] = 2 ) { 
        $period = 'monthly';       
        $begin = date("Y-m-d", strtotime($_COOKIE['begin']));
        $end = date("Y-m-d", strtotime($_COOKIE['end']));        
        for ($i = 1; $i <= 4; $i++) {        
          $Query[$i] = "SELECT convert(char(2), Dat, 112) AS label,  SUM( $line_link[$i] ) AS Total FROM cpo.dbo.RsProizvCPO WHERE (Dat BETWEEN '$begin' AND '$end' ) AND NOT Dat IN ('$end') GROUP BY convert(char(2), Dat, 112)";                   
          $ResultArray[$i] = array_fill(1, 1, 0); // fill the Result array with 0 values for each month          
        }                  
        $ChartHeading = 'Показатель по производству 4-х линий ЦПО в период: '.$begin.'-'.$end;
        $XaxisName = 'Временной период- Месяцы';                
      }    
}
function construction_diagrams() {
   global $period, $Year, $Month, $Day, $MonthsNames, $line, $first_year, $Year_interval, $last_year, $line_link, $Query, $ResultArray, $number_of_days, $FontSize;
     switch($period)
       {
       default:
       case 'yearly':
          for ($i = 1; $i <= 4; $i++) {
            $Query[$i] = "SELECT convert(char(2), Dat, 112) AS label, SUM( $line_link[$i] ) AS Total FROM cpo.dbo.RsProizvCPO WHERE Dat>='$first_year' GROUP BY convert(char(2), Dat, 112)";
            $ResultArray[$i] = array_fill(1, 1, 0);
          }                         
          $ChartHeading = 'Годовой показатель по производству 4-х линий ЦПО: '.$first_year;
          $XaxisName = 'Временной период- Годы';
       break;  
       case 'monthly':                
          for ($i = 1; $i <= 4; $i++) {
            $Query[$i] = "SELECT MONTH( Dat) AS label, SUM( $line_link[$i] ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR(Dat) = $Year GROUP BY MONTH( Dat) ORDER BY MONTH(Dat)";
            $ResultArray[$i] = array_fill(1, 1, 0); // fill the Result array with 0 values for each month
          }                  
          $ChartHeading = 'Месячный показатель по производству 4-х линий ЦПО: '.$Year;
          $XaxisName = 'Временной период- Месяцы';
       break;
       case 'daily':
          for ($i = 1; $i <= 4; $i++) {
            $Query[$i] = "SELECT DAY( Dat ) AS label, SUM( $line_link[$i] ) AS Total FROM  cpo.dbo.RsProizvCPO WHERE YEAR( Dat )= $Year AND MONTH( Dat )= $Month GROUP BY DAY( Dat ) ORDER BY DAY(Dat)";
            $number_of_days = monthdays($Month, $Year);          
            $ResultArray[$i] = array_fill(1, $number_of_days, 0);  // fill the Result array with 0 values for each day
          }              
          $ChartHeading = 'Дневной развернутый показатель по производству 4-х линий ЦПО: '.$MonthsNames[$Month].'/'.$Year;
          $XaxisName = 'Временной период- Дни';
       break;
       case 'hourly':
          $Query[$i] = "SELECT Hours_Smena AS Value, SUM( 1_line + 2_line + 3_line + 4_line ) AS Total FROM  performance WHERE YEAR(Dat)=$Year AND MONTH( Dat )={$Month} AND DAY(Dat)={$Day}
          GROUP BY MONTH( Dat ) , DAY( Dat ) , Value";
          $ResultArray = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
          $ChartHeading = 'Hourly New Production figures for the Date_Smenae: '.$Day.'/'.$MonthsNames[$Month].'/'.$Year;
          $XaxisName = 'Временной период- Часы';
          break;
     }
}
if(isset($_COOKIE["interval_chart"]) AND $_COOKIE["interval_chart"] == 1 AND $_COOKIE["node_name"] === "node/1") {
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
}  
//Connect to Date_Smenaabase
require 'sql_srv_conect.php';

//Query the Date_Smenaabase
for ($i = 1; $i <= 4; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);
   
   //Fetch results in the Result Array
   $Row = sqlsrv_fetch_array($QueryResult[$i]);
   $QueryResult[$i] = $Row['Total'];   
}
//Generate Chart XML: Head Part          
$Output ="<chart caption='$ChartHeading' subcaption='' palettecolors='#246624,#AFD8F8,#F6BD0F,#E8B989' bgcolor='#ffffff' numberSuffix=' тыс.' numberScaleValue = '2' showborder='0' use3dlighting='0' showshadow='0' enablesmartlabels='0' startingangle='0' showpercentvalues='1' showpercentintooltip='0' decimals='1' captionfontsize='14' subcaptionfontsize='14' subcaptionfontbold='0' tooltipcolor='#ffffff' tooltipborderthickness='0' tooltipbgcolor='#000000' tooltipbgalpha='80' tooltipborderradius='2' tooltippadding='5' showhovereffect='1' showlegend='1' legendbgcolor='#ffffff' legendborderalpha='0' legendshadow='0' legenditemfontsize='10' legenditemfontcolor='#666666' usedataplotcolorforlabels='1'>"; 

//Generate Chart XML: Main Body
switch($period)
{
    default:
      
    case 'yearly':
      for ($i = 1; $i <= 4; $i++) {      
            // Years is month number (2012-2015)
            $value = $QueryResult[$i];
            $value = $value/1000;
            $value = round($value, 2);
            $Output .= '<set label="'.$line[$i].'" value="'.$value.'"/>';
            
        }
        break;
        
    case 'monthly':
        for ($i = 1; $i <= 4; $i++) {       
            // Years is month number (2012-2015)
            $value = $QueryResult[$i];
            $value = $value/1000;
            $value = round($value, 2);
            $Output .= '<set label="'.$line[$i].'" value="'.$value.'"/>'; 
        }        
        break;
        
    case 'daily':      
        foreach($ResultArray[$i] as $DayNumber => $value)  // DayNumber is day (1-31)
            $value = $value/1000;
            $value = round($value, 2);
            $Output .= '<set label="'.$DayNumber.'" value="'.$value.'"/>';

        break;
    case 'hourly':        
        foreach($ResultArray[$i] as $HourNumber => $value)  // HourNumber is hour (0-23)
            $value = $value/1000;
            $value = round($value, 2);
            $Output .= '<set label="'.$HourNumber.'" value="'.$value.'"/>';
}
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;
?>