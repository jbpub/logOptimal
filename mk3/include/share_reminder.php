<?php
/*
session_start();
require_once 'config.php';
require_once 'db.inc';
require_once 'global.inc';
    $_SESSION['user']= sql_logon('mark','c0untess');
    $_SESSION['user']['logon_time']=time();
    $_SESSION['user']['time']=time();
select_site();
*/

/**
  almost constants for preferences
**/
$rem_interval=$siteprefs['rmndr_interval'];
$rem_start_hr=$siteprefs['rmndr_start_hr'];
$rem_end_hr=$siteprefs['rmndr_end_hr'];

$weekly=false;
if (!isset($_REQUEST['daily'])) {
	if (isset($_REQUEST['weekly'])) $weekly=true; else $weekly=false;
	if (isset($_REQUEST['weekday'])) $local_weekday=$_REQUEST['weekday'];
echo "<!-- ";
print_r($_REQUEST);
print_r($_POST);
echo "-->\r\n"; 
}
if (isset($_REQUEST['the_date'])) {
	/* process the date input */
	$the_date=$_REQUEST['the_date'];
	$action='';
	isset($_REQUEST['action']) and $action=$_REQUEST['action'];
	switch ($action) {
	case '-1':
		$the_date = db_fetch(db_query("SELECT DATE_SUB('$the_date', INTERVAL 1 DAY);"),DB_NUM)[0];
		break;
	case '1':
		$the_date = db_fetch(mysql_query("SELECT DATE_ADD('$the_date', INTERVAL 1 DAY);", $sql),MYSQL_NUM)[0];
		break;
	case '-2':
		$the_date = db_fetch(mysql_query("SELECT DATE_SUB('$the_date', INTERVAL 1 MONTH);", $sql),MYSQL_NUM)[0];
		break;
	case '2':
		$the_date = db_fetch(mysql_query("SELECT DATE_ADD('$the_date', INTERVAL 1 MONTH);", $sql),MYSQL_NUM)[0];
		break;
	case '-3':
		$local_weekday=$_REQUEST['weekday'];
		$local_weekday -= 1;
		if ($local_weekday < 1) $local_weekday = 7;
		if ($local_weekday > 7) $local_weekday = 1;
		break;
	case '3':
		$local_weekday += 1;
		if ($local_weekday < 1) $local_weekday = 7;
		if ($local_weekday > 7) $local_weekday = 1;
		break;
	}
	/* save the reminder information */
	if (isset($_POST['save'])  && isset($_POST['reminder'])) {
		if (!$weekly) {
			foreach ($_POST["reminder"] as $k => $v) {
				$vc = db_escstr(str_replace("&quot;", '"' , $v ));
				if ($vc == '--deleted--') {
					mysql_query(
					"DELETE from reminder where rmndr_date = DATE('$the_date') ".
							" AND rmndr_time = TIME(STR_TO_DATE('$k','%h:%i%p'));",$sql);
					continue;
				}
				$res=mysql_query(
					"UPDATE reminder set rmndr_desc = '$vc' where rmndr_date = DATE('$the_date') ".
							" AND rmndr_time = TIME(STR_TO_DATE('$k','%h:%i%p'));",$sql);
				if (db_effected_rows()==0) {
					$res = mysql_query("insert into reminder (rmndr_date, rmndr_time, rmndr_desc) values ".
						"( DATE('$the_date'), TIME(STR_TO_DATE('$k','%h:%i%p')), '$vc');",$sql);

				}
			}
		}
		else { /* weekly */
			foreach ($_POST["reminder"] as $k => $v) {
				$vc = db_escstr(str_replace("&quot;", '"' , $v ));
				if ($vc == '--deleted--') {
					mysql_query(
					"DELETE from reminder_weekly where weekday = $local_weekday ".
                            " AND rmndr_time = TIME(STR_TO_DATE('$k','%h:%i%p'));",$sql);
					continue;
				}
				mysql_query(
					"UPDATE reminder_weekly set rmndr_desc = '$vc' where weekday = $local_weekday ".
							" AND rmndr_time = TIME(STR_TO_DATE('$k','%h:%i%p'));",$sql);
				if (mysql_affected_rows($sql)==0) {
					mysql_query(
					"insert into reminder_weekly (weekday, rmndr_time, rmndr_desc) values ".
						"( $local_weekday, TIME(STR_TO_DATE('$k','%h:%i%p')), '$vc');",$sql);

				}
			}
		}
		header('Location: ./index.php?page=share&id=1&the_date='.$the_date. ($weekly? '&weekly=1&weekday='.$local_weekday: ''));
	}
	/* delete  the reminder information */
	elseif (isset($_POST['clear'])) {
		if (!$weekly) {
			mysql_query(
			"DELETE from reminder where rmndr_date = DATE('$the_date'); ", $sql);
		}
		else {
			mysql_query(
				"DELETE from reminder_weekly where weekday = $local_weekday;", $sql);
		}
		header('Location: ./index.php?page=share&id=1&the_date='.$the_date. ($weekly? '&weekly=1&weekday='.$local_weekday: ''));
	}
}
else {
	$the_date = db_fetch(mysql_query("SELECT DATE(CURDATE());", $sql),MYSQL_NUM)[0];
}


