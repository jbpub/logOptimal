<!-- variable menu -->
<div class="navigation" id="navigation">
<a href="index.php?page=share&amp;id=0" tabindex="-1"><div class="menubtn<?php echo (add_selectn($id,0)); ?>">Messages</div></a>
<a href="index.php?page=share&amp;id=1" tabindex="-1"><div class="menubtn<?php echo (add_selectn($id,1)); ?>">Reminders</div></a>
<a href="index.php?page=share&amp;id=2" tabindex="-1"><div class="menubtn<?php echo (add_selectn($id,2)); ?>">Photos</div></a>
</div> <!-- navigation -->

<div class="clearnone"></div>
<div class="content" id="content">

<div style="margin-top:9px;"></div>

<?php
$sub_array = array(
	'share_message.php'  ,
	'share_reminder.php' ,
	'share_photo.php'    ,
	);

include DIR_INCLUDE.$sub_array[$id];
?>


</div> <!-- content -->
