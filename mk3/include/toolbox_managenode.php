<?php
/* vim: set ai sw=4 ts=4: */
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
<p>Check out a monitor. This allows you to edit any details for your nodes or monitors. Usually names, areas and reporting frequency.
</p>
</div > <!-- preamble -->

<div class="toolbox managenode" style="min-height:32em;">
<?php
$siteid=$_SESSION['user']['siteid'];
$macid='';
if (isset($_REQUEST['macid'])) $macid = $_REQUEST['macid'];
$area_sel=0;
if (isset($_REQUEST['a'])) $area_sel = intval($_REQUEST['a']);
$node_sel=0;
if (isset($_REQUEST['n'])) $node_sel = intval($_REQUEST['n']);

/*
 *	Add area and add area to a schedule
 */
if (!empty($_REQUEST['ua']) && !empty($_REQUEST['newarea'])) {
  $na=$_REQUEST['newarea'];
  $res = db_query("select distinct name from areas where site_id = $siteid  and name='".db_escstr($na)."';");
  if ($res && db_numrows($res)==0) {
    echo("insert into areas set site_id = $siteid, name='".db_escstr($na)."';");
    $res = db_query("insert into areas set is_active='1', site_id = $siteid, name='".db_escstr($na)."';");
    $areaid = db_insertid();
    if (intval($areaid) <= 0) die ("bad insert");

    $query = "select area_id from schedule_area where area_id = ".$areaid." ;";
    $res = db_query($query);
    if ( $res && db_numrows($res) === 0) {
      $query = "insert into schedule_area ( schedule_id, area_id ) values (1,".$areaid.");";
      $res = db_query($query);
    }
  }
  bgexec(QVMC.'ssreset');
  header('location: index.php?page=toolbox&id=2&ok=1');
  return;
}



// areas
$res = db_query("select * from areas where site_id = $siteid  order by name;");
$areas = array();
$area_names = array();
$i=0;
while ($res && $row=db_fetch($res)) {
	$area_names[$i] = $row['name'];
	$areas[$i++] = $row;
	$area_ids[intval($row['id'])] = $row['name'];
}
// make a list of locations
$res=db_query("select distinct user_name from site_nodes where site_id = $siteid order by user_name;");
$i=0;
$locns=array();
while ($res && $row=db_fetch($res, DB_NUM)) {
	$locns[$i++] = $row[0];
}

?>
<div style="margin:1em 0 .5em 2ex;"><span style="font-weight:bold;font-size:1em;margin-right:1ex;">Select an Area</span><select id="area_list" >
<option<?php if ($area_sel==0) echo ' selected'; ?> value="0">&lt;All&gt;</option>
<?php
foreach ($area_ids as $k => $v ) {
   echo (sprintf('<option %s value="%d">%s</option>'."\r\n", 
	($area_sel == $k ? 'selected' : ''),
        $k, $v));
}
?>
</select>
<a href="index.php?page=toolbox&amp;id=2"><input style="margin-left:9ex;vertical-align:middle;" type="image" name="refresh" id="area_refresh" src="images/refresh.png" value="Refresh"  /></a><span style="padding-left:5ex;"><a href="#" id="do_addarea">Add a new area</a></span>
</div>
<?php

