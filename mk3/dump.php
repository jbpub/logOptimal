<?php
session_start();
if (isset($_REQUEST['_SESSION'])) die("");
$nl= "<pre>\r\n";
echo $nl;
print_r($_SESSION);
print_r($_COOKIE);

$CookieInfo = session_get_cookie_params();

echo "Session information session_get_cookie_params function :: <br />";
print_r($CookieInfo);


echo 'print_r= ini_get("session.gc_maxlifetime");'.$nl;
print_r( ini_get("session.gc_maxlifetime"));

$timeout_secs=60 * 60;
session_start();setcookie ('PHPSESSID',$_COOKIE['PHPSESSID'],time()+$timeout_secs);


echo $nl;
