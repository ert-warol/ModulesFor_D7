<link rel="stylesheet" type="text/css" href="chart_gas_style.css">
<?php
 
$today_all_date = date('Y-m-d');
$today_day = date('j');
$today_year = date('Y');
$today_month = date('m');
$begin_year = date("Y", strtotime($_COOKIE['begin_interval_day']));
$end_year = date("Y", strtotime($_COOKIE['end_interval_day']));
$begin_month = date("m", strtotime($_COOKIE['begin_interval_day']));
$end_month = date("m", strtotime($_COOKIE['end_interval_day']));
$begin_day = date("j", strtotime($_COOKIE['begin_interval_day']));
$end_day = date("j", strtotime($_COOKIE['end_interval_day']));
$begin = date("Y-m-d", strtotime($_COOKIE['begin_interval_day']));
$end = date("Y-m-d", strtotime($_COOKIE['end_interval_day']));
$data_begin_range = strtotime($_COOKIE["begin_interval_day"]);
$data_end_range = strtotime($_COOKIE["end_interval_day"]);
$data_sum = $data_end_range - $data_begin_range;
$range_days = ($data_sum/86400 + 1);

if ($today_month <=10) {
  $today_month = '0'.$today_month;
}
$today_all = $today_year.'-'.$today_month.'-'.'01';

function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_table() { 
global $today_all_date, $today_all, $Query, $QueryResult, $ResultArray, $today_day, $begin_day, $end_day, $begin, $end, $range_days, $begin_month, $end_month;
$data_begin = strtotime($_COOKIE["begin_interval_day"]);
$data_end = strtotime($_COOKIE["end_interval_day"]);


if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/12" AND $begin_month == $end_month): {
   $today_all = $begin;
   $today_all_date = $end;
   $today_day = $end_day - $begin_day;   
}
elseif(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/12" AND $begin_month < $end_month): {
   $today_all = $begin;
   $today_all_date = $end;
   $today_day = $range_days;   
} 
endif;          
          
          $Query[1] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(L4)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 1
                    GROUP BY Dat, Line  
                    ORDER BY Dat";              
                        
          //$ResultArray[1] = array_fill(1, $today_day, 0);  
          
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
          for ($i = 1; $i <= 3; $i++) {
	         $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);
	         while($Row = sqlsrv_fetch_array($QueryResult[$i]))
                $ResultArray[$i][$Row['label']] = $Row['Total'];
          } 
return $ResultArray; 
}
?>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%" id="total_table">
	<tbody>
	    <tr>
	       <?php 
	          $value_top = 'Subtotal - '.$today_all.' /'.$today_all_date;
	          echo '<td colspan="3" id="total_table_main_td">'.$value_top.'</td>'
	       ?>
	    </tr>
	       <?php
              $table_label = array(NULL, 'G1 Red line', '(G1-1)+∆ Blue line', 'G2 Green line', '∆Gbio', 'Savings GAS G1', 'Savings GAS G1-1');
              $text_color = array(NULL, 'red', 'blue', 'green');
              //$background_color = array(NULL, '#C16F6F', '#778DCE', '#246624');
              for($i = 1; $i <= 3; $i++) {
                 echo '<td style=" color: '.$text_color[$i].'; border: 1px dotted black; font-weight: 600; width: 33%; background: rgba(70, 136, 71, 0.34);">'.$table_label[$i].'</td>'; 
              }	          
	       ?>
		<tr>
			<?php 
			 
			  $value_all = construction_table();
			  for($j = 1; $j <= 5; $j++) {
			     $value[$j] = array_sum($value_all[$j]);
			     $value[$j] = round($value[$j], 1);			     
			     $value[$j] = $value[$j].' m3';
			  }
			  $value[4] = $value[2] - $value[3];
			   $value[4] = $value[4].' m3';
			   $value_procent = $value[1]/100;
			   $value[5] = $value[3]/$value_procent;
			   $value[5] = round($value[5], 1);
			   $value[5] = 100 - $value[5];
			   $value[5] = $value[5].' %';
			   $value[6] = $value[2]/100;
			   $value[6] = $value[3]/$value[6];
			   $value[6] = round($value[6], 1);
			   $value[6] = 100 - $value[6];
			   $value[6] = $value[6].' %';
			  //$value[6] = $value[2] - $value[6];  
			  $text_color = array(NULL, 'red', 'blue', 'green', 'black', 'black', 'black');
			  for( $i = 1; $i <= 3; $i++) {
			     if($i <= 4) {
			        $value[$i] = number_format($value[$i], 2, ',', ' ');
			     }			        			     			     
			     echo '<td id="line_value" style=" color: '.$text_color[$i].'; border: 1px dotted black;">'.$value[$i].'</td>'; 
			  }		   
			?>			
		</tr>
		<tr>
			<?php
			   for($i = 4; $i <= 6; $i++) {
                  echo '<td style=" color: '.$text_color[$i].'; border: 1px dotted black; font-weight: 600; height: 20px; background: #246624;">'.'</td>'; 
              }
			?>			
		</tr>
		<tr>
			<?php
			
			   $background_color = array(NULL, NULL, NULL, NULL, '#717171', '#C16F6F', '#778DCE');
			   for($i = 4; $i <= 6; $i++) {
                  echo '<td style=" color: white; border: 1px dotted black; font-weight: 600; background: '.$background_color[$i].'">'.$table_label[$i].'</td>'; 
              }
			?>			
		</tr>
		<tr>
			<?php
			   
			   for( $i = 4; $i <= 6; $i++) {
			     if($i == 4) {
			        $value[$i] = number_format($value[$i], 2, ',', ' ');
			     }			        			     			     
			      echo '<td id="line_value" style=" color: white; border: 1px dotted black; background: '.$background_color[$i].'">'.$value[$i].'</td>'; 
			  }	
			?>			
		</tr>
    </tbody>
