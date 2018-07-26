<?php
 
$MonthsNames_double_for_day = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$hours_period = array('21', '22', '23', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
$day_begin = date("j", strtotime($base_date));
$today_all_date = date('Y-m-d');
$today_year = date('Y');
$today_day = date('d');
$today_month = date('m');
$today_month_number = date('n');
$namber_value = array(NULL, 'G1', 'G1+delta', 'G2' );
$color_line = array(NULL, '#0000FF', '#246624');
$alpha = array(NULL, '100', '100');
$valuePosition = array(NULL, 'BELOW', 'AUTO', 'ABOVE' ); 
$last_month = $today_month - 1;

if($last_month == 0) {
   $last_month = 12;
   $last_year = $today_year - 1;
}
else {
   $last_year = $today_year;
}
$last_month_day_begin = $today_day + 1;
$today_begin = $last_year.'-'.$last_month.'-'.$last_month_day_begin;

if ($today_month <=10) {
  $today_month = '0'.$today_month;
}
//$today_all = $today_year.'-'.$today_month.'-'.'01'; 
//echo '<br>'.'$today--'.$today_all.'</br>';
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_diagrams() {
   global $ChartHeading, $XaxisName, $base_date, $Query, $ResultArray, $FontSize, $y_positions, $today_all_date, $today_begin ;
          $range_days = monthdays($today_month, $today_year) * 2;
          unset($ResultArray);         
          $ResultArray = array();               
          
          $Query[1] = "SELECT  Line, Smena, (convert(char(8), Dat, 112) + '-' )+ltrim(rtrim(str(Smena))) AS label, L8 AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_begin' AND '$today_all_date' ) AND Line = 2
                    GROUP BY Line, Dat, Smena, L8  
                    ORDER BY Dat";              
                        
          $ResultArray[1] = array_fill(1, $range_days, 0); 
          $Query[2] = "SELECT  Line, Smena, (convert(char(8), Dat, 112) + '-' )+ltrim(rtrim(str(Smena))) AS label, (GazObg*1000)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_begin' AND '$today_all_date' ) AND Line = 2
                    GROUP BY Line, Dat, Smena, GazObg  
                    ORDER BY Dat";              
                        
          $ResultArray[2] = array_fill(1, $range_days, 0);
          
          $y_positions = '0';                            
          $ChartHeading = 'Gas consumption period : from '.$today_begin.' to '.$today_all_date;
          $XaxisName = '';      
}
function construction_diagrams_interval () {
  
global $ChartHeading, $XaxisName, $base_date, $Query, $ResultArray, $FontSize, $y_positions, $today_all_date;  
   $begin = date("Y-m-d", strtotime($_COOKIE['begin_interval_day']));
   $end = date("Y-m-d", strtotime($_COOKIE['end_interval_day']));
   $data_begin = strtotime($_COOKIE["begin_interval_day"]);
   $data_end = strtotime($_COOKIE["end_interval_day"]);
   $data_sum = $data_end - $data_begin;
   $range_days_smen = ($data_sum/86400 + 1)*2;
          
          unset($ResultArray);         
          $ResultArray = array();             
           
          $Query[1] = "SELECT  Line, Smena, (convert(char(8), Dat, 112) + '-' )+ltrim(rtrim(str(Smena))) AS label, L8 AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$begin' AND '$end' ) AND Line = 2
                    GROUP BY Line, Dat, Smena, L8  
                    ORDER BY Dat";              
                        
          $ResultArray[1] = array_fill(1, $range_days_smen, 0); 
          $Query[2] = "SELECT  Line, Smena, (convert(char(8), Dat, 112) + '-' )+ltrim(rtrim(str(Smena))) AS label, (GazObg*1000)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$begin' AND '$end' ) AND Line = 2
                    GROUP BY Line, Dat, Smena, GazObg  
                    ORDER BY Dat";              
                        
          $ResultArray[2] = array_fill(1, $range_days_smen, 0);
          
          $y_positions = '0';                            
          $ChartHeading = 'Gas consumption period : with '.$begin.' from '.$end;
          $XaxisName = '';
}

if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21") { 
   construction_diagrams_interval();    
}
else {
construction_diagrams();
} 
//Connect to database
require 'sql_srv_conect.php';

