<?php
/*
 * Date: 21.05.2015
 * Time: 8:24
 */
$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$MonthsNames = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$MonthsNames_double = array(NULL, 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$MonthsNames_double_for_day = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$chemical_element = array(NULL, 'Fe', 'FeKNC');
$day_interval = array();
$last_year = date("Y"); 
$Year_interval = array();
$today_all_date = date('Y-m-d');
$today_year = date('Y');
$today_day = date('d');
$today_day = $today_day;
$today_month = date('m'); 
$today_month = $today_month - 1;
$previous_month = $today_month;
$today_m = date('m'); 
if ($today_month <=10) {
  $today_month = '0'.$today_month;
}
$today_all = $today_year.'-'.$today_month.'-'.$today_day; 
//echo '<br>'.'$today--'.$today_all.'</br>';
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_diagrams() {
   global $period, $Year, $Month, $Day, $MonthsNames, $namber_base, $line, $first_year, $Year_interval, $last_year, $line_link, $Query, $ResultArray, $number_of_days, $FontSize, $y_positions, $today_m, $today_year_all, $today_all_date, $today_year, $today_all;
     switch($period)
       {
       default:
       case 'yearly':  
          unset($ResultArray);         
          $ResultArray = array();                 
          for ($i = 1; $i <=2; $i++) {
             if ($i == 1) {
               $label_fe = 'Total'; 
               $label_fekn = 'FeKNC';
             } 
             else {
               $label_fe = 'Fe'; 
               $label_fekn = 'Total';
             } 
             $Query[$i] = "DECLARE @start_date datetime, @end_date datetime
                    SET @start_date = '$today_all'
                    SET @end_date = '$today_all_date'
                    SELECT CONVERT(varchar, tab1.OrderDate, 104) as Value, tab1.Ruda,
	                CASE WHEN sK22 > 0 THEN sFe/sK22 ELSE 0 END as $label_fe, 
	                CASE WHEN sK22 > 0 THEN sFeKNC/sK22 ELSE 0 END as $label_fekn 
                    FROM
                   (
	                SELECT MAX(OrderDate) as OrderDate, MAX(Ruda) as Ruda, 
		            SUM(Fe*Tonn_K22) as sFe, 
		            SUM(FeKNC*Tonn_K22) as sFeKNC,               
		            SUM(Tonn_K22) as sK22                      
	                FROM
	               (
	                SELECT OrderDate, Ruda, Fe, FeMg, FeKNC, Tonn as Tonn_K22
	                FROM [SVODKA].[otk_ru].[ru_mtn].[Priem_Ekg_Q]
           	        WHERE ((OrderDate >= @start_date) AND (OrderDate <= @end_date)) AND (Ruda = 'K2/2') 
	                AND (Tonn > 0) AND ((Fe > 0) AND (FeMg > 0) AND (FeKNC > 0)) 	  
	               ) tab
	   	            GROUP BY OrderDate, Ruda 
                   ) tab1
                    ORDER BY tab1.Ruda, tab1.OrderDate";
              
             $number_of_days = monthdays($today_m, $today_year);              
             $ResultArray[$i] = array_fill(1, $number_of_days, 0); // fill the Result array with 0 values for each month 
          }                              
          $y_positions = '0'; 
          $FontSize = '11px';                  
          $ChartHeading = 'Годовой развернутый показатель по производству ОФ-1 и ОФ-2: '.$first_year.'-'.$last_year;
          $XaxisName = 'Период времени - Годы';
       break;  
       case 'monthly':                
          for ($i = 1; $i <= 2; $i++) {
             $Query[$i] = "SELECT MONTH( OrderDate) AS label, SUM( M_All ) AS Total FROM otk_ru.ru_mtn.RudaS_WM_OF1 WHERE YEAR(OrderDate) = $Year GROUP BY MONTH( OrderDate) ORDER BY MONTH(OrderDate)";
             $ResultArray[$i] = array_fill(1, 12, 0); // fill the Result array with 0 values for each month
          }  
          $y_positions = '1';
          $FontSize = '11px';                 
          $ChartHeading = 'Месячный развернутый показатель по производству ОФ-1 и ОФ-2: '.$Year;
          $XaxisName = 'Период времени - Месяцы';
       break;

       case 'daily':
          for ($i = 1; $i <= 2; $i++) {           
             $Query[$i] = "SELECT DAY( OrderDate ) AS label, SUM( M_All ) AS Total FROM  otk_ru.ru_mtn.RudaS_WM_OF1 WHERE YEAR( OrderDate )= $Year AND MONTH( OrderDate )= $Month GROUP BY DAY( OrderDate ) ORDER BY DAY(OrderDate)";
             $number_of_days = monthdays($Month, $Year);          
             $ResultArray[$i] = array_fill(1, $number_of_days, 0);  // fill the Result array with 0 values for each day
          }    
          $y_positions = '1';    
          $FontSize = '0px';      
          $ChartHeading = 'Дневной развернутый показатель по производству ОФ-1 и ОФ-2: '.$MonthsNames[$Month].'/'.$Year;
          $XaxisName = 'Период времени - Дни';
       break;

       case 'hourly':
         $Query[$i] = "SELECT Hours_Smena AS Value, SUM( 1_line + 2_line + 3_line + 4_line ) AS Total FROM  otk_ru.ru_mtn.RudaS_WM_OF1 WHERE YEAR(Dat)=$Year AND MONTH( OrderDate )={$Month} AND DAY(OrderDate)={$Day}
         GROUP BY MONTH( OrderDate ) , DAY( OrderDate ) , Value";
         $y_positions = '1';
         $ResultArray = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
         $ChartHeading = 'Hourly New Production figures for the OrderDatee: '.$Day.'/'.$MonthsNames[$Month].'/'.$Year;
         $XaxisName = 'Период времени - Часы';
       break;
     }
}

construction_diagrams();
 
//Connect to database
require 'sql_srv_conect_svodka.php';

//Query the database

for ($i = 1; $i <= 2; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);  
   //print_r($Query);
//Fetch results in the Result Array 
   while($Row = sqlsrv_fetch_array($QueryResult[$i]))
      $ResultArray[$i][$Row['Value']] = $Row['Total'];         
}      
//print_r($ResultArray);
//Generate Chart XML: Head Part
$Output ="<chart caption='$ChartHeading' xaxisname='$XaxisName' yAxisMaxValue='38' yAxisMinValue='32' rotateLabels='0' yaxisname='chemical analysis of ores' xAxisNameFontSize='14' yAxisNameFontSize='14' thousandSeparatorPosition='0' numberScaleValue = '2' showlabels='1' showvalues='1' decimals='2' formatNumberScale='0' FormatNumber='0' numberprefix='' numberSuffix='%' placevaluesinside='1' rotatevalues='1' valueFontSize='$FontSize' bgcolor='FFFFFF' legendshadow='0' legendborderalpha='50' canvasborderthickness='1' canvasborderalpha='50' palettecolors='#AFD8F8,#F6BD0F,#E8B989' showborder='0' theme='ocean'>"; 

