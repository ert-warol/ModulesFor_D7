<?php
/*
 * Date: 21.05.2015
 * Time: 8:24
 */
$MonthsNames = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$MonthsNames_double = array(NULL, 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$MonthsNames_double_for_day = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$chemical_element = array(NULL, 'Fe', 'FeKNC', 'FeMg');
$day_interval = array();
$last_year = date("Y"); 
$Year_interval = array();
$today_all_date = date('Y-m-d');
$today_year = date('Y');
$today_month = date('m');
$today_month_number = date('n');
$today_day = date('j');
$last_month = $today_month - 1;
$last_month_day_begin = $today_day + 1;

if($last_month == 0) {
   $last_month = 12;
   $last_year = $today_year - 1;
}
else {
   $last_year = $today_year;
}

$today_begin = $last_year.'-'.$last_month.'-'.$last_month_day_begin;
 
if ($today_month <=10) {
  $today_month = '0'.$today_month;
}

$today_all = $today_year.'-'.$today_month.'-'.$today_day;

function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_diagrams() {
   global $MonthsNames, $today_begin, $namber_base, $line, $first_year, $Year_interval, $last_year, $Query, $ResultArray, $FontSize, $y_positions, $today_m, $today_year_all, $today_all_date, $today_year, $today_all, $ChartHeading, $XaxisName, $range_days;
      unset($ResultArray);         
      $ResultArray = array();                 
      for ($i = 1; $i <=3; $i++) {
         if($i == 1): {
           $label_fe = 'Total'; 
           $label_fekn = 'FeKNC';
           $label_FeMg = 'FeMg';
         } 
         elseif($i == 2): {
            $label_fe = 'Fe'; 
            $label_fekn = 'Total';
            $label_FeMg = 'FeMg';
         }
         elseif($i == 3): {
            $label_FeMg = 'Total';
            $label_fekn = 'FeKNC';
            $label_fe = 'Fe';
         }
         endif;
             
         $Query[$i] = "DECLARE @start_date datetime, @end_date datetime
                    SET @start_date = '$today_begin'
                    SET @end_date = '$today_all_date'
                    SELECT CONVERT(varchar, tab1.OrderDate, 104) as Value, tab1.Ruda,
                    CASE WHEN sK22 > 0 THEN sFe/sK22 ELSE 0 END as $label_fe, 
                    CASE WHEN sK22 > 0 THEN sFeKNC/sK22 ELSE 0 END as $label_fekn,
                    CASE WHEN sK22 > 0 THEN sFeMg/sK22 ELSE 0 END as $label_FeMg
                    FROM
                   (
                    SELECT MAX(OrderDate) as OrderDate, MAX(Ruda) as Ruda, 
                    SUM(Fe*Tonn_K22) as sFe, 
                    SUM(FeKNC*Tonn_K22) as sFeKNC,
                    SUM(FeMg*Tonn_K22) as sFeMg,               
                    SUM(Tonn_K22) as sK22                      
                    FROM
                   (
                    SELECT OrderDate, Ruda, Fe, FeMg, FeKNC, Tonn as Tonn_K22
                    FROM [otk_ru].[ru_mtn].[Priem_Ekg_Q]
                    WHERE ((OrderDate >= @start_date) AND (OrderDate <= @end_date)) AND (Ruda = 'K2/2') 
                    AND (Tonn > 0) AND ((Fe > 0) AND (FeMg > 0) AND (FeKNC > 0) AND (FeMg > 0))       
                   ) tab
                    GROUP BY OrderDate, Ruda 
                   ) tab1
                    ORDER BY tab1.Ruda, tab1.OrderDate";        
                       
             $today_day = date('j');              
             $ResultArray[$i] = array_fill(1, $today_day, 0); // fill the Result array with 0 values for each month 
          }                              
          $y_positions = '0'; 
          $FontSize = '11px';                  
          $ChartHeading = 'Chemical analysis of ore K2/2 Fe, FeKNC, FeMg: from '.$today_begin.' to '.$today_all_date;
          $XaxisName = 'Daily rate';
      
}
function construction_diagrams_interval () {
   global $MonthsNames, $first_year, $Year_interval, $last_year, $line_link, $Query, $ResultArray, $FontSize, $y_positions, $today_m, $today_year_all, $today_all_date, $today_year, $ChartHeading, $XaxisName, $range_days, $begin;
   unset($ResultArray);         
   $ResultArray = array();                    
      for ($i = 1; $i <=3; $i++) {
         if($i == 1): {
           $label_fe = 'Total'; 
           $label_fekn = 'FeKNC';
           $label_FeMg = 'FeMg';
         } 
         elseif($i == 2): {
            $label_fe = 'Fe'; 
            $label_fekn = 'Total';
            $label_FeMg = 'FeMg';
         }
         elseif($i == 3): {
            $label_FeMg = 'Total';
            $label_fekn = 'FeKNC';
            $label_fe = 'Fe';
         }
         endif;
         
        $begin = date("Y-m-d", strtotime($_COOKIE['begin_interval_day']));
        $end = date("Y-m-d", strtotime($_COOKIE['end_interval_day']));
        //$begin = $_COOKIE['begin_interval_day'];
        //$end = $_COOKIE['end_interval_day'];
         
         $Query[$i] = "DECLARE @start_date datetime, @end_date datetime
                    SET @start_date = '$begin'
                    SET @end_date = '$end'
                    SELECT CONVERT(varchar, tab1.OrderDate, 104) as Value, tab1.Ruda,
                    CASE WHEN sK22 > 0 THEN sFe/sK22 ELSE 0 END as $label_fe, 
                    CASE WHEN sK22 > 0 THEN sFeKNC/sK22 ELSE 0 END as $label_fekn,
                    CASE WHEN sK22 > 0 THEN sFeMg/sK22 ELSE 0 END as $label_FeMg
                    FROM
                   (
                    SELECT MAX(OrderDate) as OrderDate, MAX(Ruda) as Ruda, 
                    SUM(Fe*Tonn_K22) as sFe, 
                    SUM(FeKNC*Tonn_K22) as sFeKNC,
                    SUM(FeMg*Tonn_K22) as sFeMg,               
                    SUM(Tonn_K22) as sK22                      
                    FROM
                   (
                    SELECT OrderDate, Ruda, Fe, FeMg, FeKNC, Tonn as Tonn_K22
                    FROM [otk_ru].[ru_mtn].[Priem_Ekg_Q]
                    WHERE ((OrderDate >= @start_date) AND (OrderDate <= @end_date)) AND (Ruda = 'K2/2') 
                    AND (Tonn > 0) AND ((Fe > 0) AND (FeMg > 0) AND (FeKNC > 0) AND (FeMg > 0))       
                   ) tab
                    GROUP BY OrderDate, Ruda 
                   ) tab1
                    ORDER BY tab1.Ruda, tab1.OrderDate";
                 
             $data_begin = strtotime($_COOKIE["begin_interval_day"]);
             $data_end = strtotime($_COOKIE["end_interval_day"]);
             $data_sum = $data_end - $data_begin;
             $range_days = $data_sum/86400 + 1;       
             $ResultArray[$i] = array_fill(1, $range_days, 0); // fill the Result array with 0 values for each month 
          }                              
          $y_positions = '0'; 
          $FontSize = '11px';                  
          $ChartHeading = 'Chemical analysis of ore K2/2 Fe, FeKNC, FeMg: from '.$begin.' to '.$end;
          $XaxisName = 'Daily rate'; 
}
if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/11") { 
   construction_diagrams_interval();    
}
else {
   construction_diagrams();    
}
 