//Query the database
//print_r($Query);
for ($i = 1; $i <= 2; $i++) {

$QueryResult = sqlsrv_query($conn, $Query[$i]);  
   
//Fetch results in the Result Array 
   while($Row = sqlsrv_fetch_array($QueryResult))
      $ResultArray[$i][$Row['label']] = $Row['Total'];        
}
//print_r($ResultArray);
//Generate Chart XML: Head Part
$Output ="<chart exportenabled='1' exportAtClientSide='1' slantlabels='1' caption='$ChartHeading' xaxisname='$XaxisName' showHoverEffect='1' plotFillHoverColor='#D63713' anchorRadius='4' showValues='0' showLabels='0' showValuesOnHover='0' xAxisNameFontBold='1' outCnvBaseFontSize='14' baseFontSize='14' tickValueDistance='14' yAxisNameFont='Calibri' captionFont='Calibri' valueFont='Calibri' subCaptionFont='calibri' baseFont='calibri' baseChartMessageFont='Calibri' yAxisMinValue='28000' yAxisMaxValue='70000' rotateLabels='1' yaxisname='Gas consumption m3' xAxisNameFontSize='14' yAxisNameFontSize='14' CaptionFontSize='14' thousandSeparatorPosition='0' numberScaleValue = '1' showlabels='1' showvalues='1' decimals='1' formatNumberScale='0' FormatNumber='0' numberprefix='' numberSuffix=' ' placevaluesinside='1' rotatevalues='1' valueFontSize='14' bgcolor='FFFFFF' legendshadow='0' legendborderalpha='50' canvasborderthickness='1' canvasborderalpha='50' palettecolors='#246624' showborder='0' theme='carbon'>"; 

//Generate Chart XML: Main Body            
            
            $today_month = date('m');
            $year_begin = date("Y", strtotime($_COOKIE["begin_interval_day"]));
            $year_end = date("Y", strtotime($_COOKIE["end_interval_day"]));
            $month_begin = date("n", strtotime($_COOKIE["begin_interval_day"]));
            $month_end = date("n", strtotime($_COOKIE["end_interval_day"])); 
            $day_begin = date("j", strtotime($_COOKIE["begin_interval_day"]));
            $day_end = date("j", strtotime($_COOKIE["end_interval_day"]));
            $Output .= '<categories font="calibri">';
            if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21") {
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
                             for($smena = 1; $smena <= 2; $smena++ ) {
                                $value = $day.' '.$MonthsNames_double_for_day[$month].' '.$smena.'sm';                                
                                $Output .='<category label="'.$value.'" />';  
                             }
                          }
                    } 
                 }  
               }
               else {
                  if ($month_begin == $month_end ) {
                     $interval_day_begin = $day_begin;
                     $interval_day_end = $day_end;                     
                     for ($day = $interval_day_begin; $day <= $interval_day_end; $day++) {
                        for($smena = 1; $smena <= 2; $smena++ ) {
                           $value = $day.'-'.$MonthsNames_double_for_day[$month_begin].' '.$smena.'sm';                                     
                           $Output .='<category label="'.$value.'" />';
                        }   
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
                           for($smena = 1; $smena <= 2; $smena++ ) {
                              $value = $day.'-'.$MonthsNames_double_for_day[$month].' '.$smena.'sm'; 
                              $Output .='<category label="'.$value.'" />';
                           }
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
               
               for($day = $today_day + 1; $day <= $last_month_day; $day++) {
                             
                  for($smena = 1; $smena <= 2; $smena++ ) {
                     $value = $day.'-'.$MonthsNames_double_for_day[$last_month].' '.$smena.'sm'; 
                     $Output .='<category label="'.$value.'" />';
                  }
               }
               for ($day = 1; $day <= $today_day; $day++) {
                 
                  for($smena = 1; $smena <= 2; $smena++ ) {
                    
                     $value = $day.' '.$MonthsNames_double_for_day[$today_month_number].' '.$smena.'sm';                                    
                     $Output .='<category label="'.$value.'" />';
                  }
               }
            }    	           
            $Output .= '</categories>'; 
            for ($i = 1; $i <= 2; $i++) {
                         
               $Output .= '<dataset seriesname="'.$namber_value[$i].'" color="'.$color_line[$i].'" alpha="'.$alpha[$i].'" drawAnchors="1" valuePosition="'.$valuePosition[$i].'">';                             
                  foreach($ResultArray[$i] as $smena => $value) {                   
                     $value = round($value, 1); 
                     $Output .= '<set showValue="0" value="'.$value.'"/>';                            
                  }     
               $Output .= '</dataset>';
            }
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;






