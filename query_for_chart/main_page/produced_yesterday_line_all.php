<link rel="stylesheet" type="text/css" href="main_page_style.css">
<?php
$MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$name_label = array(null, 'Lines', 'Line # 1', 'Line # 2', 'Line # 3', 'Line # 4');
$name_label_for_pellet = array(null, 'Type pellet', null, 'Month to day tons');
$name_label_for_quality_pellet = array(null, 'Quality pellet', null, 'Month to day, Fe %');
$pellet_type = array(null, '62%', '65%', '65+');
$pellet_type_day = array(null, 'Z900', 'Z902', 'Z904');
$pellet_type_month = array(null, 'Z901', 'Z903', 'Z905');
$quality_pellet_day = array(null, 'Z906', 'Z908', 'Z910');
$quality_pellet_month = array(null, 'Z907', 'Z909', 'Z911');
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
$last_date_GMD_FROM = $last_date;

if($last_date < 10) {
   $last_date_GMD_FROM = '0'.$last_date;
   $last_date = '0'.$last_date;
}

$name_label_for_pellet[2] = '24 h (last day  '.$last_date.'/'.$MonthsNames[$month_previous_day_namber].') tons';
$name_label_for_quality_pellet[2] = '24 h (last day  '.$last_date.'/'.$MonthsNames[$month_previous_day_namber].') Fe %';
$second_label = 'Total thousands tons/day';
$query_last_day = $year_previous_day.'-'.$month_previous_day.'-'.$last_date;
$last_day_GMD_FROM = $year_previous_day.$month_previous_day.$last_date_GMD_FROM;
//$last_day_GMD_FROM = '20160131';
//$query_last_day = '2016-01-31';

