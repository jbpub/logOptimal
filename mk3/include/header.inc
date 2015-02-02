<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<noscript>
	<zzMETA HTTP-EQUIV=Refresh CONTENT="0; URL=noscript.html">
</noscript>




<?php

/* we only load the ui stuff when we absolutely have to 
 * dupe conditions below to allow for different pages
 */
$use_ui = false;
$use_flot = false;

if (!isset($pgno)) $pgno=0;
if (!isset($id)) $id=0;
if ($pgno == 2 && $id == 4) {
    $use_ui = true;
    $use_flot = true;
}
if ($pgno == 0 && $id == 0) $use_flot=true;
#echo "<!-- $page $pgno $id $use_ui -->";
# <script type="text/javascript" src="ui/combobox.js"></script>
# <link rel="stylesheet" href="ui/css/ui-lightness/jquery-ui-1.8.20.custom.css" />
#<link rel="stylesheet" href="ui/combobox.css" />
?>


<?php if ($use_ui) { ?>
<link rel="stylesheet" href="ui/css/custom-theme/jquery-ui-1.8.20.custom.css" />
<?php } 
if ($pgno == 1 && $id==2) { ?>
<link href="scripts/fileuploader.css" rel="stylesheet" type="text/css">	
<?php } ?>

<link href="style.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="scripts/jquery-1.7.2.min.js"></script>
<?php if ($use_flot) { ?>
<script type="text/javascript" src="scripts/jquery.flot.min.js"></script>
<?php } ?>
<?php if ($use_ui) { ?>
<script type="text/javascript" src="ui/js/jquery-ui-1.8.20.custom.min.js"></script>
<?php } ?>

<?php if ($pgno == 2 && $id==0) $id = 3; ?>
<?php if (($pgno == 2 && $id==3) ||
          ($pgno == 0) ||
          ($pgno == 4 && $id==1) ||
          ($pgno == 4 && $id==2)) { 
echo '<script type="text/javascript" src="scripts/jquery.simplemodal.1.4.2.min.js"></script>'."\r\n";
} ?>
<script type="text/javascript" src="scripts/script.js"></script>
<?php if ($pgno == 1 && $id==2) { ?>
<script src="scripts/fileuploader.js" type="text/javascript"></script>

<?php } 
/*

<script type="text/javascript" src="scripts/ezcalendar.js"></script>
<input name="date" id="date" type="text" size="10" maxlength="10" value="" />
 <a href="javascript: showCalendar('date')">Show calender</a>
 <a href="javascript: alert(isValidDate(getID('date').value))">Is date valid?</a>


<link href="assets/jqueryui.css" type="text/css" rel="stylesheet" />
<link href="assets/jquery.sb.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="scripts/jquery.sb.min.js"></script>
*/ ?>

<SCRIPT type="text/javascript">
function flashexists() {
  if (navigator.plugins && navigator.plugins.length) {
    for (n = 0; n < navigator.plugins.length; n++) {
      if (navigator.plugins[n].name.indexOf('Shockwave Flash') != -1) {
          return true;
      }
    }
  }
  return false;
}
function rootpath() {return window.location.pathname.replace(/\/[^\/]+$/,'\/');}
function rooturi() { return window.location.href.replace(/\/[^\/]+$/,'\/');}
if (!flashexists()) {
//	window.location.href = rooturi() + 'noflash.html';
}
</SCRIPT>
<title><?=$page_title;?></title>
</head>
