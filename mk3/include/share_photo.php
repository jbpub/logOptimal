<?php

$dnm =dirname(__DIR__).'/photos/_TB_*.jpg';
$flist =glob($dnm);
$dlen=strlen($dnm)-9;
for ($i=0; $i < sizeof($flist); $i++) {
  $flist[$i]= substr($flist[$i],$dlen);
}
if (isset($_REQUEST['cix'])) {
	$cix =intval($_REQUEST['cix']);
}
else {
	$cix=0;
}
$cixnext=min(count($flist)-7,$cix+7);
$cixnext=max(0,$cixnext);
$cixprev=max(0,$cix-7);
isset($_REQUEST['curimg']) and $curimg=$_REQUEST['curimg'];
?>
<div class="share">
<div class="clearnone"></div>
<div class="heading"><span class="title">Photographs</span></div>

<div class="subheading">New photo</div>

<div  class="photoupload"> <!-- block -->

<div class="centre" style="max-width: 80%;margin-top:1em;">
<div id="file-uploader" >
    <noscript>
        <p>Please enable JavaScript to use file uploader.</p>
        <!-- or put a simple form for upload here -->
    </noscript>
</div>


<div id="file-uploader-msgholder" >
<div id="file-uploader-msg" ></div>
</div>
</div>
</div> <!-- block -->
<a name="imgtop"></a>
<hr />
<div class="photo">
<div class="thumbholder">
<div class="lbtn photoscroll" ><a href="index.php?page=share&amp;id=2&amp;cix=<?=$cixprev;?>"><img src="images/photo_prev.png" /></a></div>
<div class="rbtn photoscroll" ><a href="index.php?page=share&amp;id=2&amp;cix=<?=$cixnext;?>"><img src="images/photo_next.png" /></a></div>
<div class="thumbcentre">
<div style="max-height:68px;min-height:68px;">
<?php for ($i=$cix; $i < $cix+7; $i++) {
$cls='thumb';
$s=preg_replace('/_TB_([^_]+)_(.+\.)jpg$/','$2$1',$flist[$i]);
(isset($curimg) and $s == $curimg) and $cls.=' selected';
echo '<div class="'.$cls.'"><img src="photos/'.$flist[$i].'" /></div>';
} ?>
</div>
<div>
<?php for ($i=$cix; $i < $cix+7; $i++) { 
//$s = preg_replace('/.+_TB_\w+_/','',$flist[$i]);
$s = preg_replace('/_TB_\w+_/','',$flist[$i]);
$s = str_replace('.jpg','',$s);
?>
<div class="text"><?=$s;?></div>
<?php } ?>
</div>
</div> <!-- thumbcentre -->
<div class="clearnone"></div>
</div> <!-- thumbholder -->
<div class="imageholder">
<!-- <a href="#" id="xbtn"><img src="images/close.png" alt="Close" title="Delete this image"></a> -->
<div id="photo_image" class="image">
<?php
echo '<a href="#"><img src="photos/';
if(isset($curimg)) echo $curimg;
echo '" alt="missing image" /></a>';
?>
</div> <!-- image -->
</div> <!-- imageholder -->
</div> <!-- photo -->


<?php 
if (isset($errmsg) && $errmsg != '') {
echo '<div class="errormsg">error:&nbsp;&nbsp;'.$errmsg.'</div>';
}
?>
<div class="clearnone"></div>

<div style="padding:1ex 0 0 0;"></div>
<div style="margin:1em 0 0 0;"></div>
</div> <!-- share -- >
<!--
<div class="rem_btnblock_wrapper">
<div class="rem_btnblock">
<a href=""><div>Wednesday</div></a>
<a href=""><div style="width: 288px;margin-left:40px;">&nbsp;</div></a>
<a href=""><div style="width: 146px;margin-left:44px;">Saturday</div></a>
</div>
-->
