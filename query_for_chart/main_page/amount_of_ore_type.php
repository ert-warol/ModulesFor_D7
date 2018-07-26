<link rel="stylesheet" type="text/css" href="main_page_style.css">
<?php
$MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$name_label = array(null, null, 'Current month', 'This year');
$second_label = array(null, 'K 2/2', 'K 2/3', 'K 2/5');
$query_taype_ore = array(null, 'K22_sm_tn', 'K23_sm_tn', 'K25_sm_tn');
$today_year = date('Y');
$today_month = date('m');
$today_month_date = date('n');
$month_previous_day_namber = $today_month_date;
$last_month = $today_month_date - 1;

if($last_month < 10){
   $last_month = '0'.$last_month;
}

$today_day = date('j');
$last_date = $today_day - 1;

if($today_month_date == 1){
   $year_previous_day = $today_year - 1;
   $month_previous_day = 12;
   $month_previous_day_namber = $month_previous_day;
}
else{
   $year_previous_day = $today_year;
}

if($today_day == 1){
   $month_previous_day = $last_month;
   $month_previous_day_namber =  $today_month_date - 1;
   $last_date = date("t", strtotime( $year_previous_day. "-" .$year_previous_day. "-01"));         
}
else {
   $month_previous_day = $today_month;
}

if($last_date < 10) {   
   $last_date = '0'.$last_date;
}

$query_last_day = $year_previous_day.'-'.$month_previous_day.'-'.$last_date;
//$query_last_day = '2016-01-31';

function construct_table() {
   global $last_date, $MonthsNames, $today_month, $today_month_date, $today_year, $name_label, $query_last_day, $query_taype_ore, $second_label;      
   $last_date = $MonthsNames[$today_month_date].'/'.$today_year;
   //$last_date = '31-Jan/2016';
   $name_label[1] = 'Last Day ('.$query_last_day.')';
   $header_table = 'Number ore grades : '.$last_date;
   $first_label = 'Ore grade';
   
   for($i = 1; $i <= 3; $i++) {
      $Query_day[$i] = "SELECT DAY(OrderDate) AS label, isnull(sum($query_taype_ore[$i]), 0) as Total
          FROM otk_ru.ru_mtn.OtherParam_OTK
          WHERE OrderDate = '$query_last_day'
          GROUP BY OrderDate";              
                        
   //$ResultArray[1] = array_fill(1, 1, 0);  
          
      $Query_month[$i] = "SELECT MONTH(OrderDate) AS label, isnull(sum($query_taype_ore[$i]), 0) as Total
          FROM otk_ru.ru_mtn.OtherParam_OTK
          WHERE MONTH(OrderDate) = '$today_month' AND YEAR(OrderDate) = '$today_year'
          GROUP BY MONTH(OrderDate)";              
                        
   //$ResultArray[2] = array_fill(1, 1, 0);
           
      $Query_year[$i] = "SELECT YEAR(OrderDate) AS label, isnull(sum($query_taype_ore[$i]), 0) as Total
          FROM otk_ru.ru_mtn.OtherParam_OTK
          WHERE YEAR(OrderDate) = '$today_year'
          GROUP BY YEAR(OrderDate)";
   //$ResultArray[3] = array_fill(1, 1, 0); 
   
   }        
   require 'sql_srv_conect.php';          
   //unset($ResultArray); 
            
   for ($i = 1; $i <= 3; $i++) {
      $QueryResult_day[$i] = sqlsrv_query($conn, $Query_day[$i]);
	  while($Row = sqlsrv_fetch_array($QueryResult_day[$i]))
         $ResultArray_day[$i][$Row['label']] = $Row['Total']; 
   
      $QueryResult_month[$i] = sqlsrv_query($conn, $Query_month[$i]);
	  while($Row = sqlsrv_fetch_array($QueryResult_month[$i]))
         $ResultArray_month[$i][$Row['label']] = $Row['Total']; 
   
      $QueryResult_year[$i] = sqlsrv_query($conn, $Query_year[$i]);
	  while($Row = sqlsrv_fetch_array($QueryResult_year[$i]))
         $ResultArray_year[$i][$Row['label']] = $Row['Total']; 
   }
   //print_r($ResultArray_day);
   $output_table = '<table border="0" cellpadding="0" cellspacing="0" style="width: 99%; margin: 10px 0px 0px 10px;" id="produced_last_day_table">';
      $output_table .= '<tbody>';
         $output_table .= '<tr>';            
            $output_table .= '<td colspan="5" style="height: 30px; width: 30px; padding-right: 5px; font-size: 19px; font-weight: 600; text-align: center; background: rgba(70, 136, 71, 0.34); color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$header_table.'</td>';
         $output_table .= '</tr>';
         $output_table .= '<tr>';            
            $output_table .= '<td style="height: 30px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$first_label.'</td>';
            for ($i = 1; $i <= 3; $i++) {
               $output_table .= '<td style="height: 30px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$name_label[$i].'</td>';
            }
         $output_table .= '</tr>';
         for($i = 1; $i <= 3; $i++) {
            if($i == 2){
               $background_color = '#FFFFFF';
            }
            else {
               $background_color = 'rgba(70, 136, 71, 0.34)';
            }
            $output_table .= '<tr>';
               $output_table .= '<td style="height: 30px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$second_label[$i].'</td>';
               
                  foreach ($ResultArray_day[$i] as $value) {
                     //$value = $value / 1000;
                     $value = round($value, 2);
                     $value = number_format($value, 2, ',', ' ');                 
                     $output_table .= '<td style="height: 30px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$value.'</td>';
                  }
                  foreach ($ResultArray_month[$i] as $value) {
                     //$value = $value / 1000;
                     $value = round($value, 2);
                     $value = number_format($value, 2, ',', ' ');                 
                     $output_table .= '<td style="height: 30px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$value.'</td>';
                  }
                  foreach ($ResultArray_year[$i] as $value) {
                     //$value = $value / 1000;
                     $value = round($value, 2);
                     $value = number_format($value, 2, ',', ' ');                 
                     $output_table .= '<td style="height: 30px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$value.'</td>';
                  }
               
            $output_table .= '</tr>';
         }
      $output_table .= '</tbody>';
   $output_table .= '</table>';
   return $output_table;
}
echo construct_table();
//echo $query_last_day;