<?php
/*
	Initialize all the globals we use
*/
/*
$sql=false;
*/

$version='0.1 1/4/2012';
$site='';
$site_time='';


$page_heading='My Mimo';
$page_subheading='Toolbox';
$page='toolbox';
$page_title='My Mimo - Toolbox';

$timedout=false;


define('QVMC', DIR_ROOT.'cgi/qvmc ');
function bgexec($cmd)
{
	return @exec('/usr/bin/nohup '.$cmd . ' > /dev/null 2>&1 & echo $!');
// should be same as exec(... &>/dev/null &');
}

#DROP TABLE IF EXISTS php_sessions;
#CREATE TABLE php_sessions (
#  sesskey varchar(64) NOT NULL default '',
#  expiry int(11) unsigned NOT NULL default '0',
#  value mediumblob NOT NULL,
#  PRIMARY KEY  (sesskey)
#);

$max_sess_life = ini_get("session.gc_maxlifetime");

function _sess_open($save_path, $session_name) {
    return true;
}

function _sess_close() {
    return true;
}

function _sess_read($key) {
    $query = "select value
            from php_sessions
            where sesskey = '" . db_escstr($key) . "'
            and expiry > '" . time() . "'";

    $res  = db_query($query);
    $r = db_fetch($res,DB_NUM)[0];

    if (isset($r) && $r) {
      $r = base64_decode($r);
      return $r;
    }

    return ("");
}

function _sess_write($key, $val) {
    $val = base64_encode($val);

    global $max_sess_life;

    $expiry = time() + $max_sess_life;

    $query = "select count(*) as total
            from php_sessions
            where sesskey = '" . db_escstr($key) . "'";

    $total = db_fetch(db_query($query),DB_NUM)[0];

    if ($total > 0) {
      $query = "update php_sessions
              set expiry = '" . db_escstr($expiry) . "', value = '" . db_escstr($val) . "'
              where sesskey = '" . db_escstr($key) . "'";

      $res = db_query($query);

    } 
    else {
      $query = "insert into php_sessions
              values ('" . db_escstr($key) . "', '" . db_escstr($expiry) . "', '" .
                       db_escstr($val) . "')";

      $res = db_query($query);

    }
  return (!empty($res) && !empty($res->resource));
}

function _sess_destroy($key) {
    $query = "delete from php_sessions where sesskey = '" . db_escstr($key) . "'";
     return db_query($query);
}

function _sess_gc($maxlifetime) {
    $query= "delete from php_sessions where expiry < " . time();
    db_query($query);
    return true;
}

  session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');

  function qv_session_start() {
    @ini_set('session.gc_probability', 1);
    @ini_set('session.gc_divisor', 2);
    if (preg_replace('/[a-zA-Z0-9]/', '', session_id()) != '') {
  	  qv_session_id(md5(uniqid(rand(), true)));
    }
    $temp = session_start();
    if (!isset($_SESSION['securityToken'])) {
      $_SESSION['securityToken'] = md5(uniqid(rand(), true));
    }
    return $temp;
  }


  function qv_session_id($sessid = '') {
    if (!empty($sessid)) {
      $tempSessid = $sessid;
  	  if (preg_replace('/[a-zA-Z0-9]/', '', $tempSessid) != '')
  	  {
  	    $sessid = md5(uniqid(rand(), true));
  	  }
      return session_id($sessid);
    } else {
      return session_id();
    }
  }

  function qv_session_name($name = '') {
    if (!empty($name)) {
      $tempName = $name;
      if (preg_replace('/[a-zA-Z0-9]/', '', $tempName) == '') return session_name($name);
      return FALSE;
    } else {
      return session_name();
    }
  }

  function qv_session_close() {
    if (function_exists('session_close')) {
      return session_close();
    }
  }

  function qv_session_destroy() {
    return session_destroy();
  }

  function qv_session_save_path($path = '') {
    if (!empty($path)) {
      return session_save_path($path);
    } else {
      return session_save_path();
    }
  }


function select_site() {
	global $site;
	global $site_time;

/* dirty kludge - until we figure out multiple sites */
	$siteinfo=sql_siteinfo();

	if (count($siteinfo) == 0) {
		die ('no sites defined');
	}
	elseif (count($siteinfo) == 1) {
		$siteinfo = $siteinfo[0];
		if (!isset($_SESSION['user']['siteid'])) {
			$_SESSION['user']['siteid'] = $siteinfo['siteid'];
			$_SESSION['user']['siteix'] = 0;
			$site_timeout = intval($siteinfo['session_timeout']) + 0;
			if ($site_timeout <= 0) $site_timeout = 60 * 20;
			$_SESSION['user']['session_timeout'] = $site_timeout;
		}
	}
	else {
		if (isset($_SESSION['user']['siteix'])) {
			$siteinfo = $siteinfo[$_SESSION['user']['siteix']];
		}
		else {
    			die ('multiple sites not implemented');
		}
	
	}	


	$site=$siteinfo['name'];
	$site_time=$siteinfo['time'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$siteinfo['date'];
}

/*
   miscellaneous global functions
*/

function add_selectn($n, $m)
{
	if ($n==$m)
	    return  ' nav_selected';
	else
	    return  '';
}

function add_selects($n, $m)
{
	if ( (strpos($n,$m) === false) )
	    return  '';
	else
	    return  ' nav_selected';
}

