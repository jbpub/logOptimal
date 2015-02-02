<?php
if ($id <= 0 ) {
	$id=3;
}
if ($id > 5 ) {
	$id=3;
}
?>
<!-- variable menu -->
<div class="navigation" id="navigation">
<a href="index.php?page=askmimo&id=1"><div class="menubtn<?php echo (add_selectn($id,1)); ?>" >Rolling View</div></a>
<a href="index.php?page=askmimo&id=2"><div class="menubtn<?php echo (add_selectn($id,2)); ?>" >Actuators</div></a>
<a href="index.php?page=askmimo&id=3"><div class="menubtn<?php echo (add_selectn($id,3)); ?>" >Monitors</div></a>
<a href="index.php?page=askmimo&id=4"><div class="menubtn<?php echo (add_selectn($id,4)); ?>" >History</div></a>
<a href="index.php?page=askmimo&id=5"><div class="menubtn<?php echo (add_selectn($id,5)); ?>" >Images</div></a>
</div> <!-- navigation -->

<div class="clearnone"></div>
<div class="content" id="content" style="min-height:42em;">

<?php
{
  $sub_array = array(
  	'',
	'askmimo_rollingview.php'  ,
	'askmimo_actuators.php'  ,
	'askmimo_monitors.php'  ,
	'askmimo_history.php'  ,
	'askmimo_images.php'  ,
	);

  include DIR_INCLUDE.$sub_array[$id];
}
?>

</div> <!-- content -->
