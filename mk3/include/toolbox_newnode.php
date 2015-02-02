<?php
/*
point is now inactive when any of the following are true:
1. Area is inactive
2. Node is inactive
3. Point is inactive
4. Area and Point are not in schedule
*/
function nonulls($a, $v)
{
    if (isset($a) && isset($a[$v]) )
	return $a[$v];
    else
	return '';
}
?>

<div class="clearall"></div>

<div class="preamble"> <!-- preamble -->
<p>Add a monitor node. If you have a new node with monitors you want to setup then start here.</p>
<p>
This process will only work if you have the site Station AND the Monitor Node are in close proximity.
<span class="hilitered">Important</span>
</p>
</div > <!-- preamble -->

<div class="toolbox newnode" style="min-height:48em;">
<?php
$siteid=$_SESSION['user']['siteid'];
$macid='';
if (isset($_REQUEST['macid'])) $macid = $_REQUEST['macid'];

/* start - update code */
if (isset($_REQUEST['update'])) {

  //add an area if we need to
  if ($_POST['area'] != $_POST['area_org']) {
    $query = "select id from areas where site_id = ".$siteid." and name = '".db_escstr($_POST['area'])."' ;";
    $areaid = intval(db_fetch(db_query($query),DB_NUM)[0]);
    if ($areaid <= 0) {
      $query = "insert into areas ( site_id, name, is_active ) values (".$siteid.", '".db_escstr($_POST['area'])."', '1');";
      $res = db_query($query);
      $areaid = db_insertid();
      if (intval($areaid) <= 0) die ("bad insert");

    }
  } 
  else {
    $areaid = $_POST['area_id'];
  }
  $query = "select area_id from schedule_area where area_id = ".$areaid." ;";
  $res = db_query($query);
  if ( $res && db_numrows($res) === 0) {
    $query = "insert into schedule_area ( schedule_id, area_id ) values (1,".$areaid.");";
    $res = db_query($query);
  }
  //update the node
  $query="update site_nodes  sn set ".
         "sn.area_id = ".$areaid.",".
         "sn.user_name = '".$_POST['location']."',".
         "sn.zigbee_report_1 = ".$_POST['zigb_rep1'].",".
         "sn.zigbee_report_2 = ".$_POST['zigb_rep2'].",".
         "sn.modify_date = NOW() ".
         "where sn. site_id = ".$siteid." AND sn.id = ".$_POST['site_node_id']." ;";
  $res = db_query($query);
	
  //update the points and make sure each node point is in the schedule
  $svals='';
  $arr = $_POST['pnt'];
  $cnt = count($_POST['pnt']['id']);
  for ($i=0;  $i < $cnt; $i++) {
    $query="update site_node_points sp set ".
         "sp.user_name = '".    $arr['what'][$i]."',".
         "sp.units = '".        $arr['units'][$i]."',".
         "sp.zigbee_change = ". $arr['zigbee_change'][$i].",".
         "sp.openedlabel = '".  $arr['openedlabel'][$i]."',".
         "sp.closedlabel = '".  $arr['closedlabel'][$i]."',".
         "sp.reversestate = ".  $arr['reversestate'][$i].",".
         "sp.modify_date = NOW() ".
         "where sp.site_id = ".$siteid." AND sp.id = ".$arr['id'][$i]." ;";
  	$res = db_query($query);
//    $query = "select point_id from schedule_point where point_id = ".$arr['id'][$i]." ;";
//    if ($res = db_query($query) && db_numrows($res) == 0) {
//      $svals.= '(1,'.$arr['id'][$i].'),';
//    }
//  }
//  if (strlen($svals) > 0) {
//    $svals=substr($svals,0,-1);
//    $query='INSERT INTO schedule_point (schedule_id, point_id) values ('.$svals.');';
  //  this bit is disabled because I dont know whether we need it
  //  echo($query);

  }
  //execute the ssreset in background - we assume that if we got this far then
  //qvmanager is running
  bgexec(QVMC.'ssreset');
  header('location: index.php?page=toolbox&id=1&macid='.$macid.'&ok=1');
  return;
}
/* end - update code */

