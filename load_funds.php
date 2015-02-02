<?php

//print_r ($argc);
//print_r ($argv);



define('DIR_ROOT', __DIR__ . '/' );
define('DIR_INCLUDE', DIR_ROOT .   'include/' );

//ini_set("session.use_only_cookies","1");
ini_set("session.gc_maxlifetime",7200);
ini_set("session.gc_probability",1);
ini_set("session.gc_divisor",1);
# must be in ini file ini_set("upload_tmp_dir","/var/www/tmp");
# upload_max_file_size
//$dfmt="Y-m-d H:i:s";
//date($dfmt);
//$tt_startreq=$_SERVER['REQUEST_TIME'];
// date_default_timezone_set('UTC');
require_once DIR_INCLUDE.'db.inc';
require_once DIR_INCLUDE.'apifunc.inc';



echo "==> Getting ticker list ...";
$ticker_list = api("ticker");
if (! $ticker_list ) exit(1);
//print "ticker\n";
//var_dump($ticker_list);
//INSERT INTO tbl_name (a,b,c) VALUES(1,2,3),(4,5,6),(7,8,9);
$q='INSERT INTO funds ( fund_code, check_trades) values ';
foreach($ticker_list as $k => $v) {
//  echo "$k\n";
  $q .= "('" . db_escstr($k) . "', 0 )," ;
}
$q=substr($q,0,-1) . ';';
echo $q;

$res   = db_query($q);
if (!$res) {
  echo "\n\n". db_error() . "\nerror code ". db_errno() . "\n";
  exit(1);
}
/*
*/
    api_close();

?>
