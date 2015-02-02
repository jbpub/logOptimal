<?php
/* vim: set ai sw=4 ts=4: */
$siteid=$_SESSION['user']['siteid'];
$area_sel=0;
if (isset($_REQUEST['a'])) $area_sel = intval($_REQUEST['a']);


$query = "select id, name  from areas where site_id = ".$siteid." order by name ;";
$res = db_query($query);
$areas = array();
while ($row =db_fetch($res)) {
	$areas[intval($row['id'])] = $row['name'];
}

//',rolling_view='.$p['rolling_view'].
#we always set rolling view to 1
# the update 
if (isset($_POST) && isset($_POST['nodepointid'])) {
	$p =array_map( function($v) {
	  return str_replace(array('Yes','No','Less than','Greater than'),array('1','0','1','0'),$v);
	}, $_POST);
	#$p['duration'] = (!$p['duration'] ||$p['duration'] =='' ? 0:$p['duration']) + 0;
	$p['duration'] = 0;
	if ($p['profile_this']+0 == 1) {
		$p['duration'] = 15;
	}
//	$doinsert=true;
	if (isset($p['eventid']) && $p['eventid'] != '') {
		$query='update event set point_id='.$p['nodepointid'].',type='.$p['type'].',threshold='.$p['threshold'].',threshold_direction='.$p['direction'].',threshold_duration='.$p['duration'].',profile_this='.$p['profile_this'].',profile_warning_perc='.$p['profile_warning_perc'].',log_frequency='.$p['log_frequency'].',modify_date=NOW(), rolling_view=1 where id='.$p['eventid'].';';
	//	echo $query;
  		//if ($res=db_query($query) &&  db_effected_rows() != 0) $doinsert=false;
  		$res=db_query($query);
	}
//	if ($doinsert) {
	else {
		$flds="point_id,type,threshold,threshold_direction, threshold_duration, profile_this,profile_warning_perc, log_frequency,modify_date, rolling_view ";
		$query="insert into event (".$flds.") values ( ".
		$p['nodepointid'].','.$p['type'].','.$p['threshold'].','.$p['direction'].', '.$p['duration'].','.$p['profile_this'].','.$p['profile_warning_perc'].','.$p['log_frequency'].',NOW(),1);';
	//	echo $query;
  		$res=db_query($query);
	}
	bgexec(QVMC.'ssreset');
//	echo('/bin/echo "'.DIR_ROOT.'cgi/qvmc ssreset" > 2> /tmp/zz < /dev/null');
}

?>
<script type="text/javascript">
$(document).ready(function() {
	$('.monitors .do_modal').on('click', function(e) {
		var formobj = $(this).next();
		//var form_inputs = $(this).next().children('input:hidden');
		var form_inputs = formobj.children('input[type="hidden"]');
		var sm = $('#modal_threshold').modal(
			{
			   close: true,
			   closeClass: 'close_btn',
			   fixed: false
			}
		);


		form_inputs.each(function(i,el) {
		var n=$(el).attr('name');
		var v=$(el).val().trim();
		var o=$('#threshold_'+ n);
		switch (n) {
		case 'head':
			o.text(v);
			break;
		case 'type':
			if (v!= 1) {o.toggleClass('on');o.toggleClass('off');}
			break;
		case 'duration':
		case 'threshold':
			if (v && v.length > 0)
				o.val(v);
			break;
		case 'profile_warning_perc':
		case 'log_frequency':
			if (v && v.length >= 0) {
				o.val(v);
			}
			break;
		case 'eventid':
		case 'nodepointid':
			break;
		default:
			if (v && v.length > 0) {
				console.log(n +'='+v);
				o.text(v);
			}
			break;
		}

	  	});


		$('#threshold_save').on('click', function(e) {
			formobj.children().each(function(i,el) {
				var n=$(el).attr('name');
				var o=$('#threshold_'+ n);
				switch (n) {
				case 'head':
				case 'eventid':
				case 'nodepointid':
					break;
				case 'type':
					$(el).val(o.hasClass('on')? '1' : '0');
					break;
				default:
					$(el).val(o.val() != '' ? o.val() : o.text());
					break;
				}
			});

			sm.close();
			formobj.submit();
			return false;
		});

		$('#xxthreshold_type').on('click', function(e) {
			$('#threshold_type').toggleClass('on');
			$('#threshold_type').toggleClass('off');
			return false;
			}
		);
	});

}); /* ready */


