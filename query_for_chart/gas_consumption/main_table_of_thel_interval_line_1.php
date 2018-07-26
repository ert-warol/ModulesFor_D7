<link rel="stylesheet" type="text/css" href="chart_gas_style.css">
<?php
 
$today_all_date = date('Y-m-d');
$today_day = date('j');
$today_year = date('Y');
$today_month = date('m');
$begin_year = date("Y", strtotime($_COOKIE['begin_interval_day']));
$end_year = date("Y", strtotime($_COOKIE['end_interval_day']));
$begin_month = date("n", strtotime($_COOKIE['begin_interval_day']));
$end_month = date("n", strtotime($_COOKIE['end_interval_day']));
$begin_day = date("j", strtotime($_COOKIE['begin_interval_day']));
$end_day = date("j", strtotime($_COOKIE['end_interval_day']));
$begin = date("Y-m-d", strtotime($_COOKIE['begin_interval_day']));
$end = date("Y-m-d", strtotime($_COOKIE['end_interval_day']));
$data_begin_range = strtotime($_COOKIE["begin_interval_day"]);
$data_end_range = strtotime($_COOKIE["end_interval_day"]);
$data_sum = $data_end_range - $data_begin_range;
$range_days = ($data_sum/86400 + 1);

$today_all = $today_year.'-'.$today_month.'-'.'01';

function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_table() { 
global $today_all_date, $today_all, $Query, $QueryResult, $ResultArray, $begin, $end, $today_year, $today_month;

if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21") {
   $today_all = $begin;
   $today_all_date = $end;      
}
       
          $Query[2] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(L8)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 1
                    GROUP BY Dat, Line  
                    ORDER BY Dat";              
                        
          //$ResultArray[2] = array_fill(1, $today_day, 0);
           
          $Query[3] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(GazObg*1000)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 1
                    GROUP BY Dat, Line  
                    ORDER BY Dat";             
                        
          //$ResultArray[3] = array_fill(1, $today_day, 0);
           
          require 'sql_srv_conect.php';          
          //unset($ResultArray);          
          for ($i = 2; $i <= 3; $i++) {
	         $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);
	         while($Row = sqlsrv_fetch_array($QueryResult[$i]))
                $ResultArray[$i][$Row['label']] = $Row['Total'];
          } 
return $ResultArray; 
}