function construct_table() {
   global $last_date, $year_previous_day,  $MonthsNames, $month_previous_day_namber, $today_day, $today_month, $today_month_date, $today_year, $name_label, $query_last_day, $name_label_for_pellet,  $second_label, $ResultArray;
     
   $header_table = 'Pellet production last day: '.$last_date.'-'.$MonthsNames[$month_previous_day_namber].'/'.$year_previous_day;  
   
   
   $Query[1] = "SELECT DAY(Dat) AS label, SUM(PrLine1) AS Total
                FROM cpo.dbo.RsProizvCPO
                WHERE Dat = '$query_last_day'
                GROUP BY Dat";              
                        
   //$ResultArray[1] = array_fill(1, 1, 0);  
          
   $Query[2] = "SELECT DAY(Dat) AS label, SUM(PrLine2) AS Total
                FROM cpo.dbo.RsProizvCPO
                WHERE Dat = '$query_last_day'
                GROUP BY Dat";              
                        
   //$ResultArray[2] = array_fill(1, 1, 0);
           
   $Query[3] = "SELECT DAY(Dat) AS label, SUM(PrLine3) AS Total
                FROM cpo.dbo.RsProizvCPO
                WHERE Dat = '$query_last_day'
                GROUP BY Dat";
   //$ResultArray[3] = array_fill(1, 1, 0);
   
   $Query[4] = "SELECT DAY(Dat) AS label, SUM(PrLine4) AS Total
                FROM cpo.dbo.RsProizvCPO
                WHERE Dat = '$query_last_day'
                GROUP BY Dat";             
                        
   //$ResultArray[4] = array_fill(1, 1, 0);  
   
   require 'sql_srv_conect.php';          
   
   for ($i = 1; $i <= 4; $i++) {
      $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);
	  while($Row = sqlsrv_fetch_array($QueryResult[$i]))
         $ResultArray[$i][$Row['label']] = $Row['Total']; 
   }   
   
   $output_table = '<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;" id="produced_last_day_table">';
      $output_table .= '<tbody>';
         $output_table .= '<tr>';            
            $output_table .= '<td colspan="5" style="height: 50px; width: 30px; padding-right: 5px; font-size: 19px; font-weight: 600; text-align: center; background: rgba(70, 136, 71, 0.34); color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$header_table.'</td>';
         $output_table .= '</tr>';
         $output_table .= '<tr>';            
            for ($i = 1; $i <= 5; $i++) {
               $output_table .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$name_label[$i].'</td>';
            }
         $output_table .= '</tr>';
         $output_table .= '<tr>';
            $output_table .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: rgba(70, 136, 71, 0.34); color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$second_label.'</td>';
            for($i = 1; $i <= 4; $i++) {
               foreach ($ResultArray[$i] as $value) {
                  $value = round($value, 2);
                  $value = number_format($value, 2, ',', ' ');                 
                  $output_table .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: rgba(70, 136, 71, 0.34); color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$value.'</td>';
               }
            }
         $output_table .= '</tr>';
      $output_table .= '</tbody>';
   $output_table .= '</table>';
   return $output_table;
}  
function construct_table_pellet() {
   global $last_date, $last_day_GMD_FROM, $month_previous_day_namber, $MonthsNames, $today_day, $today_month, $today_month_date, $today_year, $name_label, $query_last_day, $name_label_for_pellet,  $second_label, $pellet_type_day, $pellet_type_month, $pellet_type;
   $ResultArray = array();
   $last_date_pellet = $MonthsNames[$today_month_date].'/'.$today_year;
   $header_table_2 = 'Produced by the type of pellets: '.$last_date_pellet;
   
   
   for($i = 1; $i <= 3; $i++) {
      $Query_day[$i] = "SELECT GMD_FROM AS label, SUMMA AS Total
                       FROM web_interface.dbo.volume_type_pellet
                       WHERE KPKZ = '$pellet_type_day[$i]' AND GMD_FROM = '$last_day_GMD_FROM'
                       GROUP BY GMD_FROM, KPKZ, SUMMA"; 
      $Query_month[$i] = "SELECT DAY(GMD_FROM) AS label, SUMMA AS Total
                       FROM web_interface.dbo.volume_type_pellet
                       WHERE KPKZ = '$pellet_type_month[$i]' AND GMD_FROM = '$last_day_GMD_FROM'
                       GROUP BY DAY(GMD_FROM), KPKZ, SUMMA";
   }
   require 'sql_srv_conect.php';
   for ($i = 1; $i <= 3; $i++) {
      $QueryResult_day[$i] = sqlsrv_query($conn, $Query_day[$i]);
      
	  while($Row = sqlsrv_fetch_array($QueryResult_day[$i]))
         $ResultArray_day[$i][$Row['label']] = $Row['Total'];

     $QueryResult_month[$i] = sqlsrv_query($conn, $Query_month[$i]);
      
	  while($Row = sqlsrv_fetch_array($QueryResult_month[$i]))
         $ResultArray_month[$i][$Row['label']] = $Row['Total'];    
   }
   //print_r($ResultArray);
   $output_table = '<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;" id="produced_last_day_pellet_type_table">';
      $output_table .= '<tbody>';
         $output_table .= '<tr>';            
            $output_table .= '<td colspan="3" style="height: 20px; padding-right: 5px; font-size: 19px; font-weight: 600; text-align: center; background: white; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;"></td>';
         $output_table .= '</tr>';
         $output_table .= '<tr>';            
            $output_table .= '<td colspan="3" style="height: 50px; padding-right: 5px; font-size: 19px; font-weight: 600; text-align: center; background: rgba(70, 136, 71, 0.34); color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$header_table_2.'</td>';
         $output_table .= '</tr>';
         $output_table .= '<tr>';           
            for ($i = 1; $i <= 3; $i++) {
               
               $output_table .= '<td colspan="1" style="height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$name_label_for_pellet[$i].'</td>';
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
               $output_table .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$pellet_type[$i].'</td>';
            
               foreach ($ResultArray_day[$i] as $value) {
                  $value = round($value, 0);
                  $value = number_format($value, 2, ',', ' ');                  
                  $output_table .= '<td style="height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$value.'</td>';
               }
               foreach ($ResultArray_month[$i] as $value) {
                  $value = round($value, 0);
                  $value = number_format($value, 2, ',', ' ');                  
                  $output_table .= '<td style="height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$value.'</td>';
               }
            
         }
         $output_table .= '</tr>';
      $output_table .= '</tbody>';
   $output_table .= '</table>';
   return $output_table;  
}
function construct_table_quality_pellet() {
   global $name_label_for_quality_pellet, $month_previous_day_namber, $quality_pellet_day, $quality_pellet_month, $last_day_GMD_FROM, $last_date, $MonthsNames, $today_day, $today_month_date, $today_month, $today_year, $name_label, $query_last_day, $name_label_for_pellet,  $second_label, $pellet_type_day, $pellet_type_month, $pellet_type;
   $last_date = $last_date.'-'.$MonthsNames[$month_previous_day_namber].'/'.$today_year;
   $last_date_pellet_quality = $MonthsNames[$today_month_date].'/'.$today_year;
   $header_table_2 = 'The iron content in the pellets: '.$last_date_pellet_quality;
   
   for($i = 1; $i <= 3; $i++) {
      $Query_day[$i] = "SELECT GMD_FROM AS label, SUMMA AS Total
                       FROM web_interface.dbo.volume_type_pellet
                       WHERE KPKZ = '$quality_pellet_day[$i]' AND GMD_FROM = '$last_day_GMD_FROM'
                       GROUP BY GMD_FROM, KPKZ, SUMMA"; 
      $Query_month[$i] = "SELECT DAY(GMD_FROM) AS label, SUMMA AS Total
                       FROM web_interface.dbo.volume_type_pellet
                       WHERE KPKZ = '$quality_pellet_month[$i]' AND GMD_FROM = '$last_day_GMD_FROM'
                       GROUP BY DAY(GMD_FROM), KPKZ, SUMMA";
   }
   require 'sql_srv_conect.php';
   for ($i = 1; $i <= 3; $i++) {
      $QueryResult_day[$i] = sqlsrv_query($conn, $Query_day[$i]);
      
	  while($Row = sqlsrv_fetch_array($QueryResult_day[$i]))
         $ResultArray_day[$i][$Row['label']] = $Row['Total'];

     $QueryResult_month[$i] = sqlsrv_query($conn, $Query_month[$i]);
      
	  while($Row = sqlsrv_fetch_array($QueryResult_month[$i]))
         $ResultArray_month[$i][$Row['label']] = $Row['Total'];    
   }
   $output_table = '<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;" id="produced_last_day_pellet_type_table">';
      $output_table .= '<tbody>';
         $output_table .= '<tr>';            
            $output_table .= '<td colspan="3" style="height: 20px; padding-right: 5px; font-size: 19px; font-weight: 600; text-align: center; background: white; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;"></td>';
         $output_table .= '</tr>';
         $output_table .= '<tr>';            
            $output_table .= '<td colspan="3" style="height: 50px; padding-right: 5px; font-size: 19px; font-weight: 600; text-align: center; background: rgba(70, 136, 71, 0.34); color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$header_table_2.'</td>';
         $output_table .= '</tr>';
         $output_table .= '<tr>';           
            for ($i = 1; $i <= 3; $i++) {
               
               $output_table .= '<td colspan="1" style="height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$name_label_for_quality_pellet[$i].'</td>';
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
               $output_table .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$pellet_type[$i].'</td>';
            
               foreach ($ResultArray_day[$i] as $value) {
                  $value = round($value, 2);                                    
                  $output_table .= '<td style="height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$value.'</td>';
               }
               foreach ($ResultArray_month[$i] as $value) {
                  $value = round($value, 2);                                    
                  $output_table .= '<td style="height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: '.$background_color.'; color: #246624; font-family: calibri; margin: 0;  border: 1px dotted black;">'.$value.'</td>';
               }
         }
         $output_table .= '</tr>';
      $output_table .= '</tbody>';
   $output_table .= '</table>';
   return $output_table; 
}
echo construct_table();
echo construct_table_pellet();
echo construct_table_quality_pellet();

//echo $query_last_day;
//echo $last_day_GMD_FROM;









