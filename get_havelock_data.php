<?php

//print_r ($argc);
//print_r ($argv);


$key="axbQznyh5HLC2ZmpMMfNFVKxHBUchg2AVTRL8Gu4P76FWhvjvSx3n8qw9ARhgHh5";
$url="https://www.havelockinvestments.com/api/index.php";
$data_dir="./data/";

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


if ($argc > 1) { $key=$argv[1]; }
if ($argc > 2) { $url=$argv[2]; }

echo "$argv[0]\narg 1  is the security key\narg 2 is the (optional) havelock url\n";


// There is an example PHP script on their site:

//print_r (stream_get_wrappers());

$ch=curl_init() or exit ("cannot initialize curl");

curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

function api($cmd,$vars=array()) {
    global $ch;
    global $url;
    global $key;
    curl_setopt($ch,CURLOPT_URL, $url);
    $fields=array("key"=>$key,
            "cmd"=>$cmd);
    $fields_string="";
    $fields=array_merge($fields,$vars);
    foreach($fields as $k=>$value) { $fields_string .= $k.'='.urlencode($value).'&'; }

    $fields_string=rtrim($fields_string, '&');

    curl_setopt($ch,CURLOPT_POST, count($fields));
    curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30/*0*/);
//    curl_setopt($ch, CURLOPT_HEADER,  1);
//    curl_setopt($ch, CURLINFO_HEADER_OUT,  1);

    $tries = 0;
    do {
        $ret=curl_exec($ch);
        if (curl_errno($ch) == 28) {
            echo "try $tries+1\n";
            if (++$tries > 5) break;
            continue;
        }
        break;
    } while (true);
    if ($ret == NULL) {
       echo "curl exec failed - try $tries";
       exit(1);
    }
   
//    var_dump($ret);
    return json_decode($ret,true);

}
/*
echo "==> Getting ticker list ...";
$ticker_list = api("ticker");
if (! $ticker_list ) exit(1);
print "ticker\n";
var_dump($ticker_list);
*/
//    $info = curl_getinfo($ch, CURLINFO_HEADER_OUT);
//    var_dump ($info);
//    $info = curl_getinfo($ch);
//    var_dump ($info);

/* get the trade history for list of accounts */

    /* n days before now */
    $datestart = gmdate("Y-m-d H:i:s", time() - (86400 * 30));
// BTM,209780,2014-10-11 16:08:54,0.00179997,6
//foreach($ticker_list as $k => $v) {

$v='AM1';
$v='BTM';
    $wk_trade=array();

    $file=$data_dir.$v;
    $file_trade = unserialize(@file_get_contents($file));
    $last_id = ($file_trade) ? $file_trade['id'] : 0;
 
    $start_ix=null;
    if ($file_trade) for($i=0, $sz=count($file_trade); $i < $sz; ++$i) {
        if ($file_trade[$i]['d'] >= $datestart) $start_ix = $i;
    }


    var_dump($file_trade);
exit(1);
    echo "==> Getting trade history ...";
    $trade = api("trades", array( 'symbol' => $v, 'dtstart' => $datestart ));
    if (! $trade ) {
        echo "error ". curl_errno($ch) . "\n";
        echo "web site offline\n"; exit(1);}
    if ($trade['status'] != 'ok' ) exit(1);
    echo "trade\n";
    $average = array_sum(array_column($trade['trades'], 'p')) / count($trade['trades']);
    $cnt =  count($trade['trades']);
    echo "average = $average $cnt\n";
    $sum=0;
    $cnt=0;
    $ser_trade = serialize($trade['trades']);

    foreach($trade['trades'] as $t) {
      echo $v.','.$t['id'].','.$t['d'].','.$t['p'].','.$t['q']."\n";
      $sum += (float)$t['p'] * (float)$t['q'];
      $cnt += (float)$t['q'];
    }
    echo "\naverage = ".$sum/$cnt. "  ".$cnt."\n";
    unset($t);
//    var_dump($trade);
/* [118]=>
    array(4) {
      ["id"]=>
      string(6) "199524"
      ["d"]=>
      string(19) "2014-09-03 11:56:57"
      ["p"]=>
      string(10) "0.00149000"
      ["q"]=>
      string(2) "65"
*/
//}
unset ($v);
// print_r(api("tickerfull",array("symbol"=>"HIF")));
// print_r(api("orderbook",array("symbol"=>"HIF")));

// print_r(api("balance"));
//print_r(api("portfolio"));

//create an order (very low bid price, so it wont fill)
//$o=api("ordercreate",array("symbol"=>"HIF", "action"=>"buy", "price"=>0.0001, "units"=>10));
//print_r($o);

//and cancel the order created above
//print_r(api("ordercancel",array("id"=>$o['id'])));

    curl_close($ch);

?>
