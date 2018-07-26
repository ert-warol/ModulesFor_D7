<?php
/**
 * Created by PhpStorm.
 * User: opo_sav
 * Date: 21.05.2015
 * Time: 8:24
 */
$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$MonthsNames = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
$line = array(NULL, '1-я линия', '2-я линия', '3-я линия', '4-я линия');
$line_link = array(NULL, 'PrLine1', 'PrLine2', 'PrLine3', 'PrLine4');
$first_year = 2012;
$Year_interval = array(2012, 2013, 2014, 2015);
$last_year = date("Y"); 
function monthdays($month, $year)
{
    return date("t", strtotime($year . "-" . $month . "-01"));
}
switch($period)
{
    default:
    case 'yearly':
      for ($i = 1; $i <= 4; $i++) {
        $Query[$i] = "SELECT YEAR( Dat) AS label, SUM( $line_link[$i] ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR(Dat) >=$first_year GROUP BY YEAR( Dat) ORDER BY YEAR(Dat)";        
        $ResultArray[$i] = array_fill($first_year, 4, 0); // fill the Result array with 0 values for each month
        }                  
      $ChartHeading = 'Годовой развернутый показатель по производству 4-х линий ЦПО: '.$first_year.'-'.$last_year;
      $XaxisName = 'Временной период- Годы';
    break;  
    case 'monthly':                
        for ($i = 1; $i <= 4; $i++) {
          $Query[$i] = "SELECT MONTH( Dat) AS label, SUM( $line_link[$i] ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR(Dat) = $Year GROUP BY MONTH( Dat) ORDER BY MONTH(Dat)";
          $ResultArray[$i] = array_fill(1, 12, 0); // fill the Result array with 0 values for each month
        }                  
        $ChartHeading = 'Месячный развернутый показатель по производству 4-х линий ЦПО: '.$Year;
        $XaxisName = 'Временной период- Месяцы';

        break;

    case 'daily':
        for ($i = 1; $i <= 4; $i++) {
          $Query[$i] = "SELECT DAY( Dat ) AS label, SUM( $line_link[$i] ) AS Total FROM  cpo.dbo.RsProizvCPO WHERE YEAR( Dat )= $Year AND MONTH( Dat )= $Month GROUP BY DAY( Dat ) ORDER BY DAY(Dat)";
          $number_of_days = monthdays($Month, $Year);          
          $ResultArray[$i] = array_fill(1, $number_of_days, 0);  // fill the Result array with 0 values for each day
        }
        /*$quantity_days_in_month = array_fill(1, $number_of_days, 0);
          for($namber_day = 1;  $namber_day <= $number_of_days; $namber_day++) {
            $quantity_days_in_month[$namber_day] = $namber_day;
          }*/        
        $ChartHeading = 'Дневной развернутый показатель по производству 4-х линий ЦПО: '.$MonthsNames[$Month].'/'.$Year;
        $XaxisName = 'Временной период- Дни';
        break;

    case 'hourly':
        $Query[$i] = "SELECT Hours_Smena AS Value, SUM( 1_line + 2_line + 3_line + 4_line ) AS Total FROM  cpo.dbo.RsProizvCPO WHERE YEAR(Dat)=$Year AND MONTH( Dat )={$Month} AND DAY(Dat)={$Day}
        GROUP BY MONTH( Dat ) , DAY( Dat ) , Value";
        $ResultArray = array_fill(0, 23, 0);   // fill the Result array with 0 values for each hour
        $ChartHeading = 'Hourly New Production figures for the Date: '.$Day.'/'.$MonthsNames[$Month].'/'.$Year;
        $XaxisName = 'Временной период- Часы';
        break;
}

//Connect to database
require 'sql_srv_conect.php';

//Query the database
for ($i = 1; $i <= 4; $i++) {
   $QueryResult[$i] = sqlsrv_query($conn, $Query[$i]);

//Fetch results in the Result Array 
while($Row = sqlsrv_fetch_array($QueryResult[$i]))
    $ResultArray[$i][$Row['label']]=$Row['Total'];
    $i = $i++;   
}

//Generate Chart XML: Head Part
$Output ="<chart caption='$ChartHeading' xaxisname='$XaxisName' yaxisname='Произведено тыс. тонн' thousandSeparatorPosition='0' numberScaleValue = '2' showlabels='1' showvalues='1' decimals='2' formatNumberScale='0' FormatNumber='0' numberprefix='' numberSuffix=' тыс.' placevaluesinside='1' rotatevalues='1' valueFontSize='11px' bgcolor='FFFFFF' legendshadow='0' legendborderalpha='50' canvasborderthickness='1' canvasborderalpha='50' palettecolors='#E8B989,#AFD8F8,#F6BD0F,#8BBA00' showborder='0' theme='fint'>"; 

//Generate Chart XML: Main Body
switch($period)
{
    default:
      case 'yearly':

            $Output .= '<categories>';
            foreach ( $Year_interval as $value) {
              //$value .=' год';
              $Output .='<category label="'.$value.'" />';
            }                       
            $Output .= '</categories>'; 
              for ($i = 1; $i <= 4; $i++) {               
                 $Output .= '<dataset seriesname="'.$line[$i].'">';                             
                   foreach($ResultArray[$i] as $yearly => $value) {
                   $value = $value/1000;
                   $value = round($value, 2); 
                   $Output .= '<set value="'.$value.'" link="newchart-xmlurl-cpo_4_chart?type=monthly&amp;year='.$yearly.'&amp;number_line='.$line_link[$i].'"/>';         
                   }     
        	     $Output .= '</dataset>';   	      
              }       	
        break;
      case 'monthly':

            $Output .= '<categories>';
            foreach ( $MonthsNames as $value) {
              $Output .='<category label="'.$value.'" />';
            }                       
            $Output .= '</categories>'; 
              for ($i = 1; $i <= 4; $i++) {               
                 $Output .= '<dataset seriesname="'.$line[$i].'">';                             
                   foreach($ResultArray[$i] as $MonthNumber => $value) {
                   $value = $value/1000;
                   $value = round($value, 2); 
                   $Output .= '<set value="'.$value.'" link="newchart-xmlurl-cpo_4_chart?type=daily&amp;year='.$Year.'&amp;month='.$MonthNumber.'&amp;number_line='.$line_link[$i].'"/>';         
                   }     
        	     $Output .= '</dataset>';   	      
              }       	
        break;
      case 'daily':
            $Output .= '<categories>';
            //foreach ( $quantity_days_in_month as $value) 
            for ($day_value = 1; $day_value <= $number_of_days; $day_value++) {
              $value = $day_value;
              $value .= ' '.$MonthsNames[$Month];              
              $Output .='<category label="'.$value.'" />';
            }                       
            $Output .= '</categories>'; 
              for ($i = 1; $i <= 4; $i++) {               
                 $Output .= '<dataset seriesname="'.$line[$i].'">';                             
                   foreach($ResultArray[$i] as $DayNumber => $value) {
                   $value = $value/1000;
                   $value = round($value, 2);
                   $Output .= '<set value="'.$value.'" link="newchart-xmlurl-cpo_4_chart?type=hourly&amp;year='.$Year.'&amp;month='.$Month.'&amp;day='.$DayNumber.'&amp;number_line='.$line_link[$i].'"/>';         
                   }     
        	     $Output .= '</dataset>';   	      
              }
        break;
    case 'hourly':
        foreach($ResultArray as $HourNumber => $value)  // HourNumber is hour (0-23)
            $Output .= '<set label="'.$HourNumber.'" value="'.$value.'"/>';
}
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;
?>