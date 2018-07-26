<link rel="stylesheet" type="text/css" href="chart_gas_style.css">
<?php
 
$today_all_date = date('Y-m-d');
$today_day = date('j');
$today_year = date('Y');
$today_month = date('m');
if ($today_month <=10) {
  $today_month = '0'.$today_month;
}
$today_all = $today_year.'-'.$today_month.'-'.'01';

function construction_table() { 
global $today_all_date, $today_all, $Query, $QueryResult, $ResultArray, $today_day;
          
          
          $Query[1] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(L4)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 1
                    GROUP BY Dat, Line  
                    ORDER BY Dat";              
                        
          $ResultArray[1] = array_fill(1, $today_day, 0);  
          
          $Query[2] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(L8)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 1
                    GROUP BY Dat, Line  
                    ORDER BY Dat";              
                        
          $ResultArray[2] = array_fill(1, $today_day, 0);
           
          $Query[3] = "SELECT  Line, (convert(char(8), Dat, 112)) AS label, SUM(GazObg*1000)  AS Total 
                    FROM cpo_svod.dbo.LineRs 
                    WHERE (Dat BETWEEN '$today_all' AND '$today_all_date' ) AND Line = 1
                    GROUP BY Dat, Line  
                    ORDER BY Dat";             
                        
          $ResultArray[3] = array_fill(1, $today_day, 0);
           
          require 'sql_srv_conect.php';
          unset($ResultArray);
          for ($i = 1; $i <= 3; $i++) {
	         $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);
	         while($Row = sqlsrv_fetch_array($QueryResult[$i]))
                $ResultArray[$i][$Row['label']] = $Row['Total'];
          } 
