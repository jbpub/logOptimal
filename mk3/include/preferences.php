<?php

if (isset($_REQUEST['part'])) switch ($_REQUEST['part']) {

case 'reminder':
	if  (isset($_REQUEST['action']) && $_REQUEST['action'] == '1') {
		$siteprefs=array( 
			'rmndr_interval' => $_REQUEST['rem_interval'],
			'rmndr_start_hr' => $_REQUEST['rem_start_hr'],
			'rmndr_end_hr'   => $_REQUEST['rem_end_hr'],
		);
		sql_siteprefs($siteprefs);
		$siteprefs= sql_siteprefs();
	}
	break;

case 'general':
	if  (isset($_REQUEST['action']) && $_REQUEST['action'] == '1') {
		$siteprefs=array( 
			'session_timeout' => $_REQUEST['session_timeout'],
		);
		sql_siteprefs($siteprefs);
		$siteprefs= sql_siteprefs();
   		$_SESSION['user']['session_timeout']= intval($_REQUEST['session_timeout']);
	}
	break;
	break;

}
$rem_interval=$siteprefs['rmndr_interval'];
$rem_start_hr=$siteprefs['rmndr_start_hr'];
$rem_end_hr=$siteprefs['rmndr_end_hr'];
$session_timeout=$siteprefs['session_timeout'];

?>
<div class="content" id="content" style="margin: 1ex auto 1ex auto;">

<div style="margin-top:9px;"></div>

<div class="clearnone"></div>

<div class="preferences">

<form method="post" action="index.php" name="reminders" >
<fieldset>
<legend>Reminders</legend>
<input type="hidden" name="page" id="page" value='preferences' />
<input type="hidden" name="id"   id="id"   value='0' />
<input type="hidden" name="part"   id="part"   value='reminder' />
<input type="hidden" name="action"   id="action"   value='1' />
<div><label for="prefs_rem_interval">Reminder Interval:</label>
<INPUT TYPE="radio" NAME="rem_interval" id="prefs_rem_interval" VALUE="15" <?php echo ($rem_interval==15?'CHECKED':''); ?>>15 Minutes</INPUT> 
<INPUT TYPE="radio" NAME="rem_interval" id="prefs_rem_interval" VALUE="30" <?php echo ($rem_interval==30?'CHECKED':''); ?>>30 Minutes</INPUT> 
<INPUT TYPE="radio" NAME="rem_interval" id="prefs_rem_interval" VALUE="60" <?php echo ($rem_interval==60?'CHECKED':''); ?>>1 Hour</INPUT>
</div>
<div><label for="rem_start_hr">Starting hour:</label><select name="rem_start_hr" id="rem_start_hr">
<?php for ($i=5; $i < 12; ++$i) {
echo "<option";
if ($i == $rem_start_hr) echo " SELECTED";
echo ">$i</option>\r\n";
} ?>
</select>
</div>
<div>
<label for="rem_end_hr">Ending hour:</label><select name="rem_end_hr" id="rem_end_hr">
<?php for ($i=15; $i < 23; ++$i) {
echo '<option value="'.$i.'"';
if ($i == $rem_end_hr) echo " SELECTED";
echo '>';
echo $i-intval(12) ;
echo "</option>\r\n";
} ?>
</select>
</div>
<div><input type="submit" name="reminder" id="reminder" value="Update Reminders" title="Update reminders" />
</div>
</fieldset>
</form>


<form method="post" action="index.php" name="general" >
<fieldset>
<legend>Time out</legend>
<input type="hidden" name="page" id="page" value='preferences' />
<input type="hidden" name="id"   id="id"   value='0' />
<input type="hidden" name="part"   id="part"   value='general' />
<input type="hidden" name="action"   id="action"   value='1' />
<div><label for="session_timeout">Session time out:</label><select name="session_timeout" id="session_timeout">
<?php for ($i=300; $i <= 7200 ; $i+=300) {
echo '<option value="'.$i.'"';
if ($i  == $session_timeout) echo " SELECTED";
echo '>';
echo $i/60;
echo '&nbsp;minutes';
echo "</option>\r\n";
} ?>
</select>
<//div>
<div><input type="submit" name="timeout" id="timeout" value="Update time out" title="Update time out" />
</div>
</fieldset>
</form>



</div> <!-- preferences -->

</div> <!-- content -->

