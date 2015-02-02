<?php
$where='';
$pfrom='';
$pnewmsg='';
$pgsz=15;
$prm = db_fetch(db_query("SELECT  count(*) as count, MIN(id) as minid, MAX(id) as maxid, NOW() as now, DATE(CURDATE()) as today from messages ;"));

$errmsg='';
if (isset($_POST)) {
	$siteid=$_SESSION['user']['siteid'];
	if (isset($_POST['send'])) {
		$where = '';
		$pfrom=$_POST['from'];
		$pnewmsg=$_POST['newmsg'];
		if (strlen($_POST['from']) < 1) {$errmsg.="Enter a sender.  ";}
		if (strlen($_POST['newmsg']) < 1) {$errmsg.="Enter a message.";}
		if ($errmsg == '') {
			$query = sprintf("INSERT INTO messages ( msg_sender, msg_type, msg_status, msg_time, msg_text ) values ('%s', '%s', '%s', NOW(), '%s' )", 
		  db_escstr($_POST['from']),$_POST['type'],'0',db_escstr($_POST['newmsg']));
			$res = db_query($query);
			if (!$res) $errmsg = db_error();
			else {
				unset($_POST);
				$pfrom='';
				$pnewmsg='';
			}
		}
	}
	elseif (isset($_POST['prev'])) {
		$i = intval($_POST['start']);
		$j = $i + $pgsz;
		if ($j > $prm['maxid']) $where = '';
		else {
			$where =" where id > $i AND id <= $j";
		}
	}
	elseif (isset($_POST['next'])) {
		$i = intval($_POST['end']);
		if ($i <= $prm['minid'] ) $i= intval($_POST['start']);
		$where = " where id <= $i ";
	}
}
$res = db_query(
	"SELECT  *, DATE(msg_time) as msg_date, DATE_FORMAT(msg_time,'%W, %M %D, %Y') as fmt_date, TIME_FORMAT(msg_time,'%l:%i%p') as time, DATE_FORMAT(msg_time,'%W') as dayname from messages $where order by id desc limit $pgsz ;");
?>
<div class="share">
<div class="clearnone"></div>
<div class="heading"><span class="title">Messages</span></div>

<form method="post" action="index.php" name="msg" id=msg" >
<div class="subheading">New message</div>

<div style="margin:1ex 0 1ex 0"> <!-- block -->
<input type="hidden" name="page" id="page" value='share' />
<input type="hidden" name="id"   id="id"   value='0' />
<input type="hidden" name="start"   id="start"   value='0' />
<input type="hidden" name="end"     id="end"     value='0' />
<div class="entry"><label for="from">From:</label><INPUT TYPE="text" NAME="from" id="from" size="30" VALUE="<?php echo $pfrom; ?>" accesskey="0" tabindex="1" ></INPUT></div>
<div class="entry"><label for="type">Type:</label>
<select name="type" id="type" accesskey="t" tabindex="1">
<option value="0" SELECTED>Normal</option>
<option value="1">Alert</option>
</select></div>
<div class="entry"><label style="vertical-align:top;" for="newmsg">Message:</label>
<textarea NAME="newmsg" id="newmsg" rows=3  cols="55" accesskey="m" tabindex="1" ><?php echo $pnewmsg;?></textarea>
<input type="submit" name="send" id="send" value="Send" title="Send the message" accesskey="s" tabindex="1" style="min-width:10ex;vertical-align:top;" />
</div>
<?php 
if ($errmsg != '') {
echo '<div class="errormsg">New message error:&nbsp;&nbsp;'.$errmsg.'</div>';
}
?>
</div> <!-- block -->
</form>
<script type="text/javascript" >$('#from').focus();</script>
<div class="clearnone"></div>


<form method="post" action="index.php" name="scroll" id="scroll">
<input type="hidden" name="page" id="page" value='share' />
<input type="hidden" name="id"   id="id"   value='0' />
<div class="subheading">Sent messages<span> <input type="image" name="prev" id="prev" value="a" tabindex="-1" accesskey="d" " title="Later messages" src="images/up_smallw.png" />
<input type="image" name="next" id="next" value="b" tabindex="-1" accesskey="u" title="Earlier messages" src="images/down_smallw.png" /></span></div>

<div class="messages">
<?php
$lastdate='';
$start='';
if ($res) while ($row=db_fetch($res)) {
	if ($start == '') $start = $row['id'];
	if ($lastdate != $row['msg_date'] && $prm['today'] == $row['msg_date']) {
		echo '<div class="lineheading">Today</div>';
		$lastdate = $row['msg_date'];
	}
	elseif  ($lastdate != $row['msg_date'] ) {
		echo '<div class="lineheading">'. $row['fmt_date'].'</div>';
		$lastdate = $row['msg_date'];
	}

	echo '<div><span>'.$row['time'].'</span><span>'.$row['msg_sender'].'</span>';
	if ($row['msg_type'] == '1') 
		echo '<span style="color:red;">';
	else
		echo '<span>';
echo str_replace(array("\r\n","\r","\n"),array('<br />','<br />','<br />'),$row['msg_text']).
'</span></div>';
	$end = $row['id'];
}
if ($end == $prm['minid']) {
	echo '<div><span></span><span></span><span style="color:red;">-- end of messages --</span></div>';
}
?>
<input type="hidden" name="start"   id="start" value="<?=$start;?>" />
<input type="hidden" name="end"     id="end"     value="<?=$end;?>" />
</form>
</div> <!-- share_messages -->
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
