<div class="idesc">Do NOT send if LESS than
<?php  $sec_values = array(30, 60, 120, 300, 600, 3000);
//  seconds  john would like a default of 60 and 300 
$zig_rep1 =  nonulls($node,'zigbee_report_1');
$zig_rep2 =  nonulls($node,'zigbee_report_2');
if (!in_array($zig_rep1,$sec_values)) $zig_rep1=60; 
if (!in_array($zig_rep2,$sec_values)) $zig_rep2=300; 
echo '<input type="hidden" name="zigb_rep1" id="p_zigb_rep1" value="'.$zig_rep1.'" />';
echo '<span id="zigb_rep1d" style="font-weight:normal;text-align:right;width:4ex;display:inline-block;" data="'.implode(',',$sec_values).'">'.$zig_rep1.'</span>';
?>
<span class="spinh">
<input type="button" value="&#9650;" data="zigb_rep1d,p_zigb_rep1"  />
<input type="button" value="&#9660;" data="zigb_rep1d,p_zigb_rep1" />
</span> seconds since last send.</div>
<div class="idesc">Always send if MORE than
<?php
echo '<span id="zigb_rep2d" style="font-weight:normal;text-align:right;width:4ex;display:inline-block;" data="'.implode(',',$sec_values).'">'.$zig_rep2.'</span>';
echo '<input type="hidden" name="zigb_rep2" id="p_zigb_rep2" value="'.$zig_rep2.'" />';
?>
<span class="spinh">
<input type="button" value="&#9650;" data="zigb_rep2d,p_zigb_rep2"  />
<input type="button" value="&#9660;" data="zigb_rep2d,p_zigb_rep2" />
</span>
 seconds has passed.</div>

