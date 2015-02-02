<?php

include 'include/db.inc';
/*
print_r(sql_msg_get('0'));
print_r(sql_msg_get('0',2,2));
print_r(sql_msg_get('1'));
*/
#echo base64_encode(gzcompress(serialize(sql_msg_get())));
#echo base64_decode(base64_encode(serialize(sql_msg_get())));

//$rows = sql_msg_get('0',2,2);
#$rows = sql_msg_get('0');
$data=sql_msg_get();
$cdata=base64_encode(gzcompress(serialize($data)));
$tmpfname = tempnam("/tmp", "");
$fp = @fopen($tmpfname, "w");
fwrite($fp, $cdata);
fclose($fp);
system('rsync -a '.$tmpfname.' mimo::messages/msgs_'.$data[0]['id'] .'.b64i',$ret);
//echo "\nreturn code".$ret."\n";

#print_r($rows);
#echo base64_decode(base64_encode(serialize(sql_msg_get())));
//print_r(sql_msg_update_status(1,$rows));

