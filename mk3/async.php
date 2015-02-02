<?php


if (!isset($_SERVER["PATH_INFO"]))  {
	return;
}
$pi = substr($_SERVER["PATH_INFO"],1);
$incl = strstr($pi,'/',true);
if ($incl == '') {
  $incl=$pi;
  $param=array();
}
else {
  $param = strstr($pi,'/',false);
  $param = substr($param,1);
  if (strlen($param) > 0) {
    $param=explode('/',$param);
  }
  else {
    $param=array();
  }
}

//ini_set("session.use_only_cookies","1");
#ini_set("session.gc_maxlifetime",7200);
ini_set("session.gc_probability",1);
ini_set("session.gc_divisor",1);
# must be in ini file ini_set("upload_tmp_dir","/var/www/tmp");
# upload_max_file_size
//$dfmt="Y-m-d H:i:s";
//date($dfmt);
//$tt_startreq=$_SERVER['REQUEST_TIME'];
// date_default_timezone_set('UTC');



//print_r($_SESSION);

define('DIR_ROOT', __DIR__ . '/' );
define('DIR_INCLUDE', DIR_ROOT .   'include/' );
define('DIR_PHOTOS', DIR_ROOT . 'photos/' );
define('DIR_AJAX', DIR_ROOT . 'ajax/' );

require_once DIR_INCLUDE.'config.php';
require_once DIR_INCLUDE.'db.inc';
require_once DIR_INCLUDE.'global.inc';


session_start();

isset($_SESSION) or die (""); // hacking

ob_start();



function chk_cookie($interval)
{
    if ( $_SESSION['user']['time'] + $interval > time()) {
        setcookie ('PHPSESSID',$_COOKIE['PHPSESSID'],time()+$_SESSION['user']['session_timeout']);
    }
}
$timedout=0;
if(isset($_SESSION['user']) ) {
   $last_time = intval($_SESSION['user']['time']);
   $now = time();
   if (($now - $last_time) > $_SESSION['user']['session_timeout'] ) {
   	$timedout=1;
    	setcookie ('PHPSESSID',$_COOKIE['PHPSESSID'],time());
   	unset($_SESSION['user']);
   }
   else	{
       chk_cookie(1200);
       $_SESSION['user']['time']=time();
   }
//   isset($_SESSION['user']['ping']) and	echo $_SESSION['user']['ping'];
}
//Check to see if the user is logged in.
if ( !isset($_SESSION['user']) ) {
	header ('status: 403 Forbidden',true, 403);
        return;
}

/* 
    check that we have a selected site */
select_site();
if (!isset($_SESSION['user']['siteid'])) {
        return;
}
/* get the site preferences */
$siteprefs=sql_siteprefs();

include DIR_AJAX.$incl.'.php';
