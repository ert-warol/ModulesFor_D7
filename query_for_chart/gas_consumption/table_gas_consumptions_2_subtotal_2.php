<link rel="stylesheet" type="text/css" href="chart_gas_style.css">
<?php

$today_all_date = date('Y-m-d');
$today_year = date('Y');
$today_month = date('m');
$today_day = date('j');

$today_all = $today_year.'-'.$today_month.'-'.'16';

function construction_table() { 
global $today_all_date, $today_all, $Query, $QueryResult, $ResultArray, $today_day;
          
          $Query[1] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(L8)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 2
                    GROUP BY Dat, Line  
                    ORDER BY Dat";              
                        
          $ResultArray[1] = array_fill(1, $today_day, 0);
           
          $Query[2] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(GazObg*1000)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 2
                    GROUP BY Dat, Line  
                    ORDER BY Dat";             
                        
          $ResultArray[3] = array_fill(1, $today_day, 0);
           
          require 'sql_srv_conect.php';
          unset($ResultArray);
          for ($i = 1; $i <= 2; $i++) {
	         $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);
	         while($Row = sqlsrv_fetch_array($QueryResult[$i]))
                $ResultArray[$i][$Row['label']] = $Row['Total'];
          } 
return $ResultArray; }


?>
<table border="0" cellpadding="0" cellspacing="0" style="width:100%" id="total_table">
	<tbody>
	    <tr>
	       <?php 
	          $value_top = 'Subtotal - '.$today_all.' /'.$today_all_date;
	          echo '<td colspan="2" id="total_table_main_td">'.$value_top.'</td>'
	       ?>
	    </tr>
	       <?php
              $table_label = array(NULL, '(G1-1)+∆ Blue line', 'G2 Green line', '∆Gbio', 'Savings GAS G1-1');
              $text_color = array(NULL, 'blue', 'green');              
              for($i = 1; $i <= 2; $i++) {
                 echo '<td style=" color: '.$text_color[$i].'; border: 1px dotted black; font-weight: 600; width: 33%; background: rgba(70, 136, 71, 0.34);">'.$table_label[$i].'</td>'; 
              }	          
	       ?>
		<tr>
			<?php 
			 
			  $value_all = construction_table();
			  for($j = 1; $j <= 2; $j++) {
			     $value[$j] = array_sum($value_all[$j]);
			     $value[$j] = round($value[$j], 1);			     
			     $value[$j] = $value[$j].' m3';
			  }
			  $value[3] = $value[1] - $value[2];
			  $value[3] = $value[3].' m3';
			  //$value_procent = $value[1]/100;
			  $value[4] = $value[1]/100;
			  $value[4] = $value[2]/$value[4];
			  $value[4] = round($value[4], 1);
			  $value[4] = 100 - $value[4];
			  $value[4] = $value[4].' %';			    
			  $text_color = array(NULL, 'blue', 'green', 'black', 'black');
			  for( $i = 1; $i <= 2; $i++) {
			     if($i <= 2) {
			        $value[$i] = number_format($value[$i], 2, ',', ' ');
			     }			        			     			     
			     echo '<td id="line_value" style=" color: '.$text_color[$i].'; border: 1px dotted black;">'.$value[$i].'</td>'; 
			  }		   
			?>			
		</tr>
		<tr>
			<?php
			   for($i = 3; $i <= 4; $i++) {
                  echo '<td style=" color: '.$text_color[$i].'; border: 1px dotted black; font-weight: 600; height: 20px; background: #246624;">'.'</td>'; 
              }
			?>			
		</tr>
		<tr>
			<?php
			
			   $background_color = array(NULL, NULL, NULL, '#717171', '#778DCE');
			   for($i = 3; $i <= 4; $i++) {
                  echo '<td style=" color: white; border: 1px dotted black; font-weight: 600; background: '.$background_color[$i].'">'.$table_label[$i].'</td>'; 
              }
			?>			
		</tr>
		<tr>
			<?php
			   
			   for( $i = 3; $i <= 4; $i++) {
			     if($i == 3) {
			        $value[$i] = number_format($value[$i], 2, ',', ' ');
			     }			        			     			     
			      echo '<td id="line_value" style=" color: white; border: 1px dotted black; background: '.$background_color[$i].'">'.$value[$i].'</td>'; 
			  }	
			?>			
		</tr>
    </tbody>
</table>