/* start - update code */
if (isset($_REQUEST['update'])) {

//print_r($_REQUEST);
  //add an area if we need to
  if ($_POST['area'] != $_POST['area_org']) {
    $query = "select id from areas where site_id = ".$siteid." and name = '".db_escstr($_POST['area'])."' ;";
    $areaid = intval(db_fetch(db_query($query),DB_NUM)[0]);
    if ($areaid <= 0) {
      $query = "insert into areas ( site_id, name, is_active ) values (".$siteid.", '".db_escstr($_POST['area'])."', '1');";
//	print_r($query);
      $res = db_query($query);
      $areaid = db_insertid();
      if (intval($areaid) <= 0) die ("bad insert");
    }
  } 
  else {
    $areaid = $_POST['area_id'];
  }
  //update the node
  $query="update site_nodes set ".
         "area_id = ".$areaid.",".
         "user_name = '".$_POST['location']."',".
         "is_active = '".$_POST['is_active']."',".
         "zigbee_report_1 = ".$_POST['zigb_rep1'].",".
         "zigbee_report_2 = ".$_POST['zigb_rep2'].",".
         "modify_date = NOW() ".
         "where site_id = ".$siteid." and id = ".$_POST['site_node_id']." ;";
  $res = db_query($query);
	
  //update the points
  $arr = $_POST['pnt'];
  $cnt = count($_POST['pnt']['id']);
  for ($i=0;  $i < $cnt; $i++) {
    if (empty($arr['zigbee_change'][$i])) {$arr['zigbee_change'][$i]=0; }
    $query="update site_node_points set ".
         "user_name = '".    $arr['what'][$i]."',".
         "units = '".        $arr['units'][$i]."',".
         "is_active = '".    $arr['is_active'][$i]."',".
         "zigbee_change = ". $arr['zigbee_change'][$i].",".
         "openedlabel = '".  $arr['openedlabel'][$i]."',".
         "closedlabel = '".  $arr['closedlabel'][$i]."',".
         "reversestate = ".  $arr['reversestate'][$i].",".
         "modify_date = NOW() ".
         "where site_id = ".$siteid." and id = ".$arr['id'][$i]." ;";
//	echo($query);
  	$res = db_query($query);
  }
//todo 
// need to update various schedules and explicity set is_active wherever it may occur
  bgexec(QVMC.'ssreset');
  header('location: index.php?page=toolbox&id=2&a='.$areaid.'&n='.$_POST['site_node_id'].'&ok=1');
  return;
}
/* end - update code */

$where_site = " where sn.site_id = $siteid ";
$sn_flds='sn.id, sn.site_id, sn.area_id, sn.node_id,sn.is_active, '.
'sn.user_name as sn_location, sn.user_short_name as sn_name, '.
'sn. zigbee_address,sn.zigbee_report_1, sn.zigbee_report_2, sn.zigbee_is_responsive, '.
'sn.last_heard_from ';

/*
$query=
'select '
$sp_flds = 'sp.id, sp.site_id, sp.site_node_id, sp.user_name as what, sp.user_short_name,  '.
'sp.units, npt.name as nodetype, sp.zigbee_change, sp.openedlabel,sp.closedlabel, '.
'sp.is_active,sp.last_heard_from, '.
'IFNULL(sp.modify_date,'null') as modify_date, sp.reversestate,  '.
'sp.log_continuous, sp.log_contact_input ';

$sn_flds='sn.id, sn.site_id, sn.area_id, sn.node_id,sn.is_active, '.
'sn.user_name as sn_location, sn.user_short_name as sn_name, '.
'sn. zigbee_address,sn.zigbee_report_1, sn.zigbee_report_2, sn.zigbee_is_responsive, '.
'sn.last_heard_from ';

$a_flds='a.name as a_area, a.id as a_id, a.is_active as a_is_active ';

$npt_flds='npt.id as npt_id, npt.name as npt_name, npt.short_name as npt_abbrev,  '.
'npt.node_point_cat as npt_cat, npt.units as npt_units, '.

'scha.id as scha_id, schp.id as schp_id '.

'from site_node_points sp  '.
'inner join site_nodes sn on sp.site_node_id = sn.id   '.
'inner join areas a on sn.area_id = a.id   '.
'inner join node_points np on sp.node_point_id = np.id  '.
'inner join node_point_types npt on np.node_point_type_id =  npt.id '.
'left  join schedule_area scha on sn.area_id = scha.area_id '.
'left  join schedule_point schp on sp.id = schp.point_id '.
$where_site = " where sn.site_id = $siteid ";
$where_npt = " and np.node_point_type_id != 17 ";
$order = 'order by a.name, sn_location, what, sp.id ;';

$query="select $a_flds , $sn_flds from site_nodes inner join areas a on sn.area_id = a.id "
*/
//the menu
$query=
	"select $sn_flds
			from site_nodes sn ".
			"$where_site ".
			($area_sel != 0 ?  "and sn.area_id = $area_sel ":"").
			" order by sn.area_id, sn.user_name;";

$res = db_query($query);
?>
<div style="margin:1em 0 .5em 2ex;"><span style="font-weight:bold;font-size:1em;margin-right:1ex;">Select a Node</span><select id="node_list" >
<option<?php if ($node_sel==0) echo ' selected'; ?> value="0">&lt;Select ...&gt;</option>
<?php
while ($res && $r=db_fetch($res)) {
   echo (sprintf('<option %s value="%d">%s</option>'."\r\n", 
	($node_sel == $r['id'] ? 'selected' : ''),
        $r['id'], $r['sn_location']));
}
?>
</select>
<a href="index.php?page=toolbox&amp;id=2"><input style="margin-left:9ex;vertical-align:middle;" type="image" name="refresh" id="node_refresh" src="images/refresh.png" value="Refresh"  /></a>
</div>
<?php