</script>

<div class="clearall"></div>
<div class="preamble"> <!-- preamble -->
<p>Monitors shows the Sensor values.
</p>
</div > <!-- preamble -->

<div class="askmimo monitors" style="min-height:38em;">
<div style="margin:1em 0 .5em 2ex;"><span style="font-weight:bold;font-size:1em;margin-right:1ex;">Select an Area</span><select id="area_list" >
<option<?php if ($area_sel==0) echo ' selected'; ?> value="0">&lt;All&gt;</option>
<?php
foreach ($areas as $k => $v ) {
   echo (sprintf('<option %s value="%d">%s</option>'."\r\n", 
	($area_sel == $k ? 'selected' : ''),
        $k, $v));
}
?>
</select>
<a href="index.php?page=askmimo&amp;id=3"><input style="margin-left:9ex;vertical-align:middle;" type="image" name="refresh" id="area_refresh" src="images/refresh.png" value="Refresh"  /></a>
</div>
<table><tbody>
<tr><td>Monitor</td><td>Active</td><td>Value</td><td>Last Heard</td><td>Threshold</td><td>Battery</td></tr>
<?php
$query="select distinct sp.id as nodepointid, sp.user_name as what, 
	npt.name as typename, npt.node_point_cat,
	sp.is_active, a.is_active as area_is_active,sn.is_active as sn_is_active,
	sp.state, IFNULL(sp.converted_reading,'null') as converted_reading,
	sp.units, sp.last_heard_from,
	TIMESTAMPDIFF(MINUTE, sp.last_heard_from,NOW()) as last_heard_from_min,
	TIMESTAMPDIFF(SECOND, sp.last_heard_from,NOW()) as last_heard_from_sec,
	e.id as eventid, e.type as threshold_type, e.threshold, e.threshold_direction,
	e.threshold_duration, e.rolling_view, e.profile_this,
	e.profile_warning_perc, e.log_frequency,

	(select cast(xp.converted_reading as decimal(6,2))
		from site_node_points xp
		inner join site_nodes xn on xp.site_node_id = xn.id
		inner join node_points xnp on xp.node_point_id = xnp.id
		where xnp.node_point_type_id = 17 and xn.id=sn.id) as voltage

	from site_node_points sp
	inner join site_nodes sn on sp.site_node_id = sn.id
	inner join areas a on sn.area_id = a.id
	inner join node_points np on sp.node_point_id = np.id
	inner join node_point_types npt on np.node_point_type_id =  npt.id
	left  join event e on sp.id =  e.point_id ";
$query .=  "where sn.site_id = $siteid and np.node_point_type_id != 17 ".
           "and npt.node_point_cat in ('Sensor', 'Input') ";

if ($area_sel != 0) {
  $query .= ' and a.id = '.$area_sel; 
}
$query .= ';';
# sp.openedlabel,sp.closedlabel, sn.zigbee_report_1, sn.zigbee_report_2, sp.reversestate,
 $res =  db_query($query);
