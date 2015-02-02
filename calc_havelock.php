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


if ($argc > 1) { $ndays=(int)$argv[1]; }
else { $ndays=7;}

$datestart = gmdate("Y-m-d H:i:s", time() - (86400 * $ndays));

$q='select distinct fund_code from trades;';
$res=db_query($q);
if (!$res || db_numrows($res) <=1) { echo "not enough funds\n"; exit(1); }
$funds = array();
while($r   = db_fetch($res)) {
  $funds[] = $r['fund_code'];
};
//var_dump($funds);
$Vi=array();
$T1=array();
$delfunds=array();
foreach($funds as $fund) {

  //$q="SELECT fund_code,id,tran_time as d,price as p,qty as q FROM trades WHERE ".
  $q="SELECT tran_time as d,price as p,qty as q FROM trades WHERE ".
      "fund_code = '". $fund. "' ". 
     "AND tran_time >= '". $datestart. "' ORDER BY tran_time;"; 
  //echo "$q\n";
  $res=db_query($q);
  if (!$res || db_numrows($res) <=1) { $delfunds[] = $fund; continue; }
  $trades = array();
  while($r   = db_fetch($res)) {
    $trades[] = $r;
  };

// var_dump($trades);
/* 1. Calculate observed growth rate (V)
For each code;
  Subtract each trade value from the trade value before it for a specified time period (t). Call these sets of values "Vi"
Take the arithmetic average. (V)
*/
  $sum=(float)0;
  $vi=array();
  $n=(float)count($trades);
  for ($i=1; $i < $n; ++$i) {
    $v=($trades[$i]['p'] -  $trades[$i-1]['p']) * 100;
//	printf("%f \n",$trades[$i-1]['p']);
    $vi[]=$v;
    $sum+=$v;
  }
//  printf("--- %7.7s   %f %f\n", $fund, $n, $sum );
//  unset($trades);
  --$n;
  $V=(float)$sum/((float)$n);
//   printf("%7.7s  %f %f %f\n", $fund, $V, $sum, $n);
  $Vi[$fund]=(float)$V;
  $T1[$fund]=(float)($sum*$sum) / (float)($n*$n);
//  printf("--- %7.7s  %G %G %G %G\n", $fund, $V, $T1[$fund],$sum*$sum,$n*$n );
} //end of foreach fund

$funds = array_diff($funds,$delfunds);

  /* 2. Calculate covariance Matrix (Oij)
   For each code;
   For each set of Vi;
   (Sum of Vi²) /(Number of values of Vi)²  - V²
   These values form the covariance Matrix, with the diagonals representing the variance of each code (Oii). */
$sigmaii=array();
foreach($funds as $f1) {
  $sigmaii[$f1] = array();
  foreach($funds as $f2) {
     $sigmaii[$f1][$f2]  = $T1[$f1] - ($Vi[$f2]*$Vi[$f2]);
//     printf("%7.7s %7.7s  %f %f\n", $f1,$f2, $T1[$f1], $Vi[$f2]*$Vi[$f2]);
  }
}

  /* 3. Calculate growth rate for each code (U)
    U = V - 0.5*Oii */
$U=array();
foreach($funds as $f) {
  $U[$f] = $Vi[$f] - (0.5*$sigmaii[$f][$f]);
}


  /* 4. Calculate Portfolio Weights (Wi)
  Defined by:
  Sum over j [Wj*Sqrt(Oij) ] = Ui 
  Wj = U / sigma ( sqrt(Oij) )
 */
$W = array();
foreach($funds as $f1) {
  $s=0;
  foreach($funds as $f2) {
     $s +=  sqrt( abs($sigmaii[$f1][$f2] ) ) ;
  }
  $W[$f1] = $U[$f1] / $s;
}

function scalenbr($n)
{
  $h=max(abs(min($n)), max($n));
  $r=array();
  foreach($n as $v) {
     $r[]=(int)(($v * 100.0) / (float)$h);
  }
  return $r;
}
//var_dump($U);
//var_dump($W);
$i=0;
$scu=scalenbr($U);
$scw=scalenbr($W);
foreach($funds as $f) {
  printf("%8s  %+.5f %4.4d \t%+.5f %4.4d\n", $f, ($U[$f]),$scu[$i], ($W[$f]),$scw[$i]); $i++;
}
echo "\n\n";
printf("        ");
foreach($funds as $f1) {
  printf("%8s  ", $f1);
}
printf("\n");
foreach($funds as $f1) {
  printf("%8s  ", $f1);
  foreach($funds as $f2) {
     printf("%+.5f  ",$sigmaii[$f1][$f2] );
  }
  printf("\n");
}

?>