if ($node_sel != 0) {
	$query="select distinct sn.id, sn.user_name as location, sn.user_short_name, sn.zigbee_address, sn.is_active, sn.zigbee_is_responsive, sn.zigbee_id, sn.zigbee_version, sn.zigbee_report_1, sn.zigbee_report_2, sn.last_heard_from , sn.area_id, a.name as area, IFNULL(sn.modify_date,'null') as modify_date  
		from site_nodes sn
		inner join areas a on sn.area_id = a.id
		where sn.id = ".$node_sel.';';
	$res = db_query($query);
	$node = db_fetch($res);

	$query="select distinct sp.id, sp.site_id, sp.site_node_id, sp.user_name as what, sp.user_short_name, sp.units, sp.is_active, npt.name as nodetype, sp.zigbee_change, sp.openedlabel,sp.closedlabel, IFNULL(sp.modify_date,'null') as modify_date, sp.reversestate 
		from site_node_points sp 
		inner join node_points np on sp.node_point_id = np.id
		inner join node_point_types npt on np.node_point_type_id =  npt.id
		where sp.site_node_id = $node_sel and np.node_point_type_id != 17;";
	$pnt = array();
	$i=0;
	$res = db_query($query);
	while ($res && $row=db_fetch($res)) {
		$pnt[$i++] = $row;
	}

?>
<div class="step step2"></div>
<a name="step2"></a>
<div class="step step2">
<div class="left"><h1>Node details</h1><span></span></div>

<div class="right">
<form method="post" action="index.php?page=toolbox&amp;id=2" id="updnode">
<input type="hidden" name="page" id="page" value='toolbox' />
<input type="hidden" name="id"   id="id"   value='2' />
<input type="hidden" name="step"   id="step"   value='3' />
<input type="hidden" name="site_id"      value='<?= $siteid; ?>' />
<input type="hidden" name="site_node_id"      value='<?= $node['id']; ?>' />
<input type="hidden" name="area_id"      value='<?= $node['area_id']; ?>' />
<input type="hidden" name="area_org"      value='<?= $node['area']; ?>' />
<input type="hidden" name="a"             value='<?= $node_sel; ?>' />
<input type="hidden" name="update"      value='update' />
<div><br /><label>Sensor Type:</label><span><?php echo nonulls($node,'user_short_name'); ?></span></div>
<div><br /><label>Address:</label><span><?php echo nonulls($node,'zigbee_address'); ?></span></div>
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
<input type="hidden" name="is_active" id="p_is_active" value="<?= nonulls($node,'is_active');?>'" />

<div  style="margin-bottom:1em;height:15px;"><label for="sn_is_active">Enable/Disable:</label><span style="line-height:1em;" id="is_active" class="tickcross <?=($node['is_active']=='1'?'on':'off');?>"></span></div>

<?php include DIR_INCLUDE.'zigrep.inc' ?>
<div></div>
<br />
<div class="clearboth"></div>

</div> <!-- right -->
</div> <!-- step 2 -->

<?php  /*print_r($pnt) ;*/ ?>
<br />
<a name="step3"></a>
<div class="step step3">
<div class="left"><h1>Sensor details</h1><span></span></div>
<div class="right">
<?php
/*
<h2>Monitor Node in <?php echo nonulls($node,'area'); ?></h2>

<h3>Location:&emsp;<?php echo nonulls($node,'location'); ?></h3>
*/
?> 
<br />
<div class="cols">
<?php 

foreach($pnt as $v)  { $i= $v['id'];
$is_input = ($v['nodetype'] == 'Input' ? true : false); ?>
<input type="hidden" name="pnt[id][]"  value="<?= $i;?>" />
<div class="col1" style="font-weight:bold;font-size:1.2em;">Sensor:</div><div class="col2" style="font-weight:bold;font-size:1.2em;"><?=$v['nodetype'];?></div><div class="col3"></div><div class="clearleft"></div>
<div class="col1">Sensing what:</div><div class="col2"><span id="what<?=$i;?>" class="upd edible"><?=$v['what'];?></span></div><div class="col3"></div><div class="clearleft"></div>
<input type="hidden" id="p_what<?=$i;?>"  name="pnt[what][]"  value="" />
<div class="col1">Units:</div><div class="col2"><span id="units<?=$i;?>" class="upd edible"><?=$v['units'];?></div></span><div class="col3"></div><div class="clearleft"></div>
<input type="hidden" id="p_units<?=$i;?>" name="pnt[units][]"  value="" />
<div class="col1">Minimum change to report:</div><div class="col2"><span id="zigb_change<?=$i;?>" class="upd edible"><?=$v['zigbee_change'];?></span></div><div class="col3"></div><div class="clearleft"></div>
<input type="hidden" id="p_zigb_change<?=$i;?>" name="pnt[zigbee_change][]"  value="" />

<input type="hidden" id="p_is_active<?=$i;?>" name="pnt[is_active][]"  value="<?=$v['is_active'];?>" />
<div class="col1">Enable/Disable:</div><div style="line-height:1em;" id="is_active<?=$i;?>" class="col2 upd tickcross <?=($v['is_active']=='1'?'on':'off');?>"></div><div class="col3"></div><div class="clearleft"></div>

<div class="col1">Report in different area:</div><div class="col2"><?=$v['dunno'];?></div><div class="col3"></div><div class="clearleft"></div>
<?php if ($is_input) { ?>
<div style="position:relative;left:0;top:0;"><div><?php include DIR_INCLUDE.'configure_sensor.inc'; ?>
<?php } ?>
<input type="hidden" name="pnt[openedlabel][]"  value="<?= $v['openedlabel'];?>" />
<input type="hidden" name="pnt[closedlabel][]"  value="<?= $v['closedlabel'];?>" />
<input type="hidden" name="pnt[reversestate][]"  value="<?=$v['reversestate']; ?>" />
<?php if ($is_input) { ?>
<div class="col1">Describe sensor states:
</div><div class="col2"><?=$v['openedlabel'];?></div><div class="col3"><?=$v['closedlabel'];?></div><div style="display: inline-block; float:right;margin-right:1ex;"><a href=""><img class="config_sensor" data="<?$i;?>" src="images/configure.png" /></a></div></div></div><div class="clearleft"></div>
<br />
<?php } /* is_input */ ?>
<?php   } /* for */ ?>
</div> <!-- cols -->

<div style="display:none;"><input  type="submit" name="finish" id="finish" value="Finish"  /></div>
</form>

<div style="margin:.5em 5ex 2em 67%;"><a href="#"><input style="float:left;" type="button" name="confirm3" id="managenode_save" value="Save"  /></a><a href="index.php?page=toolbox&amp;id=2"><input style="float:right;"  type="button" name="cancel" id="cancel" value="Cancel" /></a></div>
<?php   } /* if node_sel */ ?>
<br />
<br />
<br />
<div style="margin:.5em 5ex 2em 62%;visibility:hiddenorvisible;"><input style="float:right;" type="submit" name="advancednode" id="advancednode" value="Advanced Info" /></div>


