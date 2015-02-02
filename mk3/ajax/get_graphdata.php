<?php

global $sql;

$selected_period = strtoupper($param[0]); // e.g. 'DAY';
$selected_start = false;
$ids=false;
if (isset($_GET) && isset($_GET['snpids'])) {
  $ids = explode(',', $_GET['snpids']);
}
else {
  return;
}

if (!$selected_period || count($ids) <= 0) return;

if (isset($_GET) && isset($_GET['start'])) {
  $selected_start = $_GET['start'];
}

if ($selected_start == false) {
  $wclause = " log_date > NOW() -  INTERVAL 1 $selected_period ";
}
else {
  $wclause = " (log_date >= TIMESTAMP('".$selected_start."') and log_date <= (TIMESTAMP('".$selected_start."') + INTERVAL 1 $selected_period )) ";
}

$xmin='';
$xmax='';
$inpcnt =0; 
$response = array();

foreach ($ids as $ix => $selected_id) {

//calculate utc offset
$query1 = "select CAST((timestampdiff(SECOND,UTC_TIMESTAMP(),NOW())*1000) as signed) as utc_offset;";
$utc_offset = db_fetch(db_query($query1),DB_NUM)[0] + 0;  //utc offset in milliseconds

//get the header info.
$query1a="select distinct sp.id, sp.user_name as what, sp.units, sp.openedlabel, sp.closedlabel, sp.reversestate, npt.name as typename, npt.node_point_cat from site_node_points sp inner join site_nodes sn on sp.site_node_id = sn.id inner join node_points np on sp.node_point_id = np.id inner join node_point_types npt on np.node_point_type_id =  npt.id where sp.id = $selected_id; ";
$hdr = db_fetch(db_query($query1a));

$hdg= $hdr['what']." - ".$hdr['typename'].($hdr['units'] !='' ? " ( " .$hdr['units']. " )":"");
// we need to know the type of the node_point so we know which table to retrieve
switch ($hdr['node_point_cat']) {
case 'Sensor':
	$query2="select CAST(MIN(UNIX_TIMESTAMP(pm.log_date)) as signed)*1000 as min , CAST(MAX(UNIX_TIMESTAMP(pm.log_date)) as signed)*1000 as max from site_node_point_minutes pm  where pm.site_node_point_id = $selected_id and $wclause ;";
	break;
case 'Input':
	$query2="select CAST(MIN(UNIX_TIMESTAMP(pm.log_date)) as signed)*1000 as min , CAST(MAX(UNIX_TIMESTAMP(pm.log_date)) as signed)*1000 as max from site_node_point_states pm  where pm.site_node_point_id = $selected_id and $wclause ;";
	break;
}		
$minmax = db_fetch(db_query($query2),DB_NUM);
$minmax[0] += $utc_offset;
$minmax[1] += $utc_offset;

//$query2="select pm.id, pm.value, pm.log_date, DATE_FORMAT(pm.log_date,'%b %e, %Y %r') as fmtdate, (UNIX_TIMESTAMP(pm.log_date) - $minmax[0] )/60 as timediff from site_node_point_minutes pm  where pm.site_node_point_id = $selected_id and log_date > NOW() -  INTERVAL 1 $selected_period;";
switch ($hdr['node_point_cat']) {
case 'Sensor':
	$query3="select ((CAST(UNIX_TIMESTAMP(pm.log_date) as signed)*1000) + $utc_offset) as timems, pm.value from site_node_point_minutes pm  where pm.site_node_point_id = $selected_id and $wclause ;";
	break;
case 'Input':
	if ($inpcnt++ > 0) {
	  $inpofs = .015;
	  $xofs = intval(($minmax[1] - $minmax[0])/250);
	}
	else {
	  $inpofs=0;
	  $xofs = 0;
	}
	$query3="select ((CAST(UNIX_TIMESTAMP(log_date) as signed)*1000) + $utc_offset + $xofs) as timems, ".
	"IF(".$hdr['reversestate']." = 1,IF(state=1, 0+$inpofs,1+$inpofs) ,state+$inpofs) ".
	"as value from site_node_point_states where site_node_point_id = $selected_id and $wclause ;";
	break;
}		
$res =  db_query($query3);
$data=array();
$i=0;
while ($res && $r=db_fetch($res,DB_NUM)) {
  $r[0]+=0;
  $r[1]= floatval($r[1]);
  $data[$i++] = $r;
}
// use the lowest/highest value from any series
if ($xmin == '') $xmin = $minmax[0]; else $xmin = min($xmin, $minmax[0]); 
if ($xmax == '') $xmax = $minmax[1]; else $xmax = max($xmax, $minmax[1]); 

$response[] = array(
	'heading' => $hdg,
	'uri' => $_SERVER['REQUEST_URI'],   //so we can save it
	'snpids' => $ids,
	'count' => $i,
	'category' => $hdr['node_point_cat'],
	'toplabel'    => $hdr['openedlabel'],
	'bottomlabel' => $hdr['closedlabel'],
	'xminval' => $xmin,
	'xmaxval' => $xmax,
	'utc_offset' => $utc_offset,
	'id' => $selected_id,
	'period' => '1 '.$selected_period,
	'data' => $data
);
} //foreach ids
foreach ($response as $v) {
  $v['xminval'] = $xmin;
  $v['xmaxval'] = $xmax;
}
echo json_encode($response);
#$h=@fopen('/tmp/xx.log','w');
#@fwrite($h,json_encode($response));
#@fclose($h)

?>

