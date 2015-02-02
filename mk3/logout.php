<?php

define('DIR_ROOT', __DIR__ . '/' );
define('DIR_INCLUDE', DIR_ROOT .   'include/' );

require_once DIR_INCLUDE.'config.php';
require_once DIR_INCLUDE.'db.inc';
require_once DIR_INCLUDE.'global.inc';

session_start();

if(isset($_SESSION['user']) ) {
   	unset($_SESSION['user']);
}


    /*
      the sole purpose of the redirect here is to drop the usr/pwd postdata
    */
    header('Location: ./index.php');
    return;

?>