$step2_color     = 'images/incomplete.png';
$step3_color     = 'images/incomplete.png';
if ($macid != '') {
	$step1_color = 'images/completed.png';
	$step1_discover  = 'disabled';
	$step1_confirm  = '';

	$query="select distinct sp.id, sp.site_id, sp.site_node_id, sp.user_name as what, sp.user_short_name, sp.units, npt.name as nodetype, sp.zigbee_change, sp.openedlabel,sp.closedlabel, sn.zigbee_address, sn.zigbee_report_1, sn.zigbee_report_2, a.name as area, IFNULL(sp.modify_date,'null') as modify_date, sp.reversestate 
	from site_node_points sp
	inner join site_nodes sn on sp.site_node_id = sn.id
	inner join areas a on sn.area_id = a.id
	inner join joined_nodes jn on  sn.zigbee_address = jn.address
	inner join node_points np on sp.node_point_id = np.id
	inner join node_point_types npt on np.node_point_type_id =  npt.id
	where sn.zigbee_address = '".$macid. "' and sn.site_id = $siteid and np.node_point_type_id != 17;";
	$res = db_query($query);
	$pnt = array();
	$i=0;
	while ($res && $row=db_fetch($res)) {
		$pnt[$i++] = $row;
	}
	if (count($pnt) > 0) {
		// get the node info
		$query="select distinct sn.id, sn.user_name as location, sn.user_short_name, sn.zigbee_address, sn.zigbee_is_responsive, sn.zigbee_id, sn.zigbee_version, sn.zigbee_report_1, sn.zigbee_report_2, sn.last_heard_from , sn.area_id, a.name as area, IFNULL(sn.modify_date,'null') as modify_date  from site_nodes sn inner join areas a on sn.area_id = a.id inner join joined_nodes jn on  sn.zigbee_address = jn.address where sn.id = ".
 			$pnt[0]['site_node_id'].';';
		$res = db_query($query);
		if (!$node = db_fetch($res)) {
			$macid='';
		}
	}
	else {
		$macid = '';
	}
}
if ($macid == '') {
//	echo __LINE__." line<br />\n";
	$step1_color     = 'images/incomplete.png';
	$step1_discover  = '';
	$step1_confirm  = 'disabled';
//echo 'confirm';print_r($step1_confirm);echo "<br />";
	$step2_color     = 'images/incomplete.png';
	$step3_color     = 'images/incomplete.png';
}
else {
	if ($node['modify_date'] != 'null') {
		$step2_color     = 'images/completed.png';
	}
	$p_a='';
	if (isset($_REQUEST['a'])) $p_a = $_REQUEST['a'];
	$p_l='';
	if (isset($_REQUEST['l'])) $p_l = $_REQUEST['l'];
	$p_z1='';
	if (isset($_REQUEST['z1'])) $p_z1 = $_REQUEST['z1'];
	$p_z2='';
	if (isset($_REQUEST['z2'])) $p_z1 = $_REQUEST['z2'];
}

// areas

$res = db_query("select * from areas where site_id = $siteid  order by name;");
$areas = array();
$area_names = array();
$i=0;
while ($res && $row=db_fetch($res)) {
	$area_names[$i] = $row['name'];
	$areas[$i++] = $row;
}
if (isset($_REQUEST['ok'])) {
	$step1_color = 'images/completed.png';
	$step2_color = 'images/completed.png';
	$step3_color = 'images/completed.png';
}
// make a list of locations
$res=db_query("select distinct user_name from site_nodes where site_id = $siteid order by user_name;");
$i=0;
$locns=array();
while ($res && $row=db_fetch($res, DB_NUM)) {
	$locns[$i++] = $row[0];
}
if (!isset($node)) $node=null;
?>
<a name="step1"></a>
<div class="step step1">
<div class="left"><h1>Join Node</h1><span><img src="<?= $step1_color; ?>" /></span></div>

