
<div class="clearall"></div>
<div class="preamble"> <!-- preamble -->
<p>Actuators let you switch electrical devices on and off, remotely.
</p>
</div > <!-- preamble -->

<div class="askmimo actuators" style="min-height:30em;">
<div style="width:80%;margin: 1em auto 0 auto;">
<table><tbody>
<tr><th>Actuators</th><th>On Duration</th><th>Load Current</th><th>Turn on/off</th></tr>
<?php
$siteid=$_SESSION['user']['siteid'];
$query="select distinct sp.id as nodepointid, sp.user_name as what, npt.name as typename, npt.node_point_cat, sp.is_active, a.is_active as area_is_active, sp.state, IFNULL(sp.converted_reading,'null') as converted_reading, sp.units, sp.last_heard_from, TIMESTAMPDIFF(MINUTE, sp.last_heard_from,NOW()) as last_heard_from_min, e.id as eventid, e.type as threshold_type, e.threshold, e.threshold_direction, e.threshold_duration, e.rolling_view, e.profile_this, e.profile_warning_perc, e.log_frequency from site_node_points sp inner join site_nodes sn on sp.site_node_id = sn.id  inner join areas a on sn.area_id = a.id inner join node_points np on sp.node_point_id = np.id inner join node_point_types npt on np.node_point_type_id =  npt.id left join event e on sp.id =  e.point_id ";
$query .=  "where sn.site_id = $siteid and np.node_point_type_id != 17 ".
           "and npt.node_point_cat in ('Actuator') ";

$query .= ';';
$res =  db_query($query);
while ($res && $r=db_fetch($res)) {
/*
 * the questions: what is on, what is duration, what is load
 */ 
  if (!$is_on) {
    $timeon='';
    $load='';
  }
  echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td><div class="clickable %s"></div></td></tr>'."\r\n",
	$r['what'],  $timeon, $load. ($is_on ? "on" : "off"));
 
}
?>
<tr><td>Lights</td><td>00:12:54</td><td>1.15A</td><td><div class="clickable on"></div></td></tr>
<tr><td>Sprinklers</td><td>00:12:54</td><td>1.15A</td><td><div class="clickable off"></div></td></tr>
<tr><td>Outside lights</td><td>00:12:54</td><td>1.15A</td><td><div class="clickable off"></div></td></tr>
</tbody></table>
</div> <!-- centre -->
<br />
<br />
<div style="margin:1.5em 5ex 2em 67%;"><a href="#"><input style="float:left;" type="button" name="ok" id="ok" value="Save changes"  /></a><a href="index.php?page=askmimo&amp;id=2"><input style="float:right;"  type="button" name="cancel" id="cancel" value="Cancel" /></a></div>
</div> <!-- askmimo actuators -->