function all_table_default() {
  global $today_year, $today_day;
  $MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
  $MonthsNames_double = array(NULL, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  $label_first_str = array(NULL, 'Day', '(G1-1)+∆ Blue line', 'G2 Green line', '∆Gbio', 'Savings Gas G1-1');
  $today_month = date('n');  
         $table_all = '<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;" id="total_dynamic_table">';
            $table_all .= '<tbody>';               
               $table_all .= '<tr>';
                  for($i = 1; $i <= 5; $i++) {          
                     $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$label_first_str[$i].'</td>';
                  }   
               $table_all .= '</tr>'; 
                  $test = construction_table();                			    
			      $j = 1;  
                  foreach($test[2] as $value) {                   
                     $value = round($value, 1);
                     $value_blue_line_all[$j] = $value;
                     $value = number_format($value, 0, ',', ' ');                                     
                     $value_blue_line[$j] = $value;
                     $j++;			     	 			 
			      }  
			      $j = 1;      
                  foreach($test[3] as $value) {                   
                     $value = round($value, 1);
                     $value_green_line_all[$j] = $value;
                     $value = number_format($value, 0, ',', ' ');                                     
                     $value_green_line[$j] = $value;
                     $j++;			     	 			 
			      }			       			               
                  for($i = 1; $i <= $today_day; $i++) {
                     $value_black[$i] = $value_blue_line_all[$i] - $value_green_line_all[$i];
                     $value_black[$i] = number_format($value_black[$i], 0, ',', ' '); 
                     $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                     $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                     $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';                     
                     $table_all .= '<tr>';
                        $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$i.'-'.$MonthsNames[$today_month].'</td>';                        
                        $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: blue; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_blue_line[$i].'</td>';
                        $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: green; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_green_line[$i].'</td>';
                        $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: black; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_black[$i].'</td>';                        
                        $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';                        
                     $table_all .= '</tr>';
                  }              
                  
            $table_all .= '<tbody>';
         $table_all .= '</table>';
              
return $table_all; 
}

function all_table_interval() {
  global $today_year, $today_month, $today_day, $begin_day, $end_day, $range_days, $begin_month, $end_month, $begin_year, $end_year;
  $MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
  $MonthsNames_double = array(NULL, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  $label_first_str = array(NULL, 'Day', '(G1-1)+∆ Blue line', 'G2 Green line', '∆Gbio', 'Savings Gas G1-1');
  $begin = date("Y-m-d", strtotime($_COOKIE['begin_interval_day']));
  $end = date("Y-m-d", strtotime($_COOKIE['end_interval_day']));
  $data_begin = strtotime($_COOKIE["begin_interval_day"]);
  $data_end = strtotime($_COOKIE["end_interval_day"]); 
     
         $table_all .= '<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;" id="total_dynamic_table">';
            $table_all .= '<tbody>';               
               $table_all .= '<tr>';
                  for($i = 1; $i <= 5; $i++) {          
                     $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$label_first_str[$i].'</td>';
                  }   
               $table_all .= '</tr>'; 
                  $test = construction_table();                  			    
			      $j = $begin_day;  
                  foreach($test[2] as $value) {                   
                     $value = round($value, 0);
                     $value = number_format($value, 2, ',', ' ');                                     
                     $value_blue_line[$j] = $value;
                     $j++;			     	 			 
			      }  
			      $j = $begin_day;      
                  foreach($test[3] as $value) {                   
                     $value = round($value, 0);
                     $value = number_format($value, 2, ',', ' ');                                     
                     $value_green_line[$j] = $value;
                     $j++;			     	 			 
			      } 
                  if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21" AND $begin_month == $end_month AND  $begin_year == $end_year): {
			         $begin_interval = $begin_day;
			         $end_interval = $end_day;
                        for($day = $begin_interval; $day <= $end_interval; $day++) {
                           $i = $day;                        
                           $value_black[$i] = $value_blue_line[$i] - $value_green_line[$i];
                           $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                           $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                           $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';                           
                           $table_all .= '<tr>';                     
                              $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$day.'/'.$MonthsNames[$begin_month].'</td>';                
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: blue; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_blue_line[$i].'</td>';
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: green; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_green_line[$i].'</td>';
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: black; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_black[$i].'</td>';                              
                              $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';                              
                              $table_all .= '</tr>';                              
			            }
			      }
			      elseif(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21" AND $begin_month < $end_month AND $begin_year == $end_year): {			         			         
			         $i = $begin_day;			         			         
			         for($month = $begin_month; $month <= $end_month; $month++) {			               
			              
			           if($month == $begin_month) {			                  
			              $end_interval = date("t", strtotime($begin_year. "-" . $month . "-01"));
			              $begin_interval = $begin_day; 
			           }
			           if($month < $end_month AND $month > $begin_month) {
			              $end_interval = date("t", strtotime($begin_year. "-" . $month . "-01"));
			              $begin_interval = 1;
			           }
			           if($month == $end_month) {
			              $end_interval = $end_day;
			              $begin_interval = 1;
			           }
			           for($day = $begin_interval; $day <= $end_interval; $day++) {                                                      
                          $value_black[$i] = $value_blue_line[$i] - $value_green_line[$i];
                          $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                          $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                          $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';                          
                          $table_all .= '<tr>';                     
                             $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$day.'/'.$MonthsNames[$month].'</td>';                 
                             $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: blue; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_blue_line[$i].'</td>';
                             $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: green; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_green_line[$i].'</td>';
                             $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: black; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_black[$i].'</td>';                             
                             $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';
                          $table_all .= '</tr>'; 
                          $i++;                             
			           }
			         } 
			      }
			      elseif(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21" AND $begin_year < $end_year): {
			      $i = $begin_day;
			      for($year = $begin_year; $year <= $end_year; $year++) {
			         if($year == $begin_year){
			            for($month = $begin_month; $month <= 12; $month++) {
			               if($month == $begin_month AND $year == $begin_year) {			                  
			                  $end_interval = date("t", strtotime($begin_year. "-" . $month . "-01"));
			                  $begin_interval = $begin_day;			                 
			               }
			               if($month > $begin_month AND $year == $begin_year) {
			                  $end_interval = date("t", strtotime($begin_year. "-" . $month . "-01"));
			                  $begin_interval = 1;			                  
			               }
			               for($day = $begin_interval; $day <= $end_interval; $day++) {                                                      
                              $value_black[$i] = $value_blue_line[$i] - $value_green_line[$i];
                              $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                              $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                              $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';                          
                              $table_all .= '<tr>';                     
                                $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$day.'/'.$MonthsNames[$month].'</td>';                 
                                $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: blue; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_blue_line[$i].'</td>';
                                $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: green; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_green_line[$i].'</td>';
                                $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: black; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_black[$i].'</td>';                             
                                $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';
                              $i++;
			               }   
			            }
			         }  
			         else {
			         for($month = 1; $month <= $end_month; $month++) {
			            if($month < $end_month AND $month > $begin_month AND $year > $begin_year) {
			               $end_interval = date("t", strtotime($begin_year. "-" . $month . "-01"));
			               $begin_interval = 1;			               
			            }  
			            if($month == $end_month AND $year > $begin_year) {
			               $end_interval = $end_day;
			               $begin_interval = 1;
			            }
			            for($day = $begin_interval; $day <= $end_interval; $day++) {			                                                                   
                           $value_black[$i] = $value_blue_line[$i] - $value_green_line[$i];
                           $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                           $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                           $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';                          
                           $table_all .= '<tr>';                     
                              $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$day.'/'.$MonthsNames[$month].'</td>';                 
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: blue; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_blue_line[$i].'</td>';
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: green; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_green_line[$i].'</td>';
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: black; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_black[$i].'</td>';                             
                              $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';
                           $i++;
			            }
			         }
			      }	
			   }
			   }
			   endif;			      
            $table_all .= '<tbody>';
         $table_all .= '</table>';
              
return $table_all; 
}
if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21") {   
   echo all_table_interval();
}
else {
   echo all_table_default();   
}
//echo '<br>'.'Begin '.$today_all.'</br>';
//echo '<br>'.'End '.$today_all_date.'</br>';