<div class="right">

<p>
With your monitor node close by, allow the discovery to start. 
</p>
<p>
Press Node button 3 times in quick succession.
</p>
<p>
The node light will start to blink. After about 30 seconds a 10 digit number should appear below. This should be the same 10 digit number that is on the back of the node.
</p>
<div style="margin-left:3em;"><a href=""><input type="button" <?= $step1_discover; ?>  name="discover" id="discover" value="Allow Node Discovery" /></a><span id="discover_cnt" style="margin-left:3ex;"></span></div>

<br />
<h4>Joined Nodes:</h4>
<div class="nodelist" >
<?= $macid; ?>
</div> <!-- nodelist -->
<a class="textlink" href="">Can't find any nodes?</a>
<div style="margin:-1.5em 5ex 2em 67%;">
<a href="#"><input style="float:left;"   type="button"<?= $step1_confirm;?> name="confirm" id="confirm" value="OK" /></a>
<a href="index.php?page=toolbox&amp;id=1">
<input style="float:right;"  type="button" name="cancel" id="cancel" value="Cancel" /></a></div>
</div> <!-- "right" -->
</div> <!-- step 1 -->


<br />
<a name="step2"></a>
<div class="step step2">
<div class="left"><h1>Add Node details</h1><span><img src="<?= $step2_color; ?>" /></span></div>

