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
$today_all = $today_year.'-'.$today_month.'-'.'01';
if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21") {
   $value_top = 'The total amount of the interval '.$begin.' / '.$end; 
}
else {
   $value_top = 'The total amount of the interval '.$today_all.' / '.$today_all_date;
}
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_table() { 
global $today_all_date, $today_all, $Query, $QueryResult, $ResultArray, $begin, $end;
$data_begin = strtotime($_COOKIE["begin_interval_day"]);
$data_end = strtotime($_COOKIE["end_interval_day"]);

if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21") {
   $today_all = $begin;
   $today_all_date = $end;     
}
        
          $Query[2] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(L8)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 2
                    GROUP BY Dat, Line  
                    ORDER BY Dat";              
                        
          //$ResultArray[2] = array_fill(1, $today_day, 0);
           
          $Query[3] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(GazObg*1000)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 2
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

function table_subtotal() {
  global $today_all, $today_all_date, $today_year, $today_month, $value_top;
  $table_label = array(NULL, NULL, '(G1-1)+∆ Blue line', 'G2 Green line', '∆Gbio', 'Savings GAS G1-1');
  $text_color = array(NULL, NULL, 'blue', 'green');
  $background_color = array(NULL, NULL, NULL, NULL, '#717171', '#778DCE');
  if(isset($_COOKIE["interval_day_chart"]) AND $_COOKIE["interval_day_chart"] == 1 AND $_COOKIE["node_name_interval_day"] === "node/21") {
     $text_color_for_head = '#FF0000'; 
  }
  else {
     $text_color_for_head = '#FFFFFF';
  }
  
  $table_all_sum = '<table border="0" cellpadding="0" cellspacing="0" style="width:100%" id="total_table">';
   $table_all_sum .='<tbody>';
      $table_all_sum .='<tr>';         
	     $table_all_sum .= '<td colspan="2" id="total_table_gas_consumption_2_main_td" style=" color: '.$text_color_for_head.';">'.$value_top.'</td>';
	  $table_all_sum .='</tr>';
	  $table_all_sum .='<tr>';
       for($i = 2; $i <= 3; $i++) {
          $table_all_sum .= '<td style=" color: '.$text_color[$i].'; border: 1px dotted black; font-weight: 600; width: 33%; background: rgba(70, 136, 71, 0.34);">'.$table_label[$i].'</td>';
       }
       $table_all_sum .='</tr>';
       
       $value_all = construction_table();
       for($j = 2; $j <= 3; $j++) {
		  $value[$j] = array_sum($value_all[$j]);
		  $value[$j] = round($value[$j], 1);			     
		  $value[$j] = $value[$j].' m3';
	   }
	   $value[4] = $value[2] - $value[3];
	   $value_procent = $value[2]/100;
	   $value[5] = $value[3]/$value_procent;
       $value[5] = round($value[5], 0);
	   $value[5] = 100 - $value[5];
	   $value[5] = $value[5].' %';
	      
       $table_all_sum .='<tr>';
          for($i = 2; $i <= 3; $i++){
             if($i <= 3) {
		        $value[$i] = number_format($value[$i], 2, ',', ' ');
		     }
             $table_all_sum .= '<td id="line_value" style=" color: '.$text_color[$i].'; border: 1px dotted black;">'.$value[$i].'</td>';
          }
       $table_all_sum .='</tr>';
       
       $table_all_sum .='<tr>';
          $table_all_sum .='<td  colspan="2" style=" border: 1px dotted black; font-weight: 600; height: 20px; background: #246624;">'.'</td>';
       $table_all_sum .='</tr>';
       
       $table_all_sum .='<tr>';
       for($i = 4; $i <= 5; $i++) {
          $table_all_sum .= '<td style=" color: white; border: 1px dotted black; font-weight: 600; background: '.$background_color[$i].'">'.$table_label[$i].'</td>'; 
       }
       $table_all_sum .='</tr>';
       
       $table_all_sum .='<tr>';	      
          for( $i = 4; $i <= 5; $i++) {
		     if($i == 4) {
			    $value[4] = number_format($value[4], 2, ',', ' ');
		     }
		     $table_all_sum .= '<td id="line_value" style=" color: white; border: 1px dotted black; background: '.$background_color[$i].'">'.$value[$i].'</td>';
          }
       $table_all_sum .='</tr>';	   
   $table_all_sum .= '<tbody>';
$table_all_sum .= '</table>';
	   return $table_all_sum;	
}
echo table_subtotal();
