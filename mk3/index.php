<?php

/**
 * This is the index page of the site. It provides the central structure for all the different pages and classes to revolve around.
 * The user is sent away to be logged in and returns here upon successful login.
 * This page accepts a GET variable containing the window name which generates the correct screen using the window class.
 */

define('DIR_ROOT', __DIR__ . '/' );
define('DIR_INCLUDE', DIR_ROOT .   'include/' );
define('DIR_PHOTOS', DIR_ROOT . 'photos/' );

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
require_once DIR_INCLUDE.'config.php';
require_once DIR_INCLUDE.'db.inc';
require_once DIR_INCLUDE.'global.inc';

session_start();
/*
 * special case for file upload
 */

if (isset($_SERVER["PATH_INFO"]))  {
	switch ($_SERVER["PATH_INFO"]) {
	case '/upload':
	case 'upload':
		require __DIR__.'/include/upload.php';
		return;
	default:
		break;
	}
}

function chk_cookie($interval)
{
    //if ( $_SESSION['user']['time'] + $interval > time()) {
//    if ( isset($_SESSION['user']['timeout'])) {
	if ($interval < 300) $interval = 2400;
        setcookie ('PHPSESSID',$_COOKIE['PHPSESSID'],time()+$interval);
//i_SESSION['user']['session_timeout']);
//    }
//    else {
//        setcookie ('PHPSESSID',$_COOKIE['PHPSESSID'],time()+1800);
//    }
}
isset($_SESSION) or die (""); // hacking
/*
 * special case for ajax ping session
 */

if (isset($_GET) && isset($_GET["pingsession"]))  {
  if(isset($_SESSION['user']) ) {
    chk_cookie($_SESSION['user']['session_timeout']);
    $_SESSION['user']['time']=time();
    $_SESSION['user']['ping']=$_SESSION['user']['time'];
  }
  return;
}

ob_start();

//Turn off Magic Quotes
turn_off_magic_quotes();




//print_r($_SESSION);


$timedout=0;
if(isset($_GET) && isset($_GET['timedout'])) $timedout=1;
if(isset($_SESSION['user']) ) {
   $last_time = intval($_SESSION['user']['time']);
   $now = time();
   if (($now - $last_time) > $_SESSION['user']['session_timeout'] ) {
   	$timedout=1;
    	setcookie ('PHPSESSID',$_COOKIE['PHPSESSID'],time());
   	unset($_SESSION['user']);
   }
   else	{
       chk_cookie($_SESSION['user']['session_timeout']);
       $_SESSION['user']['time']=time();
   }
//   isset($_SESSION['user']['ping']) and	echo $_SESSION['user']['ping'];
}


//Check to see if the user is logged in. Display the login page if they're not else continue.
if (/*false &&*/ !isset($_SESSION['user']) /* or many other checks */) {
    require_once  DIR_INCLUDE.'login.php';
    if (ob_get_length() > 0) {
    	setcookie ('PHPSESSID',$_COOKIE['PHPSESSID'],time());
        return;
    }
    if ($login_ok != 1) { // double check
    	setcookie ('PHPSESSID',$_COOKIE['PHPSESSID'],time());
    	return;
    }
    /*
      the sole purpose of the redirect here is to drop the usr/pwd postdata
    */
    select_site();
    /* get the site preferences */
    $_SESSION['user']['siteprefs'] = sql_siteprefs();
    header('Location: ./index.php');
    return;
}

/* 
    check that we have a selected site */
select_site();

if (!isset($_SESSION['user']['siteid'])) {
	unset($_SESSION['user']);
	header('Location: ./index.php');
}


$siteprefs=$_SESSION['user']['siteprefs'];
chk_cookie($_SESSION['user']['session_timeout']);



/* =============================================================== */
/* ============  end of PHP preamble ============================= */
/* =============================================================== */

$page='';
if (isset($_REQUEST['pgno'])) {
	$pgno=intval($_REQUEST['pgno']);
}
else {
	$pgno = 0;
}

$page_array = array(
	'dashboard',
	'share'    ,
	'askmimo'  ,
	'alerts'   ,
	'toolbox'  ,
	'preferences'  ,
	'account'  ,
	'utilities'  ,
	'test'  ,
	);

$title_array = array(
	'My Mimo - Dashboard',
	'My Mimo - Share'    ,
	'My Mimo - Ask Mimo'  ,
	'My Mimo - Alerts'   ,
	'My Mimo - Toolbox',
	'My Mimo - Preferences',
	'My Mimo - My Account',
	'My Mimo - Utilities',
	'test',
	);



if (isset($_REQUEST['page'])) {
  $req_page = $_REQUEST['page'];
  $req_page = str_ireplace ( array('.php', ',inc'), '', $req_page);
  if (in_array($req_page, $page_array ))  {
     $pgno = array_search($req_page , $page_array);
     $page=$req_page.'.php';
  }
}
if ($page=='') {
    $page=$page_array[$pgno].'.php';
}

$page_title = $title_array[$pgno];

if (!isset($pgno)) {
	$pgno=0;
}

$id    = (isset($_REQUEST['id']))    ? intval($_REQUEST['id'])    : 0;
$subid = (isset($_REQUEST['subid'])) ? intval($_REQUEST['subid']) : 0;


include DIR_INCLUDE.'template.inc';

ob_end_flush();


unset($pgno);
unset($page);
unset($req_page);


function turn_off_magic_quotes(){
	//Turn off MAGIC QUOTES - they are already off
	if (get_magic_quotes_gpc()) {
	    $in = array(&$_GET, &$_POST, &$_COOKIE);
	    while (list($k,$v) = each($in)) {
	        foreach ($v as $key => $val) {
	            if (!is_array($val)) {
	                $in[$k][$key] = stripslashes($val);
	                continue;
	            }
	            $in[] =& $in[$k][$key];
	        }
	    }
	    unset($in);
	}
}

?>