<div class="right">
<form method="post" action="index.php?page=toolbox&amp;id=1" id="updnode">
<input type="hidden" name="page" id="page" value='toolbox' />
<input type="hidden" name="id"   id="id"   value='1' />
<input type="hidden" name="step"   id="step"   value='3' />
<input type="hidden" name="site_id"      value='<?= $siteid; ?>' />
<input type="hidden" name="site_node_id"      value='<?= nonulls($node,'id'); ?>' />
<input type="hidden" name="area_id"      value='<?= nonulls($node,'area_id'); ?>' />
<input type="hidden" name="area_org"      value='<?= nonulls($node,'area'); ?>' />
<input type="hidden" name="update"      value='update' />
<?php if ($macid != '') {
echo '<input type="hidden" name="macid" id="macid" value="'.$macid.'" />
<br />'; } ?>
<div><label for="sensortype">Sensor Type:</label><span><?php echo nonulls($node,'user_short_name'); ?></span></div>
<br />
<div><label>Address:</label><span><?php echo nonulls($node,'zigbee_address'); ?></span></div>
<br />

<div  style="margin-bottom:1em;height:15px;"><label for="new">Area:</label><span id="area" class="edible"
 data="<?php echo implode(',',$area_names)?>"><?php echo nonulls($node,'area'); ?></span><span class="spinh">
<input type="button" value="&#9650;" data="area"  />
<input type="button" value="&#9660;" data="area" />
</span></div>

<div style="margin-bottom:1em;height:15px;"><label for="new">Location:</label><span id="location" class="edible" data="<?php echo implode(',',$locns)?>"><?php echo nonulls($node,'location'); ?></span><span class="spinh">
<input type="button" value="&#9650;" data="location"  />
<input type="button" value="&#9660;" data="location" />
</span></div>

<input type="hidden" name="area" id="p_area" value="<?= nonulls($node,'area');?>" />
<input type="hidden" name="location" id="p_location" value="<?= nonulls($node,'location');?>'" />

<?php include DIR_INCLUDE.'zigrep.inc' ?>
<div></div>
<br />
<div style="margin:.5em 5ex 2em 67%;"><a href="#"><input style="float:left;" type="button" name="confirm2" id="confirm2" value="OK" /></a><a href="index.php?page=toolbox&amp;id=1"><input style="float:right;"  type="button" name="cancel" id="cancel" value="Cancel" /></a></div>
<div class="clearboth"></div>

</div> <!-- right -->
</div> <!-- step 2 -->

<?php  /*print_r($pnt) ;*/ ?>
<br />
<a name="step3"></a>
<div class="step step3">
<div class="left"><h1>Add Sensor details</h1><span><img src="<?= $step3_color; ?>" /></span></div>
<div class="right">
<?php 
/*
<h2>Monitor Node in <?php echo nonulls($node,'area'); ?></h2>
<h3>Location:&emsp;<?php echo nonulls($node,'location'); ?></h3>
*/
?>
<div class="cols">
<?php 

if (isset($pnt)) { foreach($pnt as $v)  { $i= $v['id'];
$is_input = ($v['nodetype'] == 'Input' ? true : false); ?>
<input type="hidden" name="pnt[id][]"  value="<?= $i;?>" />
<div class="col1"><h1>Sensor:</h1></div><div class="col2"><h1><?=$v['nodetype'];?></h1></div><div class="col3"><h1></h1></div><div class="clearleft"></div>
<div class="col1">Sensing what:</div><div class="col2"><span id="what<?=$i;?>" class="upd edible"><?=$v['what'];?></span></div><div class="col3"></div><div class="clearleft"></div>
<input type="hidden" id="p_what<?=$i;?>"  name="pnt[what][]"  value="" />
<div class="col1">Units:</div><div class="col2"><span id="units<?=$i;?>" class="upd edible"><?=$v['units'];?></div></span><div class="col3"></div><div class="clearleft"></div>
<input type="hidden" id="p_units<?=$i;?>" name="pnt[units][]"  value="" />
<div class="col1">Minimum change to report:</div><div class="col2"><span id="zigb_change<?=$i;?>" class="upd edible"><?=$v['zigbee_change'];?></span></div><div class="col3"></div><div class="clearleft"></div>
<input type="hidden" id="p_zigb_change<?=$i;?>" name="pnt[zigbee_change][]"  value="" />
<div class="col1">Report in different area:</div><div class="col2"><?=$v['dunno'];?></div><div class="col3"></div><div class="clearleft"></div>
<?php if ($is_input) { ?>
<div style="position:relative;left:0;top:0;"><div><?php include DIR_INCLUDE.'configure_sensor.inc'; ?>
<?php } ?>
<input type="hidden" name="pnt[openedlabel][]"  value="<?= $v['openedlabel'];?>" />
<input type="hidden" name="pnt[closedlabel][]"  value="<?= $v['closedlabel'];?>" />
<input type="hidden" name="pnt[reversestate][]"  value="<?=$v['reversestate']; ?>" />
<?php if ($is_input) { ?>
<div class="col1">Describe sensor states:</div>
<div class="col2"><?=$v['openedlabel'];?></div><div class="col3"><?=$v['closedlabel'];?></div><div style="display: inline-block; float:right;margin-right:1ex;"><a href=""><img class="config_sensor" data="<?$i;?>" src="images/configure.png" /></a></div>
</div></div><div class="clearleft"></div>
<?php } /* is_input */ ?>
<?php   } /* for */ } /* if */ ?>
</div> <!-- cols -->

<div style="display:none;"><input  type="submit" name="finish" id="finish" value="Finish"  /></div>
</form>

<div style="margin:.5em 5ex 2em 67%;"><a href="#"><input style="float:left;" type="button" name="confirm3" id="confirm3" value="Finish" disabled /></a><a href="index.php?page=toolbox&amp;id=1"><input style="float:right;"  type="button" name="cancel" id="cancel" value="Cancel" /></a></div>
<br />
<br />
<br />
<div style="margin:.5em 5ex 2em 62%;visibility:hiddenorvisible;"><input style="float:right;" type="submit" name="advancednode" id="advancednode" value="Advanced Info" /></div>


</div> <!-- right -->
</div> <!-- step 3 -->

</div> <!-- toolbox -->