$pnt = array();
$i=0;
while ($res && $r=db_fetch($res)) {
 
  echo '<tr><td>'.$r['what'].'</td>';
  $type = $r['node_point_cat'];
//value 
//print_r($r);
  if ($type == 'Sensor') {$v = $r['converted_reading'];}
  else { 
    $v = $r['state'];
    if ($v =='' &&  $r['converted_reading'] != '') {
      $v =  $r['converted_reading'];
    }
  }
  $active = intval($r['is_active']); //enabled/disabled
  if ($active && intval($r['sn_is_active']) == 0) $active=0;
  if ($active && intval($r['area_is_active']) == 0) $active=0;
  $lh = intval($r['last_heard_from_sec']);
  $lhv = $r['last_heard_from'];
  if ($lhv == '') $lh = -1;
  $units = $r['units'];
  if ($lh < 0 || $v == '') $active=0;


  if ($v == 'null' or  $v =='') {
    $units = '';
    $v = '&phi;';
  }
  else {
    $v = is_numeric($v) ? floatval($v) : $v ;
    if ($units == 'C' || $units == 'F') {$units = '&deg;'.$units;}
  } 
  echo '<td class="'.($active ? 'on' :'off').'"><div class="traffic"></div></td>';
  echo sprintf("<td>%s%s</td>\r\n", $v, $units);
  $dstr = '';
  if ($lh >= 0) {
  /*  */
  	$d = intval($lh/86400);
  	$h = intval(($lh%86400)/3600);
  	$m = intval($lh%3600)/60;
  	$s = intval($lh%60);
	if ($d > 0) $dstr .= sprintf('%u day%s ', $d,($d>1?'s':'')); 
	if ($d > 0 || $h > 0) $dstr .= sprintf('%u hr%s ', $h,($h>1?'s':'')); 
	if ($d > 0 || $h > 0 || $m > 0) $dstr .= sprintf('%u min%s ', $m,($m>1?'s':'')); 
	if ($d <= 0) $dstr .= sprintf('%u sec%s ', $s,($s>1?'s':'')); 
    $dstr .= 'ago'; 
  }
  else {
    $dstr = 'never';
  }
  echo '<td>'.$dstr.'</td>';
  // I think based on the vague description that both sensors and inputs can have 
  // a threshold
  echo '<td>';
  if ($r['threshold_type'] == '1') {
    $tdir =intval($r['threshold_direction']);
    switch ($r['threshold_type']) {
    case '0':
      //echo 'Inactive ';
      break;
    case '1':
      if ($tdir == 0) echo "Greater than ";
      else echo "Less than ";

      echo floatval($r['threshold'])+0;
      echo $r['units'];
      break;
    default:
      break;
    }
  }
  if ($type == 'Sensor') {
    $str = '<input type="hidden" name="%s" value="%s" />'."\r\n";
 
?>
<div class="do_modal">&#9660;</div>
<form method="post" action="index.php?page=askmimo&amp;id=3&amp;a=<?=$area_sel;?>">
<?php 
/* set defaults */
if (empty($r['eventid'])) {
	$r['profile_warning_perc']=0;
	$r['log_frequency']=30;
}
echo sprintf($str, 'head',$r['what'].' ( '.$r['typename'].' )');
echo sprintf($str, 'eventid',$r['eventid']);
echo sprintf($str, 'nodepointid',$r['nodepointid']);
echo sprintf($str, 'type',$r['threshold_type']);
echo sprintf($str, 'direction',($r['threshold_direction']!=1?'Greater than':'Less than'));
//echo sprintf($str, 'duration','15');
//echo sprintf($str, 'duration',$r['threshold_duration']+0);
echo sprintf($str, 'threshold',$r['threshold']+0);
//echo sprintf($str, 'rolling_view',($r['rolling_view']==1?'Yes':'No'));
echo sprintf($str, 'profile_this',($r['profile_this']==1?'Yes':'No'));
echo sprintf($str, 'profile_warning_perc',intval($r['profile_warning_perc']));
echo sprintf($str, 'log_frequency',$r['log_frequency']+0);
?>
</form>

<?php
  }
  else echo 'N/A';
  echo '</td><td>'.$r['voltage'].'V</td>';
  echo '</tr>'."\r\n";
/*<tr><td>Outside temp</td><td class="off"><span>&nbsp;</span><div class="traffic"></div></td><td>22&deg;C</td><td>5 mins ago</td><td>Less than 15.0&deg;C<div class="do_modal">&#9660;</div></td></tr>*/
}
?>
</tbody></table>
<br />
<br />

