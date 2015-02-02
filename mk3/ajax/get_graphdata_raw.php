<?php

global $sql;


$selected_period = strtoupper($param[0]); // e.g. 'DAY';
$selected_type = intval($param[1]);       //the site_node_point id
$selected_id = intval($param[1]);         //the site_node_point id
$ids=false;
if (isset($_GET) && isset($_GET['ids'])) {
  $ids = explode(',', $_GET['ids']);
}
if (!$selected_period || !$selected_id) return;


$query="select distinct sp.id, sp.user_name as what, sp.units, npt.name as typename, npt.node_point_cat from site_node_points sp inner join site_nodes sn on sp.site_node_id = sn.id inner join node_points np on sp.node_point_id = np.id inner join node_point_types npt on np.node_point_type_id =  npt.id where sp.id = $selected_id; ";
$hdr = db_fetch(db_query($query));

echo "<h3>".$hdr['what']." - ".$hdr['typename'].($hdr['units'] !='' ? " ( " .$hdr['units']. " )":"")."</h3>\r\n";
echo "<div><table><tbody>\r\n";
switch ($hdr['node_point_cat']) {
case 'Sensor':
	$query="select DATE_FORMAT(pm.log_date,'%b %e, %Y %r') as fmtdate, pm.value from site_node_point_minutes pm where pm.site_node_point_id = $selected_id and pm.log_date > NOW() -  INTERVAL 1 $selected_period;";
	break;
case 'Input':
	$query="select DATE_FORMAT(ps.log_date,'%b %e, %Y %r') as fmtdate, ps.state as value from site_node_point_states ps where ps.site_node_point_id = $selected_id and ps.log_date > NOW() -  INTERVAL 1 $selected_period;";
	break;
}		
$odd='';
$res =  db_query($query);
while ($res && $r=db_fetch($res)) {
	switch ($hdr['node_point_cat']) {
	case 'Sensor':
 		$val = floatval($r['value']);
		break;
	case 'Input':
 		$val = floatval($r['value']);
		break;
	}		
	if ($odd == '') {
	  $odd =' class="odd"';
	  
	}
	else {
	  $odd ='';
	}
	echo sprintf("<tr%s><td>%s</td><td>%s</td></tr>\r\n", $odd, $r['fmtdate'],$val) ;
}		
echo "</tbody></table></div>\r\n";


?>


