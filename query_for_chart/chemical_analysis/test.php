<?php
//session_start();
$MonthsNames_for_day = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');  

$year_begin = date("Y", strtotime($_COOKIE["begin_interval_day"]));
$year_end = date("t", strtotime($_COOKIE["end_interval_day"]));
$month_begin = date("n", strtotime($_COOKIE["begin_interval_day"]));
$month_end = date("n", strtotime($_COOKIE["end_interval_day"])); 
$day_begin = date("j", strtotime($_COOKIE["begin_interval_day"]));
$day_end = date("j", strtotime($_COOKIE["end_interval_day"]));

//echo '<br>'.'$year_begin--'.$year_begin.'</br>';

//echo '<br>'.'$month_begin--'.$month_begin.'</br>';
//echo '<br>'.'$month_end--'.$month_end.'</br>';
//echo '<br>'.'$day_begin--'.$day_begin.'</br>';
//echo '<br>'.'$day_end--'.$day_end.'</br>';
//echo '<br>'.'$day_in_month--'.$day_in_month.'</br>';
//echo '<br>'.'$today_day--'.$today_day.'</br>';
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function number_days_in_month() {
   for ($y = 2008; $y <= 2015; $y++) {
      for($m = 1; $m <= 12; $m++) {
         if ($m < 10) {
         $m = '0'.$m;
      }
      $kol_day[$i] = monthdays($m, $y);
      echo '<br>'.'kol day--'.$y.'-'.$m.'-'.$kol_day.'</br>';
      $i++;
      }
   }
   return $kol_day;
}
$day_end = date("j", strtotime($_COOKIE["begin_interval_day"]));

echo '<br>'.'begin_interval_day--'.$day_end.'</br>';
        
        
        
        
        
        
        
        