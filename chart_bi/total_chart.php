<?php
header('Content-Type: text/html; charset= utf-8');

require 'connect.php';
mysql_query("SET NAMES utf8 COLLATE utf8_unicode_ci");

function chart_column2d(){
  $query = mysql_query("SELECT count(node.nid) value, FROM_UNIXTIME( node.created,  '%m' ) MONTHS, FROM_UNIXTIME( node.created,  '%y' ) YEARS,
                               CONCAT(LEFT(ELT(FROM_UNIXTIME(node.created, '%m'), 'Янв','Фев','Март','Апр','Май','Июнь','Июль','Авг','Сен','Окт','Ноя','Дек'),3),'/', FROM_UNIXTIME( node.created,  '%y' )) as MonthName_SMALL
                        FROM node
                        INNER JOIN taxonomy_index ti ON node.nid = ti.nid
                        INNER JOIN taxonomy_term_hierarchy tth ON ti.tid = tth.tid
                        WHERE TYPE =  'forum'
                              AND tth.parent = 2
                              AND FROM_UNIXTIME( node.created,  '%Y%m%d' ) >=20160401
                              AND FROM_UNIXTIME( node.created,  '%Y%m%d' ) <=20170331
                        GROUP BY MONTHS
                        ORDER by YEARS, MONTHS");

  if (!$query) {
    die('Error executing the query:' . mysql_error());
  }

  $i = 0;
  while ($row = mysql_fetch_assoc($query)) {
    $result[$i] = $row;
    $i++;
  }

  $Output = "<chart caption='Всего идей по месяцам'
                 subcaption=''
                 palettecolors='#4f81bd'
                 bgcolor='#ffffff'
                 rotatevalues='0'
                 showalternatehgridcolor='1'
                 alternatehgridcolor='#99ccff'
                 alternatehgridalpha='30'
                 showborder='0'
                 decimals='1'
                 captionfontsize='22'
                 subcaptionfontsize='14'
                 subcaptionfontbold='0'
                 labelFontSize='14'
                 tooltipcolor='#ffffff'
                 tooltipborderthickness='0'
                 tooltipbgcolor='#000000'
                 tooltipbgalpha='80'
                 tooltipborderradius='2'
                 tooltippadding='5'
                 showhovereffect='1'
                 showlegend='1'
                 legendbgcolor='#ffffff'
                 legendborderalpha='0'
                 legendshadow='0'
                 legenditemfontsize='14'
                 legenditemfontcolor='#666666'
                 usedataplotcolorforlabels='1'
                 valueFontSize='16'
                 theme='carbon' >";

  for ($i = 0; $i <= 11; $i++) {
      $Output .= "<set label='" . $result[$i]['MonthName_SMALL'] . "' value='" . $result[$i]['value'] . "' />";
  }

  return $Output .= "</chart>";
}

$result = chart_column2d();

header('Content-type: text/xml');
//Send output
echo $result;