$res =  db_fetch(mysql_query(
  "SELECT 
		DATE_FORMAT('".$the_date."', '%W, %M %D, %Y'), 
		DATE_FORMAT('".$the_date."', '%M'), 
		DATE_FORMAT('".$the_date."', '%D, %Y'), 
		DATE_FORMAT('".$the_date."', '%W'), 
		DAYOFWEEK('".$the_date."');", 
			$sql),MYSQL_NUM);

$local_date_str =  $res[0];
$local_mthname  =  $res[1];
$local_dy       =  $res[2];
if (!$weekly) {
	$local_dayname  =  $res[3];
	$local_weekday  =  $res[4];
}
else {
	$a  =  array(1 => 'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	$local_dayname = $a[$local_weekday];
}


$wrows = array();
$qry="select id,  LOWER(DATE_FORMAT(rmndr_time,'%l:%i%p')) as time, rmndr_desc as txt from reminder_weekly where weekday = ". $local_weekday . " order by rmndr_time;";
$res=mysql_query($qry,$sql);
$i = 0;
if ($res) while($row   = db_fetch($res,MYSQL_ASSOC)) {
	$wrows[$i++] = $row;
};

$drows = array();
if (!$weekly) {
	$qry="select id,  LOWER(DATE_FORMAT(rmndr_time,'%l:%i%p')) as time, rmndr_desc as txt from reminder where rmndr_date = '". $the_date . "' order by rmndr_time;";
	$res=mysql_query($qry,$sql);
	$i = 0;
	if ($res) while($row   = db_fetch($res,MYSQL_ASSOC)) {
		$drows[$i++] = $row;
	};
}

function fnd_slot($arr, $t)
{
	for ($i=0; $i < sizeof($arr); ++$i) {
		if ($arr[$i]['time'] == $t) return $i;
	}
	return -1;
}

function fill_data($i,$j, $drows, $wrows)
{ 
	global $weekly;

	$fmt1 = '<a href="#"><div class="rem_line"><span class="rem_time">%s</span><span class="rem_text">%s</span></div></a>'."\r\n";
	if ($i < 12 ) $sfx = 'am'; else $sfx = 'pm';
	if ($i > 12) $i %= 12;
	$t = sprintf('%d:%02d%s',$i,$j,$sfx);
	$esctxt='';
	if ($weekly) $k=-1; else $k = fnd_slot($drows, $t);
    if ($k!=-1) {
		$esctxt =  htmlspecialchars($drows[$k]['txt']);
	}
    else {
		$k = fnd_slot($wrows, $t);
    	if ($k!=-1) {
			$esctxt =  htmlspecialchars($wrows[$k]['txt']);
		}
	}
	printf($fmt1, $t, $esctxt);
}

/*
SELECT DATE_FORMAT('1970-01-01 21:30:00','%h:%i%p');
SELECT STR_TO_DATE('09:30am','%h:%i%p');
SELECT DATE_FORMAT('1953-11-7 21:30:00.0001','%W, %M %D, %Y');
$rem_interval can be 15,30,60
*/

?>
<!-- start of reminders -->
<div class="clearnone"></div>
<a name="1"></a>
<div class="rem_heading"><span class="rem_title">Reminders</span>

<span class="rem_date">
<?php


if (!$weekly) {
echo '
<a href="index.php?page=share&amp;id=1&amp;action=-1&amp;the_date='.$the_date.'#h1"><img src="images/left_small.png" /></a>'.$local_dayname.'<a href="index.php?page=share&amp;id=1&amp;action=1&amp;the_date='.$the_date.'#h1" ><img src="images/right_small.png" /></a>
<a href="index.php?page=share&amp;id=1&amp;action=-2&amp;the_date='.$the_date.'#1" ><img src="images/left_small.png" /></a>'.$local_mthname.'<a href="index.php?page=share&amp;id=1&amp;action=2&amp;the_date='.$the_date.'#1" ><img src="images/right_small.png" /></a>&nbsp;'.
$local_dy.'</span></div>';
}
else { /* weekly */
echo '
<a href="index.php?page=share&amp;id=1&amp;action=-3&amp;the_date='.$the_date.'&amp;weekly=1&amp;weekday='.$local_weekday.'"><img src="images/left_small.png" /></a>'.$local_dayname.'<a href="index.php?page=share&amp;id=1&amp;action=3&amp;the_date='.$the_date.'&amp;weekly=1&amp;weekday='.$local_weekday.'" ><img src="images/right_small.png" /></a></span></div>';
}
?>
<form method="post" action="index.php">
<input type="hidden" name="page" id="page" value='share' />
<input type="hidden" name="id"   id="id"   value='1' />
<?php if (!$weekly) { ?>
<div class="rem_subheading">Plans for Today
<div style="float:right;margin-right:3ex;"><input type="submit" name="clear" id="clear" value="Clear" title="Clear all entries for this page" /><input type="submit" name="weekly" id="weekly" value="Weekly" title="Enter recurrent weekly reminders" /><input type="submit" name="save" id="save" value="Save" /></div>
<?php } else { ?>
<div class="rem_subheading">Plans for each week
<div  style="float:right;margin-right:3ex;"><input type="hidden" name="weekly" id="weekly" value="1"><input type="submit" name="clear" id="clear" value="Clear" title="Clear all entries for this page" /><input type="submit" name="daily" id="daily" value="Daily" title="Enter daily reminders" /><input type="submit" name="save" id="save" value="Save" /></div>
<?php } ?>
<div class="clearnone"></div>
</div>
<input type="hidden" name="the_date" id="the_date" value="<?= $the_date;?>" />
<input type="hidden" name="weekday" id="weekday" value="<?= $local_weekday;?>" />
<div class="rem_list" style="min-height:24em;max-height:34em;overflow-y:auto;">
<?php
for ($i=$rem_start_hr; $i<=$rem_end_hr; ++$i) {
  for ($j=0; $j < 60; $j+=$rem_interval ) {
	fill_data($i,$j, $drows, $wrows);
	if  ($i == $rem_end_hr) break;
  }
}
?>
</div>
</form>

<!--
<div class="rem_btnblock_wrapper">
<div class="rem_btnblock">
<a href=""><div>Wednesday</div></a>
<a href=""><div style="width: 288px;margin-left:40px;">&nbsp;</div></a>
<a href=""><div style="width: 146px;margin-left:44px;">Saturday</div></a>
</div>
-->
</div>

