<?php

?>
<div class="content" id="content" style="margin: 1ex auto 1ex auto;">

<div style="margin-top:9px;"></div>

<div class="clearnone"></div>
<style>
.ubtn {
	width:20%;
	float:left;
}
.uout {
	width:78%;
	max-width:78%;
	max-height:30em;
	margin: 0 2ex 0 21%;
	overflow: auto;
	font-family: "lucida-console", monospace;
	font-size: 10pt;
	white-space: pre;
}
</style>
<div class="utilities">
<div class="ubtn">
<form method="get" action="index.php" id="updutl">
<input type="hidden" name="page" value="utilities" />
<input type="submit" name="backup" value="Backup" /><br />
<input type="submit" name="status" value="Status" />
<input type="submit" name="showip" value="IP Addresses" />
<input type="submit" name="restartwifi" value="Restart WiFi" />
<input type="submit" name="qvmanagerstart" value="Start qvmanager" />
<input type="submit" name="qvmanagerstop" value="Stop qvmanager" />
</form>
</div> <!-- ubtn -->

<div class="uout">
<?php 
#error_reporting(E_ALL);
function ex($cmd, $async=false)
{
/* Add redirection so we can get stderr. */
echo '<b>'.preg_replace('/.+\/([^\/]+)$/',"$1",$cmd)."</b>\n";
if (!strstr($cmd,'>')) {$cmd .=' 2>&1';}
if ($async) {
  $cmd .= ' &';
  echo ($cmd);
}
$h = popen($cmd, 'r');
#echo "'$h'; " . gettype($h) . "\n";
while ($s = fread($h, 2096)) {
	echo $s."\r\n";
}
pclose($h);
}

if (!empty($_GET) && count($_GET) > 1) {
  if (!empty($_GET['status'])) {
	$out='';
	ex(QVMC.'status');
	ex(QVMC.'network');
	ex('ifconfig');
	ex('df -ahT');
	ex('free');
	ex('id');
  }
  if (!empty($_GET['backup'])) {
	$pid=bgexec('sudo sync_dp2s');
	echo "Backup initiated ($pid)";
  }
  if (!empty($_GET['showip'])) {
	$out='';
	ex('showip');
  }
  if (!empty($_GET['restartwifi'])) {
	ex('sudo ifdown -v mlan0');
	ex('sudo ifup -v mlan0');
  }
  if (!empty($_GET['qvmanagerstop'])) {
	ex('sudo /etc/init.d/qvmanager stop');
  }
  if (!empty($_GET['qvmanagerstart'])) {
	ex('sudo /etc/init.d/qvmanager start');
  }
//  if (!empty($_GET['msgupload'])) {
//	ex('php -f /usr/local/bin/upload_messages');
//  }
}  
?>
</div> <!-- uout -->
</div> <!-- utilities -->

</div> <!-- content -->