return $ResultArray; }

   function first_table() {
      global $MonthsNames, $today_year, $today_month, $today_day;
      $MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	  $label_first_str = array(NULL, 'The name index');
      if($today_day <= 13) {
         $table_all = '<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12" id="first_table">';
         $table_all .= '<table border="0" cellpadding="0" cellspacing="0" style="" id="total_dynamic_table_1">';
            $table_all .= '<tbody>';
               $table_all .= '<tr>';
                  if($today_day > 4){
                     $interval = 4;
                  }
                  else {
                     $interval = $today_day;
                  }
                  for($i = 0; $i <= $interval; $i++) {
			         if ($i == 0): {
			            $value = 'The name index'; 
			         }
			         elseif ($i <= $today_day AND  $i > 0): {
			            $value = $i.' '.$MonthsNames[$today_month].' '.$today_year; 
			         }			    
			         endif;
			         $table_all .='<td style="width: 100px; height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value.'</td>';
                  }                  
               $table_all .= '</tr>';
               $table_all .= '<tr>';                  
                     $value_red_line[0] = 'G1 Red line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[1] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_red_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 4){
                        $interval = 4;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 0; $i <= $interval; $i++) {
			            if( $i > 0 ) {
			               $text_position = 'right';
			            }			     			
			            else {
			               $text_position = 'center';
			            }
			            if( $i == $today_day+1) {
			               $text_weight = 600;
			            }
			            else {
			               $text_weight = 400;
			            }
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; font-size: 16px; text-align: '.$text_position.'; color: red; font-family: calibri; margin: 0; font-weight: '.$text_weight.';  border: 1px dotted black;">'.$value_red_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                  
                     $value_blue_line[0] = '(G1-1)+∆ Blue line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[2] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_blue_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 4){
                        $interval = 4;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 0; $i <= $interval; $i++) {
			            if( $i > 0 ) {
			               $text_position = 'right';
			            }			     			
			            else {
			               $text_position = 'center';
			            }
			            if( $i == $today_day+1) {
			               $text_weight = 600;
			            }
			            else {
			               $text_weight = 400;
			            }
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: rgba(70, 136, 71, 0.34); font-size: 16px; text-align: '.$text_position.'; color: blue; font-family: calibri; margin: 0; font-weight: '.$text_weight.';  border: 1px dotted black;">'.$value_blue_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                  
                     $value_green_line[0] = 'G2 Green line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[3] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_green_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 4){
                        $interval = 4;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 0; $i <= $interval; $i++) {
			            if( $i > 0 ) {
			               $text_position = 'right';
			            }			     			
			            else {
			               $text_position = 'center';
			            }
			            if( $i == $today_day+1) {
			               $text_weight = 600;
			            }
			            else {
			               $text_weight = 400;
			            }
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; font-size: 16px; text-align: '.$text_position.'; color: green; font-family: calibri; margin: 0; font-weight: '.$text_weight.';  border: 1px dotted black;">'.$value_green_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                
                     if($today_day > 4){
                        $interval = 4;
                     }
                     else {
                     $interval = $today_day;
                     }                     
			         for ($i = 0; $i <= $interval; $i++) {
			            $value_black[$i] = $value_blue_line[$i] - $value_green_line[$i];
			            if( $i > 0 ) {
			               $text_position = 'right';
			            }			     			
			            else {
			               $text_position = 'center';
			            }	
			            $value_black[0] = '∆Gbio';		            
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: rgba(70, 136, 71, 0.34); font-size: 16px; text-align: '.$text_position.'; color: black; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_black[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                
                     if($today_day > 4){
                        $interval = 4;
                     }
                     else {
                     $interval = $today_day;
                     }                     
			         for ($i = 0; $i <= $interval; $i++) {
			           $value_procent_g1[$i] = $value_red_line[$i] / 100;
                       $value_procent_g1[$i] = $value_green_line[$i] / $value_procent_g1[$i];
                       $value_procent_g1[$i] = 100 - round($value_procent_g1[$i], 1).' %';
                       $value_procent_g1[0] = 'Savings Gas G1';
                       if( $i > 0 ) {
			          $text_position = 'right';
			       }			     			
			       else {
			          $text_position = 'center';
			       }			       
			       $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: #C16F6F; font-size: 16px; text-align: '.$text_position.'; color: white; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_procent_g1[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                
                     if($today_day > 4){
                        $interval = 4;
                     }
                     else {
                     $interval = $today_day;
                     }                     
			         for ($i = 0; $i <= $interval; $i++) {
			           $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                       $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                       $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';
                       $value_procent_g1_1[0] = 'Savings Gas G1-1';
                       if( $i > 0 ) {
			          $text_position = 'right';
			       }			     			
			       else {
			          $text_position = 'center';
			       }			       
			       $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 16px; text-align: '.$text_position.'; color: white; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';
			         }
                $table_all .= '</tr>';
            $table_all .= '</tbody>';	    
         $table_all .= '</table>';
      $table_all .= '</div>';
      }
      return $table_all;
   }
   function second_table() {
      global $MonthsNames, $today_year, $today_month, $today_day;
      $MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');	  
      if($today_day <= 13) {
         $table_all = '<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12" id="second_table">';
         $table_all .= '<table border="0" cellpadding="0" cellspacing="0" style="" id="total_dynamic_table_2">';
            $table_all .= '<tbody>';
               $table_all .= '<tr>';
                  for($i = 5; $i <= 9; $i++) {			         
			         $value = $i.' '.$MonthsNames[$today_month].' '.$today_year; 			         
			         $table_all .='<td style="width: 100px; height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0;  border: 1px dotted black; ">'.$value.'</td>';
                  }                  
               $table_all .= '</tr>';
               $table_all .= '<tr>';                  
                     $value_red_line[0] = 'G1 Red line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[1] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_red_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 9){
                        $interval = 9;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 5; $i <= $interval; $i++) {			            
			            $table_all .= '<td style="width: 100px; height: 50px; padding-right: 5px; border-bottom: none; font-size: 16px; text-align: right; color: red; font-family: calibri; margin: 0; font-weight: '.$text_weight.';  border: 1px dotted black;">'.$value_red_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
                 $table_all .= '<tr>';                  
                     $value_blue_line[0] = '(G1-1)+∆ Blue line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[2] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_blue_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 9){
                        $interval = 9;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 5; $i <= $interval; $i++) {
			            
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: rgba(70, 136, 71, 0.34); font-size: 16px; text-align: right; color: blue; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_blue_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                  
                     $value_green_line[0] = 'G2 Green line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[3] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_green_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 9){
                        $interval = 9;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 5; $i <= $interval; $i++) {			            
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; font-size: 16px; text-align: right; color: green; font-family: calibri; margin: 0; font-weight: ;  border: 1px dotted black;">'.$value_green_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                
                     if($today_day > 9){
                        $interval = 9;
                     }
                     else {
                     $interval = $today_day;
                     }                     
			         for ($i = 5; $i <= $interval; $i++) {
			            $value_black[$i] = $value_blue_line[$i] - $value_green_line[$i];			            	
			            $value_black[0] = '∆Gbio';		            
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: rgba(70, 136, 71, 0.34); font-size: 16px; text-align: right; color: black; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_black[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                
                     if($today_day > 9){
                        $interval = 9;
                     }
                     else {
                     $interval = $today_day;
                     }                     
			         for ($i = 5; $i <= $interval; $i++) {
			           $value_procent_g1[$i] = $value_red_line[$i] / 100;
                       $value_procent_g1[$i] = $value_green_line[$i] / $value_procent_g1[$i];
                       $value_procent_g1[$i] = 100 - round($value_procent_g1[$i], 1).' %';
                       $value_procent_g1[0] = 'Savings Gas G1';                    			       
			           $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: #C16F6F; font-size: 16px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_procent_g1[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                
                     if($today_day > 9){
                        $interval = 9;
                     }
                     else {
                     $interval = $today_day;
                     }                     
			         for ($i = 5; $i <= $interval; $i++) {
			           $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                       $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                       $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';
                       $value_procent_g1_1[0] = 'Savings Gas G1-1';                   			       
			           $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 16px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';
			         }
                $table_all .= '</tr>';
            $table_all .= '</tbody>';	    
         $table_all .= '</table>';
      $table_all .= '</div>';
      }
      return $table_all;
   }
   function third_table() {
      global $MonthsNames, $today_year, $today_month, $today_day;
      $MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');	  
      if($today_day <= 13) {
         $table_all = '<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12" id="third_table">';
         $table_all .= '<table border="0" cellpadding="0" cellspacing="0" style="" id="total_dynamic_table_3">';
            $table_all .= '<tbody>';
               $table_all .= '<tr>';
                  if($today_day > 14){
                     $interval = 14;
                     }
                     else {
                     $interval = $today_day;
                     }
                  for($i = 10; $i <= $interval; $i++) {			         
			         $value = $i.' '.$MonthsNames[$today_month].' '.$today_year; 			         
			         $table_all .='<td style="width: 100px; height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0; border: 1px dotted black; ">'.$value.'</td>';
                  }                  
               $table_all .= '</tr>';
               $table_all .= '<tr>';                  
                     $value_red_line[0] = 'G1 Red line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[1] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_red_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 14){
                        $interval = 14;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 10; $i <= $interval; $i++) {			            
			            $table_all .= '<td style="width: 100px; height: 50px; padding-right: 5px; border-bottom: none; font-size: 16px; text-align: right; color: red; font-family: calibri; margin: 0; font-weight: 400; border: 1px dotted black;">'.$value_red_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                  
                     $value_blue_line[0] = '(G1-1)+∆ Blue line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[2] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_blue_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 14){
                        $interval = 14;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 10; $i <= $interval; $i++) {
			            
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: rgba(70, 136, 71, 0.34); font-size: 16px; text-align: right; color: blue; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_blue_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                  
                     $value_green_line[0] = 'G2 Green line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[3] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_green_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 14){
                        $interval = 14;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 10; $i <= $interval; $i++) {			            
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; font-size: 16px; text-align: right; color: green; font-family: calibri; margin: 0; font-weight: ;  border: 1px dotted black;">'.$value_green_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                
                     if($today_day > 14){
                        $interval = 14;
                     }
                     else {
                     $interval = $today_day;
                     }                     
			         for ($i = 10; $i <= $interval; $i++) {
			            $value_black[$i] = $value_blue_line[$i] - $value_green_line[$i];			            	
			            $value_black[0] = '∆Gbio';		            
			            $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: rgba(70, 136, 71, 0.34); font-size: 16px; text-align: right; color: black; font-family: calibri; margin: 0; font-weight: 600;  border: 1px dotted black;">'.$value_black[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                
                     if($today_day > 14){
                        $interval = 14;
                     }
                     else {
                     $interval = $today_day;
                     }                     
			         for ($i = 10; $i <= $interval; $i++) {
			           $value_procent_g1[$i] = $value_red_line[$i] / 100;
                       $value_procent_g1[$i] = $value_green_line[$i] / $value_procent_g1[$i];
                       $value_procent_g1[$i] = 100 - round($value_procent_g1[$i], 1).' %';
                       $value_procent_g1[0] = 'Savings Gas G1';                    			       
			           $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: #C16F6F; font-size: 16px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_procent_g1[$i].'</td>';
			         }
                $table_all .= '</tr>';
                $table_all .= '<tr>';                
                     if($today_day > 14){
                        $interval = 14;
                     }
                     else {
                     $interval = $today_day;
                     }                     
			         for ($i = 10; $i <= $interval; $i++) {
			           $value_procent_g1_1[$i] = $value_blue_line[$i] / 100;
                       $value_procent_g1_1[$i] = $value_green_line[$i] / $value_procent_g1_1[$i];
                       $value_procent_g1_1[$i] = 100 - round($value_procent_g1_1[$i], 1).' %';
                       $value_procent_g1_1[0] = 'Savings Gas G1-1';                   			       
			           $table_all .= '<td style=" width: 100px; height: 50px; padding-right: 5px; border-bottom: none; background: #778DCE;  font-size: 16px; text-align: right; color: white; font-family: calibri; margin: 0; font-weight: 400;  border: 1px dotted black;">'.$value_procent_g1_1[$i].'</td>';
			         }
                $table_all .= '</tr>';
            $table_all .= '</tbody>';	    
         $table_all .= '</table>';
      $table_all .= '</div>';
      }
      return $table_all;
   }
   function fourth_table() {
      global $MonthsNames, $today_year, $today_month, $today_day;
      $MonthsNames = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');	  
      if($today_day <= 18) {
         $table_all = '<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12" id="fourth_table">';
         $table_all .= '<table border="0" cellpadding="0" cellspacing="0" style="" id="total_dynamic_table_4">';
            $table_all .= '<tbody>';
               $table_all .= '<tr>';
                  if($today_day > 18){
                     $interval = 18;
                     }
                     else {
                     $interval = $today_day;
                     }
                  for($i = 15; $i <= $interval; $i++) {			         
			         $value = $i.' '.$MonthsNames[$today_month].' '.$today_year; 			         
			         $table_all .='<td style="width: 100px; height: 50px; padding-right: 5px; font-size: 16px; text-align: center; background: #246624; color: white; font-family: calibri; margin: 0; border: 1px dotted black; ">'.$value.'</td>';
                  }                  
               $table_all .= '</tr>';
               $table_all .= '<tr>';                  
                     $value_red_line[0] = 'G1 Red line';
			         $test = construction_table();			    
			         $j = 1;			  
			         foreach($test[1] as $smena => $value) {                   
                        $value = round($value, 1);                                    
                        $value_red_line[$j] = $value;
                        $j++;			     	 			 
			         }
                     if($today_day > 18){
                        $interval = 18;
                     }
                     else {
                     $interval = $today_day;
                     }
			         for ($i = 15; $i <= $interval; $i++) {
			            if( $i > 0 ) {
			               $text_position = 'right';
			            }			     			
			            else {
			               $text_position = 'center';
			            }
			            if( $i == $today_day+1) {
			               $text_weight = 600;
			            }
			            else {
			               $text_weight = 400;
			            }
			            $table_all .= '<td style="width: 100px; height: 50px; padding-right: 5px; border-bottom: none; font-size: 16px; text-align: right; color: red; font-family: calibri; margin: 0; font-weight: '.$text_weight.'; border: 1px dotted black;">'.$value_red_line[$i].'</td>';
			         }
                $table_all .= '</tr>';
            $table_all .= '</tbody>';	    
         $table_all .= '</table>';
      $table_all .= '</div>';
      }
      return $table_all;
   }
   $first_table = first_table();
   $second_table = second_table();
   $third_table = third_table();
   $fourth_table = fourth_table();   
   echo $first_table;
   echo $second_table;
   echo $third_table;
   echo $fourth_table;
?>