//Generate Chart XML: Main Body
switch($period)
{
    default:
      case 'yearly':
            //$number_of_days = monthdays($today_month, $today_year);
            $number_of_days = monthdays($previous_month, $today_year);
            $Output .= '<categories>';
            for($j = $today_day; $j <= $number_of_days; $j++) {            
            /*for ($day_value = 1; $day_value <= $number_of_days; $day_value++) {*/
               $value = $j; 
               $value .= ' '.$MonthsNames_double_for_day[$previous_month];             
               $Output .='<category label="'.$value.'" />';
               }
            for($n = 1; $n <= $today_day; $n++) {
               $value = $n; 
               $value .= ' '.$MonthsNames_double_for_day[$previous_month+1];             
               $Output .='<category label="'.$value.'" />';
            }                           
            $Output .= '</categories>'; 
            for ($i = 1; $i <= 2; $i++) {
               $Output .= '<dataset seriesname="'.$chemical_element[$i].'">';                             
                   foreach($ResultArray[$i] as $yearly => $value) {                   
                   $value = round($value, 2); 
                   $Output .= '<set value="'.$value.'"/>';                            
                   }     
                $Output .= '</dataset>';        	           
            }
                    	
        break;
      case 'monthly':

            $Output .= '<categories>';
            foreach ( $MonthsNames as $value) {
              $Output .='<category label="'.$value.'" />';
            }                       
            $Output .= '</categories>'; 
              for ($i = 1; $i <= 2; $i++) {               
                 $Output .= '<dataset seriesname="'.$line[$i].'">';                             
                   foreach($ResultArray[$i] as $MonthNumber => $value) {
                   $value = $value/1000;
                   $value = round($value, 2); 
                   $Output .= '<set value="'.$value.'" link="newchart-xmlurl-ob_all_chart.php?type=daily&amp;year='.$Year.'&amp;month='.$MonthNumber.'&amp;number_line=line"/>';         
                   }     
        	     $Output .= '</dataset>';   	      
              }  
                   	
        break;
      case 'daily':
            $Output .= '<categories>';
            //foreach ( $quantity_days_in_month as $value)
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
              for ($i = 1; $i <= 2; $i++) {               
                 $Output .= '<dataset seriesname="'.$line[$i].'">';                             
                   foreach($ResultArray[$i] as $DayNumber => $value) {
                   $value = $value/1000;
                   $value = round($value, 2);
                   $Output .= '<set value="'.$value.'" link="newchart-xmlurl-ob_all_chart.php?type=hourly&amp;year='.$Year.'&amp;month='.$Month.'&amp;day='.$DayNumber.'&amp;number_line='.M_All.'"/>';         
                   }     
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
?>

