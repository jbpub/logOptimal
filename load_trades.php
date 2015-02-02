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

/* get the trade history for list of accounts */

    /* n days before now */
// BTM,209780,2014-10-11 16:08:54,0.00179997,6

  $funds=array();
  $res=db_query('select fund_code from funds where check_trades = 1;');
  if (!$res) exit(1);
  while($row   = db_fetch($res)) {
    $funds[] = $row['fund_code'];
  }

  foreach($funds as $fund) {

    $datestart = null;
    $res=db_query("select MAX(tran_time) as maxtt from trades where fund_code = '".$fund."';");
    if ($res) {
      $ds = db_fetch($res);
      if (isset($ds['maxtt']))  $datestart = $ds['maxtt'];
    }

    //$datestart = gmdate("Y-m-d H:i:s", time() - (86400 * 30));

    echo "==> Getting trade history ...";
    if ($datestart) 
       $trade = api("trades", array( 'symbol' => $fund, 'dtstart' => $datestart ));
    else
       $trade = api("trades", array( 'symbol' => $fund));
    if (! $trade ) {
        echo "error ". api_errno() . "\n";
        echo "web site offline\n"; exit(1);}
    if ($trade['status'] != 'ok' ) exit(1);

    $q="INSERT INTO trades (id,fund_code,tran_time,price,qty) VALUES "; 
    foreach($trade['trades'] as $t) {
      $q .= "(".$t['id'].",'".$fund. "','".$t['d']."',".$t['p'].",".$t['q']."),\n";
    }
    $q=substr($q,0,-2) . " ON DUPLICATE KEY UPDATE fund_code = '".$fund."';";
    echo "\nsql = ".$q."\n";
    unset($t);
    $res   = db_query($q);
    if (!$res) {
      echo "\n\n". db_error() . "\nerror code ". db_errno() . "\n";
      exit(1);
    }
}
unset ($fund);
api_close();
?>
