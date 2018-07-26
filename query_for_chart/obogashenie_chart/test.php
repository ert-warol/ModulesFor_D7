<?php
//session_start();
//setcookie();
//print_r($_COOKIE);
//echo $_COOKIE["begin_interval"];
//echo $_COOKIE["end_interval"];
//echo $_COOKIE['PHPSESSID'];
//echo $_COOKIE['interval_chart'];
//print_r($_COOKIE);
$MonthsNames = array();
$MonthsNames_double = array('Jan', 'Feb', 'Mar', 'April', 'May', 'Juin', 'Jil', 'Augost', 'Sektemb', 'Oktober', 'November', 'Desember');


        $begin = date("Y-m-d", strtotime($_COOKIE['begin']));
        $end = date("Y-m-d", strtotime($_COOKIE['end']));
        $year_begin = date("Y", strtotime($_COOKIE["begin"]));
        $year_end = date("Y", strtotime($_COOKIE["end"]));
        $month_begin = date("n", strtotime($_COOKIE["begin"]));
        $month_end = date("n", strtotime($_COOKIE["end"]));
        $range_year = $year_end - $year_begin;
        $range_month = $range_year*12+($month_end-$month_begin); 
        $day_begin = date("j", strtotime($_COOKIE["begin"]));
        $day_end = date("j", strtotime($_COOKIE["end"]));
        
        echo '<br>'.'$begin-'.$begin.'</br>';
        echo '<br>'.'$end-'.$end.'</br>';
        echo '<br>'.'$year_begin-'.$year_begin.'</br>';
        echo '<br>'.'$year_end-'.$year_end.'</br>';
        echo '<br>'.'$month_begin-'.$month_begin.'</br>';
        echo '<br>'.'$month_end-'.$month_end.'</br>';
        echo '<br>'.'$range_year-'.$range_year.'</br>';
        echo '<br>'.'$range_month-'.$range_month.'</br>';
        echo '<br>'.'$day_begin--'.$day_begin.'</br>';
        echo '<br>'.'$day_end--'.$day_end.'</br>';
        $MonthsNames = array();
        if($year_begin == $year_end) {           
           for ($i = $month_begin-1; $i <=$month_end-1; $i++) {                 
                 $MonthsNames[$i] = $MonthsNames_double[$i].'-'.$year_begin;
                 //echo '<br>'.'month < 12__'.$i.'</br>';
              }             
        }
        else {           
           
           $k = 1;
           while ( $k <= $range_month) {
              for($j = $year_begin; $j <= $year_end; $j++ ) {
                 if ($j = $year_end) {
                    $stop_i = $month_end;
                    echo '<br>'.'1-variant'.'</br>';
                 }
                 else {
                    $stop_i = 12; 
                    echo '<br>'.'2-variant'.'</br>';                   
                 }
                 if ($j > $year_begin ) {
                   $begin_i = 1;
                   echo '<br>'.'3-variant'.'</br>';
                 }
                 else {
                   $begin_i = $month_begin;
                   echo '<br>'.'4-variant'.'</br>';
                 }
                 for ($i = $begin_i; $i <=$stop_i; $i++) {
                    $MonthsNames[$k] = $MonthsNames_double[$i].'-'.$j;
                    $k++;
                    
                 }
              }              
           }
        }
        //print_r($MonthsNames);
  
$today = date('Y-m-d');
$today_day = date('d');
$today_year = date('Y');
$today_moth = date('m'); 
$today_moth = $today_moth - 1;
$today_m = date('m'); 
if ($today_moth <=10) {
  $today_moth = '0'.$today_moth;
}
$first_year = 2012;
$today_all = $today_year.'-'.$today_moth.'-'.$today_day;
$Year_interval = array();
echo '<br>'.$today.'</br>';

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
$label_month = monthdays($today_m, $today_year);
echo '<br>'.'$today--'.$today.'</br>';
echo '<br>'.'$today--'.$today_year.'</br>';
echo '<br>'.'$today--'.$today_day.'</br>';
echo '<br>'.'$today--'.$today_moth.'</br>';
echo '<br>'.'$today--'.$today_all.'</br>';
echo '<br>'.'$today--'.$label_month.'</br>';

print_r($Year_interval);
        
        
        
        
        
        
        
        