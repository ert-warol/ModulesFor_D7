<?php
$MonthsNames_double_for_day = array(null, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$hours_period = array('21', '22', '23', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20');
$base_date = '2015-10-01';
$day_begin = date("j", strtotime($base_date));
$today_all_date = date('Y-m-d');
$today_year = date('Y');
$today_day = date('d');
$today_month = date('m');
$namber_value = array(NULL, 'Gas consumption the base period', 'Gas consumption bio' );
$color_line = array(NULL, '#F72B08', '#246624'); 
if ($today_month <=10) {
  $today_month = '0'.$today_month;
}
$today_all = $today_year.'-'.$today_month.'-'.$today_day; 
//echo '<br>'.'$today--'.$today_all.'</br>';
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
function construction_diagrams() {
   global $ChartHeading, $XaxisName, $base_date, $Query, $ResultArray, $FontSize, $y_positions, $today_all_date;
          
          unset($ResultArray);         
          $ResultArray = array();               
          $Query[1] = "SELECT Chas, Line, (convert(char(8), Dat, 112) + Chas) AS label, L1 AS Total 
                    FROM cpo_svod.dbo.LineRsH 
                    WHERE (Dat BETWEEN '$base_date' AND '$today_all_date' ) AND Line = 1
                    GROUP BY Line, Dat, Chas, L1  
                    ORDER BY Dat, Chas ";              
                        
          $ResultArray[1] = array_fill(1, 480, 0); // fill the Result array with 0 values for each month 
          
          $Query[2] = "SELECT Chas, Line, (convert(char(8), Dat, 112)+Chas) AS label, (GazObg*1000) AS Total 
                    FROM cpo_svod.dbo.LineRsH 
                    WHERE (Dat BETWEEN '$base_date' AND '$today_all_date' ) AND Line = 1
                    GROUP BY Line, Dat, Chas, L1, GazObg  
                    ORDER BY Dat, Chas";              
                        
          $ResultArray[2] = array_fill(1, 480, 0); // fill the Result array with 0 values for each month
          
          $y_positions = '0'; 
          $FontSize = '14px';                  
          $ChartHeading = 'Gas consumption : '.$base_date.' '.$today_all_date;
          $XaxisName = 'Hours period';      
}

construction_diagrams();
 
//Connect to database
require 'sql_srv_conect.php';

//Query the database

for ($i = 1; $i <=2; $i++) {

$QueryResult = sqlsrv_query($conn, $Query[$i]);  
   
//Fetch results in the Result Array 
   while($Row = sqlsrv_fetch_array($QueryResult))
      $ResultArray[$i][$Row['label']] = $Row['Total'];         

}
//Generate Chart XML: Head Part
$Output ="<chart caption='$ChartHeading' xaxisname='$XaxisName' baseFontSize='14' tickValueDistance='14' yAxisNameFont='Calibri' captionFont='Calibri' valueFont='Calibri' baseChartMessageFont='Calibri' labelheight='70' yAxisMinValue='-5000' yAxisMaxValue='3200' rotateLabels='1' yaxisname='Gas consumption m3/h' xAxisNameFontSize='14' yAxisNameFontSize='14' thousandSeparatorPosition='0' numberScaleValue = '2' showlabels='1' showvalues='1' decimals='2' formatNumberScale='0' FormatNumber='0' numberprefix='' numberSuffix=' ' placevaluesinside='1' rotatevalues='1' valueFontSize='$FontSize' bgcolor='FFFFFF' legendshadow='0' legendborderalpha='50' canvasborderthickness='1' canvasborderalpha='50' palettecolors='#246624' showborder='0' theme='fint'>"; 

//Generate Chart XML: Main Body
            
            $Output .= '<categories>';
            for($day = 1; $day <= $today_day; $day++) {            
               for($hour = 0; $hour <= 23; $hour++ ) {
                  $value = $day.'-'.$MonthsNames_double_for_day[10].' '.$hours_period[$hour].'h'; 
                  $Output .='<category label="'.$value.'" />';
               }
            }                                       
            $Output .= '</categories>'; 
            for ($i = 1; $i <= 2; $i++){           
               $Output .= '<dataset seriesname="'.$namber_value[$i].'" color="'.$color_line[$i].'" valuePosition="ABOVE">';                             
                  foreach($ResultArray[$i] as $hourly => $value) {                   
                     $value = round($value, 4); 
                     $Output .= '<set value="'.$value.'"/>';                            
                  }     
               $Output .= '</dataset>';
            }    	           
            
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;