//Connect to database
require 'sql_srv_conect_svodka.php';

//Query the database
//print_r($Query);
for ($i = 1; $i <= 3; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);  
   
//Fetch results in the Result Array 
   while($Row = sqlsrv_fetch_array($QueryResult[$i]))
      $ResultArray[$i][$Row['Value']] = $Row['Total'];         
}    

//Generate Chart XML: Head Part
$Output ='<chart exportenabled="1" exportAtClientSide="1" slantlabels="1" rotateLabels="1" legendItemFontSize="14" caption="'.$ChartHeading.'" xaxisname="'.$XaxisName.'" yaxisname="chemical analysis of ores" showvalues="0" palettecolors="#246624,#F72B08,#2207F3" theme="fint">'; 

//Generate Chart XML: Main Body            
            for ($i = 1; $i <=3; $i++) {
              $max_value[$i] = 0;
              $min_value[$i] = 100;  
               foreach($ResultArray[$i] as $yearly => $value) {
                  if($value > 0) {
                     if($max_value[$i] < $value) {
                        $max_value[$i] = $value;
                    }
                     if($min_value[$i] > $value) {
                        $min_value[$i] = $value;
                    }      
                  }
               }
               $max_value[$i] = $max_value[$i] + 0.1;
               $min_value[$i] = $min_value[$i] - 0.1;
            }

            $scale_y_min = array(NULL, $min_value[1], $min_value[2], $min_value[3]);
            $scale_y_max = array(NULL, $max_value[1], $max_value[2], $max_value[3]);
            $scale_y = array(NULL, '1', '0', '0');
            $today_month = date('m');
            $year_begin = date("Y", strtotime($_COOKIE["begin_interval_day"]));
            $year_end = date("Y", strtotime($_COOKIE["end_interval_day"]));
            $month_begin = date("n", strtotime($_COOKIE["begin_interval_day"]));
            $month_end = date("n", strtotime($_COOKIE["end_interval_day"])); 
            $day_begin = date("j", strtotime($_COOKIE["begin_interval_day"]));
            $day_end = date("j", strtotime($_COOKIE["end_interval_day"]));                   
            $Output .= '<categories>';
            if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/11") {
               if ($year_begin < $year_end) {
                 for($year = $year_begin;  $year <= $year_end; $year++) {
                    
                    if ($year == $year_end) {                   
                       $interval_month_end = $month_end;
                       $interval_month_begin = 1;
                    }
                    else {
                       $interval_month_end = 12;
                       $interval_month_begin = $month_begin; 
                    }
                    for ($month = $interval_month_begin; $month <= $interval_month_end; $month++) {
                       $interval_day_end = monthdays($month, $year);
                          $interval_day_begin = 1;
                       if ($month == $month_end AND $year == $year_end): {
                          $interval_day_end = $day_end; 
                          $interval_day_begin = 1;    
                       }
                       elseif ($month == $month_begin AND $year == $year_begin): {
                          $interval_day_end = monthdays($month, $year);
                          $interval_day_begin = $day_begin;  
                       }                       
                       endif;
                          for ($day = $interval_day_begin; $day <= $interval_day_end; $day++) {
                             $value = $day.' '.$MonthsNames_double_for_day[$month].' '.$year;
                             $Output .='<category label="'.$value.'" />';  
                       }
                    } 
                 }  
               }
               else {
                  if ($month_begin == $month_end ) {
                        $interval_day_begin = $day_begin;
                        $interval_day_end = $day_end;                     
                          for ($day = $interval_day_begin; $day <= $interval_day_end; $day++) {
                             $value = $day.' '.$MonthsNames_double_for_day[$month].' '.$today_year;                                    
                             $Output .='<category label="'.$value.'" />';
                     }
                  }
                  else {
                     for ($month = $month_begin; $month <= $month_end; $month++) {
                        if ($month == $month_end): {
                           $interval_day_end = $day_end; 
                           $interval_day_begin = 1;    
                        }
                        elseif ($month == $month_begin): {
                           $interval_day_end = monthdays($month_begin, $today_year);
                           $interval_day_begin = $day_begin;  
                        }
                        elseif ($month > $month_begin AND $month < $month_end): {
                           $interval_day_end = monthdays($month, $today_year);
                           $interval_day_begin = 1;
                        }                     
                        endif;                                          
                        for ($day = $interval_day_begin; $day <= $interval_day_end; $day++) {
                           $value = $day.' '.$MonthsNames_double_for_day[$month].' '.$today_year;                                    
                           $Output .='<category label="'.$value.'" />';
                        }
                     }   
                  }
               }               
            } 
            else {
               if($last_month == 12) {                  
                  $today_year = $today_year - 1;
                  $today_month = 1;
               }
               $last_month_day = monthdays($last_month, $today_year);
               for ($day = $today_day + 1; $day <= $last_month_day ; $day++) {
                  $value = $day.' '.$MonthsNames_double_for_day[$last_month];                                    
                  $Output .='<category label="'.$value.'" />';
               }
            for ($day = 1; $day <= $today_day; $day++) {
                  $value = $day.' '.$MonthsNames_double_for_day[$today_month_number];                                    
                  $Output .='<category label="'.$value.'" />';
               }
            }                                   
            $Output .= '</categories>'; 
            for ($i = 1; $i <= 3; $i++) {
               $Output .= '<axis title="'.$chemical_element[$i].'" tickwidth="10" numberprefix="%" divlinedashed="1" numdivlines="4" minValue="'.$scale_y_min[$i].'" maxValue="'.$scale_y_max[$i].'" axisOnLeft="'.$scale_y[$i].'">';
               $Output .= '<dataset seriesname="'.$chemical_element[$i].'">';                             
                   foreach($ResultArray[$i] as $yearly => $value) {                   
                   $value = round($value, 2); 
                   $Output .= '<set value="'.$value.'"/>';                            
                   }     
                $Output .= '</dataset>';
                $Output .= '</axis>';        	           
            }                  	
     
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;