</table>
<?php 
function all_table_default() {
  global $MonthsNames, $today_year, $today_month, $today_day;
  $MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
  $MonthsNames_double = array(NULL, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  $label_first_str = array(NULL, 'Day', 'G1 Red line', '(G1-1)+∆ Blue line', 'G2 Green line', '∆Gbio', 'Savings Gas G1', 'Savings Gas G1-1');
     
         $table_all .= '<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;" id="total_dynamic_table">';
            $table_all .= '<tbody>';
               /*$table_all .= '<tr>';
               $table_all .= '<td colspan="7" style="height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$MonthsNames_double[$today_month].'-'.$today_year.'</td>';
               $table_all .= '</tr>';*/
               $table_all .= '<tr>';
                  for($i = 1; $i <= 7; $i++) {          
                     $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$label_first_str[$i].'</td>';
                  }   
               $table_all .= '</tr>'; 
                  $test = construction_table();                  			    
			      $j = 1;			  
			      foreach($test[1] as $value) {                   
                     $value = round($value, 0);
                     $value = number_format($value, 2, ',', ' ');                                    
                     $value_red_line[$j] = $value;
                     $j++;			     	 			 
			      }
			      $j = 1;  
                  foreach($test[2] as $value) {                   
                     $value = round($value, 0);
                     $value = number_format($value, 2, ',', ' ');                                     
                     $value_blue_line[$j] = $value;
                     $j++;			     	 			 
			      }  
			      $j = 1;      
                  foreach($test[3] as $value) {                   
                     $value = round($value, 0);
                     $value = number_format($value, 2, ',', ' ');                                     
                     $value_green_line[$j] = $value;
                     $j++;			     	 			 
			      }			       			               
                  for($i = 1; $i <= $today_day; $i++) {
                     $value_black[$i] = $value_blue_line[$i] - $value_green_line[$i];
                     $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                     $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                     $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';
                     $value_procent_g1[$i] = $value_red_line[$i] / 100;
                     $value_procent_g1[$i] = $value_green_line[$i] / $value_procent_g1[$i];
                     $value_procent_g1[$i] = 100 - round($value_procent_g1[$i], 1).' %';
                     $table_all .= '<tr>';
                        $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$i.' st'.'</td>';
                        $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: red; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_red_line[$i].'</td>';
                        $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: blue; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_blue_line[$i].'</td>';
                        $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: green; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_green_line[$i].'</td>';
                        $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: black; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_black[$i].'</td>';
                        $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #C16F6F; font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_procent_g1[$i].'</td>';
                        $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';
                        
                     $table_all .= '</tr>';
                  }              
                  
            $table_all .= '<tbody>';
         $table_all .= '</table>';
              
return $table_all; 
}

function all_table_interval() {
  global $MonthsNames, $today_year, $today_month, $today_day, $begin_day, $end_day, $range_days, $begin_month, $end_month, $begin_year, $end_year;
  $MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
  $MonthsNames_double = array(NULL, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  $label_first_str = array(NULL, 'Day', 'G1 Red line', '(G1-1)+∆ Blue line', 'G2 Green line', '∆Gbio', 'Savings Gas G1', 'Savings Gas G1-1');
  $begin = date("Y-m-d", strtotime($_COOKIE['begin_interval_day']));
  $end = date("Y-m-d", strtotime($_COOKIE['end_interval_day']));
  $data_begin = strtotime($_COOKIE["begin_interval_day"]);
  $data_end = strtotime($_COOKIE["end_interval_day"]); 
     
         $table_all .= '<table border="0" cellpadding="0" cellspacing="0" style="width: 100%;" id="total_dynamic_table">';
            $table_all .= '<tbody>';
               /*$table_all .= '<tr>';
               $table_all .= '<td colspan="7" style="height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$MonthsNames_double[$today_month].'-'.$today_year.'</td>';
               $table_all .= '</tr>';*/
               $table_all .= '<tr>';
                  for($i = 1; $i <= 7; $i++) {          
                     $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$label_first_str[$i].'</td>';
                  }   
               $table_all .= '</tr>'; 
                  $test = construction_table();
                  //print_r($test);                  
                  //for($i = 1; $i <= 3; $i++)			    
			      $j = $begin_day;			  
			      foreach($test[1] as $value) {                   
                     $value = round($value, 0);
                     $value = number_format($value, 2, ',', ' ');                                    
                     $value_red_line[$j] = $value;
                     $j++;			     	 			 
			      }
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
                  if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/12" AND $begin_month == $end_month AND  $begin_year == $end_year): {
			         $begin_interval = $begin_day;
			         $end_interval = $end_day;
                        for($day = $begin_interval; $day <= $end_interval; $day++) {
                           $i = $day;                        
                           $value_black[$i] = $value_blue_line[$i] - $value_green_line[$i];
                           $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                           $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                           $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';
                           $value_procent_g1[$i] = $value_red_line[$i] / 100;
                           $value_procent_g1[$i] = $value_green_line[$i] / $value_procent_g1[$i];
                           $value_procent_g1[$i] = 100 - round($value_procent_g1[$i], 1).' %';
                           $table_all .= '<tr>';                     
                              $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$day.'/'.$MonthsNames[$begin_month].'</td>';                   
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: red; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_red_line[$i].'</td>';
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: blue; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_blue_line[$i].'</td>';
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: green; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_green_line[$i].'</td>';
                              $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: black; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_black[$i].'</td>';
                              $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #C16F6F; font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_procent_g1[$i].'</td>';
                              $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';
                              $table_all .= '</tr>';                              
			            }
			      }
			      elseif(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/12" AND $begin_month < $end_month AND $begin_year == $end_year): {			         			         
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
                          $value_procent_g1[$i] = $value_red_line[$i] / 100;
                          $value_procent_g1[$i] = $value_green_line[$i] / $value_procent_g1[$i];
                          $value_procent_g1[$i] = 100 - round($value_procent_g1[$i], 1).' %';
                          $table_all .= '<tr>';                     
                             $table_all .= '<td style="height: 50px; width: 30px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$day.'/'.$MonthsNames[$month].'</td>';                   
                             $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: red; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_red_line[$i].'</td>';
                             $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: blue; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_blue_line[$i].'</td>';
                             $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: green; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_green_line[$i].'</td>';
                             $table_all .= '<td style="height: 50px; width: 82px; padding-right: 5px; font-size: 15px; text-align: right; background: white; color: black; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value_black[$i].'</td>';
                             $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #C16F6F; font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_procent_g1[$i].'</td>';
                             $table_all .= '<td style=" width: 82px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 15px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';
                          $table_all .= '</tr>'; 
                          $i++;                             
			           }
			         } 
			      }			      
			      endif;			      
            $table_all .= '<tbody>';
         $table_all .= '</table>';
              
return $table_all; 
}
if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/12") {   
   echo all_table_interval();
}
else {
   echo all_table_default();
   
   /*$begin = date("Y-m-d", strtotime($_COOKIE['begin_interval_day']));
   $end = date("Y-m-d", strtotime($_COOKIE['end_interval_day']));
   echo '<br>'.$begin.'</br>';
   echo '<br>'.$end.'</br>';*/
}
/*$a = monthdays($month, $begin_year);
echo '<br>'.$begin.'</br>';
echo '<br>'.$end.'</br>';
echo '<br>'.$today_day.'</br>';
echo '<br>'.'Begin DAY- '.$begin_day.'</br>';
echo '<br>'.'End month- '.$end_month.'</br>';
echo '<br>'.'Range DAY- '.$range_days.'</br>';*/