<div id="modal_threshold" class="threshold_dialog">
<div class="close_btn"></div><div class="clearboth" ></div>
<div style="margin:0 1ex 0 1ex;">
<h3 style="margin:1ex 1ex;" >Threshold parameters for<br /><span id="threshold_head"></span></h3>

<table>
<tbody class="dialog">
<tr><td>Enable/Disable:</td><td><span id="threshold_type" class="tickcross on clickable"></span></span></td><td></td></tr>
<?php
//<tr><td>Duration:</td><td></td><td></td></tr>
?>
<tr class="sensoronly"><td>Direction:</td><td><span id="threshold_direction" data="Greater than,Less than">Less than</span>
	</td><td><span class="spinh">
<!--	<input type="button" value="&#9650;" data="threshold_direction"  /> -->
	<input type="button" value="&#9660;" data="threshold_direction" />
	</span></td></tr>
<tr class="sensoronly"><td>Value:</td><td><input id="threshold_threshold" type="text" size="4" /></td></tr>

<?php
/*
<tr><td>Display in Rolling View?:</td><td><span id="threshold_rolling_view" data="Yes,No">No</span>
	</td><td><span class="spinh">
	<input type="button" value="&#9650;" data="threshold_rolling_view"  />
	<input type="button" value="&#9660;" data="threshold_rolling_view" />
	</span></td></tr>
*/	
?>
<tr><td>Create a Profile?:</td><td><span id="threshold_profile_this" data="Yes,No">No</span>
	</td><td><span class="spinh">
<!--	<input type="button" value="&#9650;" data="threshold_profile_this"  /> -->
	<input type="button" value="&#9660;" data="threshold_profile_this" />
	</span></td></tr>
<?php /*
<tr><td>Profile alert level:</td><td><span id="threshold_profile_warning_perc" data="0,10,20,25,30,40,50,60,70,80,90,100">
100</span><span>%</span>
	</td><td><span class="spinh">
	<input type="button" value="&#9650;" data="threshold_profile_warning_perc"  />
	<input type="button" value="&#9660;" data="threshold_profile_warning_perc" />
	</span></td></td></tr> */?>
<tr><td>Profile alert level:</td><td><select id="threshold_profile_warning_perc" style="width:7.5ex;">
<?php for ($i=0; $i<=100; $i+=5) {
	echo sprintf('<option value="%u">%u</option>%s',$i,$i,"\r\n");
} ?>
</select><span style="margin-left:1ex;font-weight:bold;">%</span></td></tr>

<?php /*
<tr><td>Profile log interval:</td><td><span id="threshold_log_frequency" data="1,2,3,4,5,6,10,12,15,20,30,60">30</span><span> minutes.</span></td>
	<td><span class="spinh">
	<input type="button" value="&#9650;" data="threshold_log_frequency"  />
	<input type="button" value="&#9660;" data="threshold_log_frequency" />
	</span></td></tr>
*/ ?>	
<tr><td>Profile log interval:</td><td><select id="threshold_log_frequency" style="width:7.5ex;">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="10">10</option>
<option value="12">12</option>
<option value="15">15</option>
<option value="20">20</option>
<option value="30">30</option>
<option value="60">60</option>
</select><span style="margin-left:1ex;">minutes</span></td></tr>
</tbody>
</table>
<div style="margin:1.5em 5ex 2em 50%;"><a href="#" ><input style="float:right;" type="button" name="ok" id="threshold_save" value="Save"  /></a></div>
<div class="clearboth" ></div>
</div></div>

<div style="margin:3.5em 5ex 2em 67%;"><a href="#"><input style="float:left;" type="button" id="advanced" value="Advanced Info."  /></a></div>
</div> <!-- askmimo monitors -->
