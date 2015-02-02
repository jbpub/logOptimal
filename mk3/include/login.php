<?php
/*
 * Generates the login page.
*/

$login_errors = '';


function do_login()
{
    //Log the user on and get basic info

    $username = $_REQUEST['username'] or '';
    $pwd = $_REQUEST['pwd'] or '';
    $user = sql_logon($username,$pwd);
    if (!$user) {
        return  ('<tr><td class="login_error" colspan="2">Login failure !</td></tr>');
    }

    isset($_SESSION) or die ("");
    $_SESSION['user']=$user;
    $_SESSION['user']['logon_time']=time();
    $_SESSION['user']['session_time']=time();
    $_SESSION['user']['time']=time();
    $_SESSION['user']['session_timeout']=60*20;  //default
    return "";
}

$login_ok=0;


if ( isset($_POST['submit']) || isset($_POST['submit_x']) ) {
//    $sval = $_POST['submit'];
//    if (!$sval) $sval = $_POST['submit_x'];
    $login_errors = do_login();
//      print_r ($login_errors);
//    echo 'login errors '. $login_errors;
    if ($login_errors == "") {
       $login_ok=1;
       return;
    }
}

$page_title='My Mimo login';
$page_heading='My Mimo login';
include 'header.inc';
define('URI_IMAGES','images/');
?>
<body>

<div class="container" id="container">
<div id="header_wrapper">
<div class="header" id="header">
<span class="smallwhitetext">Version <?php echo $version; ?></span><!-- smallwhitetext  -->
<div class="clearboth"></div>
</div> <!-- header -->
</div> <!-- header_wrapper -->

<div class="clearboth"></div>
<div class="main" id="main" >
<div class="clearnone"></div>
<div class="content" id="content"  style="padding-top:1ex;min-width:550px;max-width:550px;min-height:350px;">

<div style="margin-top:2ex;"></div>


            <div id="login_shell" style="margin:1ex 0 0 5ex;float:none;">
                    <img class="loginPageWords" src="<?php echo URI_IMAGES; ?>login_word.jpg" />
		    <span class="clearboth" ></span>
                    <form action="" method="post" name="loginForm" style="margin:0;">
                        <table border="0" width="250" id="loginTable">
                            <tr>
                                <td class="login_label"><label for="username">Username:</label></td>
                                 <td>
                                    <input type="text" name="username" id="username" tabindex="1" accesskey="u" autocomplete="off" autofocus />
                                </td>
                            </tr>

                            <tr><td></td></tr><tr><td></td></tr>
                            <tr>
                                <td class="login_label"><label for="pwd">Password:</label></td>
                                 <td>
                                    <input type="password" name="pwd" id="pwd" tabindex="2" accesskey="p" />
                                </td>
                            </tr><tr><td></td></tr><tr><td></td></tr><tr><td></td></tr><tr><td></td></tr>
                            <tr>
<?php echo '
                            <td colspan="2" align="right" ><img src="./'.URI_IMAGES.'forgotpassword.jpg" /><input name="submit" value="1" type="image" src="./'.URI_IMAGES.'loginbtn.jpg" tabindex="-1" /></td>
<!--
                            <td colspan="2" align="right" ><input name="submit" value="7" type="image" src="./'.URI_IMAGES.'forgotpassword.jpg" /><input name="submit" value="1" type="image" src="./'.URI_IMAGES.'loginbtn.jpg"  /></td>
-->
                          </tr>
';

			 print $login_errors;

echo '</table></form><br class="clearboth" />';

    if ($timedout) {echo '<div style="font-size: 1em;font-weight:bold;margin:2em 0 0 0;float:none;color:red;text-align:center;">Session has expired - please login again</div>'; $timedout=0;}
    echo '<div style="font-size: .8em;margin:2em 0 0 0;float:none;;">Hint: mark/&quot;the usual&quot;</div>';
?>
</div>
</div> <!-- content -->
</div> <!-- main -->

<div class="clearboth"></div>
</div> <!-- container -->

</body>
</html>