</div> <!-- right -->
</div> <!-- step 3 -->


<script type="text/javascript">
$(function() {
	$('#do_addarea').on('click', function(e) {
		var sm = $('#modal_area').modal(
			{
			   close: true,
			   closeClass: 'close_btn',
			   fixed: false
			}
		);
		return false;
	});
});
</script>
<div id="modal_area" class="area_dialog" style="width:35ex;border: 1px solid #cccccc;display:none;border: 1px solid #999999;border-radius: 8px;z-index:99;background: #f9f9f9;;" >
<div class="close_btn"></div><div class="clearboth" ></div>
<div style="margin:0 auto 0 auto;">
<h3 style="margin:1ex 1ex 1ex 10ex;" >Add an area<br /></h3>
<form method="post" action="index.php?page=toolbox&amp;id=2&amp;ua=1" id="newareadlg">
<div><span style="padding:0 3ex 0 2ex;font-weight:bold;font-size:.9em;">Area Name:</span><span style=""><input type="text" name="newarea" size="20" value="" /></span></div>
<div style="margin:1.5em 2ex 2em 80%;"><input style="float:right;" type="submit" name="ok" id="area_save" value="Save"  /></div>
</form>
<div class="clearboth" ></div>
</div><br /></div>

</div> <!-- toolbox -->
