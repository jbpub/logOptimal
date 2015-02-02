<!-- variable menu -->
<div class="navigation" id="navigation">
<a href="index.php?page=toolbox&amp;id=1"><div class="menubtn menubtn_x<?php echo (add_selectn($id,1)); ?>" title="Add a monitor node. If you have a new node with monitors you want to setup then start here."><br />Set up a new monitor point</div></a>
<a href="index.php?page=toolbox&amp;id=2"><div class="menubtn menubtn_x<?php echo (add_selectn($id,2)); ?>" title="This allows you to edit any details for your nodes or monitors. Usually names, areas and reporting frequency."><br />Check out a monitor</div></a>
<a href="index.php?page=toolbox&amp;id=3"><div class="menubtn menubtn_x<?php echo (add_selectn($id,3)); ?>" title="See how a profile looks. Profiles use the last 100 days to create a behaviour pattern. This helps iCare look for any changes of routine."><br />Look at profile</div></a>
<a href="index.php?page=toolbox&amp;id=4"><div class="menubtn menubtn_x<?php echo (add_selectn($id,4)); ?>" title="Manage times of day and year to activate certain monitor points."><br />Look after time schedules</div></a>
<a href="index.php?page=toolbox&amp;id=5"><div class="menubtn menubtn_x<?php echo (add_selectn($id,5)); ?>" title="Puts alerts against profiles or individual monitors to create SMS alerts to carers."><br />Manage alerts and thresholds</div></a>
</div> <!-- navigation -->

<div class="clearnone"></div>
<div class="content" id="content" style="min-height:42em;">

<?php
if ($id > 0 ) {
  $sub_array = array(
  	'',
	'toolbox_newnode.php'  ,
	'toolbox_managenode.php'  ,
	'toolbox_profile.php'  ,
	'toolbox_schedule.php'  ,
	'toolbox_alerts.php'  ,
	);

  include DIR_INCLUDE.$sub_array[$id];
}
else {
?>
<div style="padding-top:29px;background:#fffff9;"></div>
<div class="btndescription">Add a monitor node. If you have a new node with monitors you want to setup then start here.</div>
<div class="btndescription">This allows you to edit any details for your nodes or monitors. Usually names, areas and reporting frequency.</div>
<div class="btndescription">See how a profile looks. Profiles use the last 100 days to create a behaviour pattern. This helps iCare look for any changes of routine.</div>
<div class="btndescription">Manage times of day and year to activate certain monitor points.</div>
<div class="btndescription">Puts alerts against profiles or individual monitors to create SMS alerts to carers.</div>
<?php
}
?>

</div> <!-- content -->
