<?php

define( "DATABASE_SERVER", "localhost:3306" );
define( "DATABASE_USERNAME", "root" );
define( "DATABASE_PASSWORD", "c0untess" );
define( "DATABASE_NAME", "qvantage" );

define ("DB_NUM", MYSQL_NUM);
define ("DB_ASSOC", MYSQL_ASSOC);
define ("DB_BOTH", MYSQL_BOTH);

$sql=false;
$sql = mysql_connect(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD) or die(mysql_error());
$sql_userid=false;

mysql_select_db( DATABASE_NAME );

function db_query($query)
{
  global $sql;
  return mysql_query($query,$sql);
}
function db_exec($query)
{
  global $sql;
  return mysql_query($query,$sql);
}

function db_free_result($res)
{
  return mysql_free_result($res);
}

function db_fetch($res, $mode=DB_ASSOC)
{
  return mysql_fetch_array($res, $mode);
}
function db_escstr($str) 
{
  global $sql;
  return mysql_real_escape_string($str,$sql);
}
function db_numrows($res)
{
  return mysql_num_rows ($res);
}
function db_effected_rows()
{
  global $sql;
  return mysql_affected_rows ($sql);
}
function db_insertid()
{
  global $sql;
  return mysql_insert_id($sql);
}
function db_error()
{
  global $sql;
  return mysql_error($sql);
}
function db_errno()
{
  global $sql;
  return mysql_errno($sql);
}



function sql_siteinfo()
{
  global $sql;


  if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) )
    return null;
  $query = "SELECT s.name as name, s.id as siteid, p.session_timeout as session_timeout,
	CURTIME() as time, CURDATE() as date, 
	TIME_TO_SEC(TIMEDIFF(NOW(),UTC_TIMESTAMP())) as gmt_offset_secs,
	TIMEDIFF(NOW(),UTC_TIMESTAMP()) as gmt_offset,
	(UNIX_TIMESTAMP() -" . time() . ") as server_offset,
	'".$_SESSION['user']['username']."' as username
        from sites s 
	inner join user_sites us on s.id = us.siteid
 	left outer join site_prefs p  on p.id = s.id
        where us.userid='" . $_SESSION['user']['id'] . 	"';
	";
//LIMIT 1 where ;';
/*
	we need to iterate over these iin case we have more than one site defined
	for this user
*/
  $rows = array();
  $i = 0;
  $result =  db_query($query);

  while($row   = db_fetch($result)) {
	$rows[$i++] = $row;
  };
  return $rows;
}


function sql_logon($usr, $pw)
{
  global $sql;
  global $sql_userid;

  $query = sprintf("SELECT * from users where username = '%s' and password = '%s';",
  			db_escstr($usr),
  			db_escstr($pw));
  $res   = db_query($query);
  if (!$res) return false;
  $row   = db_fetch($res);
  if ($row) {
    $sql_userid=$row['id'];
  }
  unset($row['password']);
  return $row;
}

function sql_siteprefs($prefs=NULL)
{
  global $sql;
  $siteid=$_SESSION['user']['siteid'];

  if (!$prefs) {
    $query = sprintf("SELECT * from site_prefs  where id = %d;",
  			$siteid);
    $res   = db_query($query);
    if (!$res) return null;
    $row   = db_fetch($res);
    if ($row) unset($row['id']);
    return $row;
  }  
  /* else */
  $query = "update site_prefs set ";
  foreach($prefs as $k => $v) {
    if (is_numeric($v)) {
      $query .= sprintf("%s = %d, ",  $k, $v);
    }
    else {
      $query .= sprintf("%s = '%s', " , $k, db_escstr($v));
    }
  }
  $query = substr($query,0, -2);
  $query .= sprintf(" where id = %d;", $siteid);

  $res=db_query($query);
  if (db_effected_rows() == 0) {
    $query = "insert into site_prefs ( id, ";
    foreach($prefs as $k => $v) {
      $query .= $k . ", ";
    }
    $query .= sprintf(" ) values ( %d, ",$siteid);
    foreach($prefs as $k => $v) {
      if (is_numeric($v)) {
        $query .= sprintf("%d, ", $v);
      }
      else {
        $query .= sprintf("'%s', ", db_escstr($v));
      }
    } 
    $query = substr($query,0, -2);
    $query .= ");";
    $res=db_query($query);
  }
  return $res;
}

function sql_msg_get($p_status=-1, $p_fromid=0, $p_toid=0, $p_limit=0)
{
	$limit='';
	($p_limit > 0) and $limit = 'limit '.$p_limit;

	$where='';
	($p_fromid > 0) and $where .= 'id >= '.$p_fromid;
	if ($p_toid > 0) {
		($where != '') and $where .= ' and ';
		$where .= 'id <= '.$p_toid;
	}

	if ($p_status >= 0) {
		($where != '') and $where .= ' and ';
		$where  .= "msg_status = '".$p_status."'";
	}


	($where != '') and $where = 'where '.$where;


	$query =
	"SELECT  *, DATE(msg_time) as msg_date, DATE_FORMAT(msg_time,'%W, %M %D, %Y') as fmt_date, TIME_FORMAT(msg_time,'%l:%i%p') as time, DATE_FORMAT(msg_time,'%W') as dayname  from messages $where order by id desc $limit ;";

	//echo $query."\r\n\r\n";

	$res = db_query($query);
	$rows = array();
	if ($res == false) return false;
	if (db_numrows($res) <= 0) return false;
	$i = 0;
	while($row   = db_fetch($res)) {
		$rows[$i++] = $row;
	};
	return $rows;
}

function sql_msg_update_status($p_status, $rows)
{
	if (!$rows || count($rows) <=0) return false;
	$ids=array();
	foreach($rows as $v) {
		$ids[] = $v['id'];
	}
	$query =
	"UPDATE messages set msg_status = '".$p_status."' where id in ( '" . implode("', '",$ids)."' );";
	//print($query);
	db_query($query);
	return db_effected_rows();
}

?>
