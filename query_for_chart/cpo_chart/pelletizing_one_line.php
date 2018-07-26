<?php

$period	= $_GET['type'];
$Year   = intval($_GET['year']);
$Month 	= intval($_GET['month']);
$Day 	= intval($_GET['day']);
$MonthsNames = array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябырь', 'Ноябрь', 'Декабрь');
$day_interval = array();
$first_year = 2012;
$last_year = date("Y");
$year_interval = array();
$today_year = date('Y'); 
$j = 0;
while ($i < $today_year) {
   $i = $first_year + $j;   
   $year_interval[$j] = $i;
   $j++;    
}  
function monthdays($month, $year)
{
   return date("t", strtotime($year . "-" . $month . "-01"));
}

//Connect to database
require 'sql_srv_conect.php';

//Query the database
$Query = "SELECT YEAR( Dat) AS label, SUM( PrLine1 ) AS Total FROM cpo.dbo.RsProizvCPO WHERE YEAR(Dat) >=$first_year GROUP BY YEAR( Dat) ORDER BY YEAR(Dat)";        
$ResultArray = array_fill($first_year, 4, 0); // fill the Result array with 0 values for each month           
$y_positions = '0';
$FontSize = '11px';                  
$ChartHeading = 'Годовой развернутый показатель по производству 4-х линий ЦПО: '.$first_year.'-'.$last_year;
$XaxisName = 'Период времени - Годы';
$QueryResult = sqlsrv_query($conn, $Query);  

//Fetch results in the Result Array 
while($Row = sqlsrv_fetch_array($QueryResult))
    $ResultArray[$Row['label']]=$Row['Total'];         


//Generate Chart XML: Head Part
$Output ="<chart caption='$ChartHeading' xaxisname='$XaxisName' rotateLabels='$y_positions' yaxisname='Произведено тыс. тонн' xAxisNameFontSize='14' yAxisNameFontSize='14' thousandSeparatorPosition='0' numberScaleValue = '2' showlabels='1' showvalues='1' decimals='2' formatNumberScale='0' FormatNumber='0' numberprefix='' numberSuffix=' тыс.' placevaluesinside='1' rotatevalues='1' valueFontSize='$FontSize' bgcolor='FFFFFF' legendshadow='0' legendborderalpha='50' canvasborderthickness='1' canvasborderalpha='50' palettecolors='#246624,#AFD8F8,#F6BD0F,#E8B989' showborder='0' theme='fint'>";
$Output .= '<categories>';
   foreach ( $year_interval as $value) {
     //$value .=' год';
    $Output .='<category label="'.$value.'" />';
  }                       
$Output .= '</categories>'; 
$Output .= '<dataset seriesname="Line 1">';                             
  foreach($ResultArray as $yearly => $value) {
     $value = $value/1000;
     $value = round($value, 2); 
     $Output .= '<set value="'.$value.'" link="newchart-xmlurl-pelletizing_1_line.php?type=monthly&amp;year='.$yearly.'&amp;number_line=line_1"/>';         
      	      
  } 
     $Output .= '</dataset>';                              	
     
//Generate Chart XML: Last Part
$Output .= '</chart>';

//Set the output header to XML
header('Content-type: text/xml');

//Send output
echo $Output;  