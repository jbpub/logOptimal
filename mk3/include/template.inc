<?php  // isset($page) or die('page error');
include DIR_INCLUDE.'header.inc';
?>

<body>
<div class="container" id="container">
<div id="header_wrapper">
<span class="smallwhitetext">Version <?php echo $version; ?></span><!-- end smallwitetext -->
<div class="header" id="header">
<!--
  pgno= <?= $pgno; ?>
  page= <?= $page; ?>
  id= <?= $id; ?>
  page_title= <?= $page_title; ?>
-->
  <div class="hdr_links">
  <a href="index.php?page=preferences" tabindex="-1"><div class="hdr_linkitem<?php echo ($pgno=='5'?' selected':''); ?>">Preferences</div></a>
  <a href="index.php?page=account" tabindex="-1"><div class="hdr_linkitem<?php echo ($pgno=='6'?' selected':''); ?>">My Account</div></a>
  <a href="index.php?page=utilities" tabindex="-1"><div class="hdr_linkitem<?php echo ($pgno=='7'?' selected':''); ?>">Utilities</div></a>
  <a href="logout.php" tabindex="-1"><div class="hdr_linkitem">Logout</div></a>
  </div> <!-- header_links -->
  <div class="clearboth"></div>
<!-- fixed menu -->
<div class="navhoriz" id="navhoriz">
<?php echo
'<a href="index.php?page=dashboard&amp;id=0" tabindex="-1" ><div class="navitem' .add_selects($page,'dashboard').'" title="Dashboard explanation" >Dashboard</div></a>
<a href="index.php?page=share&amp;id=0" tabindex="-1"     ><div class="navitem'  .add_selects($page,'share'    ).'" title="Share explanation"     >Share</div></a>
<a href="index.php?page=askmimo&amp;id=0" tabindex="-1"   ><div class="navitem'  .add_selects($page,'askmimo'  ).'" title="Ask Mimo explanation"  >Ask Mimo</div></a>
<a href="index.php?page=alerts&amp;id=0" tabindex="-1"    ><div class="navitem'  .add_selects($page,'alerts'   ).'" title="Alerts explanation"    >Alerts</div></a>
<a href="index.php?page=toolbox&amp;id=0" tabindex="-1"   ><div class="navitem'  .add_selects($page,'toolbox'  ).'" title="Toolbox explanation"   >Toolbox</div></a>
'; ?>
</div> <!-- navhoriz -->
<div class="clearboth"></div>

<?php
/*
  <div class="header_info"><span>Site: <?=$site;?></span><span style="padding-left:5ex;">Local Time: <?php echo $site_time; ?></span></div><!-- php $site $site_time -->
  <h1><?=$page_heading;?><span style="padding-left:5em;"><?=$page_subheading;?></span></h1> <!-- php $page_heading -->
  <div id="headerhr"></div>
*/
?>
</div> <!-- header -->
</div> <!-- header_wrapper -->




<div class="clearboth"></div>
<div class="main" id="main">
<a name="h1"></a>
<div class="header_info" 
<?php if ($pgno >= 5 && $pgno <=7) echo ' style="margin:1ex auto 0 auto;"'; ?>><span>Site: <?=$site;?></span><span style="padding-left:8ex;">Local Time: <?php echo $site_time; ?></span></div><!-- header_info -->

<!-- CONTENT <?= $page; ?> -->
<?php include DIR_INCLUDE.$page; ?>
<!-- END CONTENT -->

</div> <!-- main -->

<div class="clearboth"></div>
<div id="footer_wrapper">

<!-- 
 -->
<div id="footer_meerkat">
<div class="footer" id="footer">

<div  class="footer_links">
<span>
<a href="mailto:"  tabindex="-1" ><img  src="images/email.png"  /></a>&nbsp;&nbsp;
<a href="http://www.facebook.com/" target="_blank"  tabindex="-1"><img  src="images/facebook.png"  /></a>&nbsp;
<a href="http://www.twitter.com/" target="_blank"  tabindex="-1"><img  src="images/twitter.png"  /></a>&nbsp;
<a href="http://plus.google.com" target="_blank"  tabindex="-1"><img  src="images/googleplus.png" style="vertical-align:50%;" /></a>
</span>
<div  style="margin:25px 0 0 55px; vertical-align:top;" />
<a href="" target="_blank"  tabindex="-1" ><img  src="images/more.png"  /></a>&nbsp;
</div>
<div style="float:left;color:white; font-style: italic;font-size:85%;margin:133px 0 0 0;">Copyright &copy; Qvisual Ltd.</div>
<div style="color:#dbdbdb;;font-size:70%;margin:152px 0 0 40em;">&delta;&alpha;&beta;</div>
</div>  <!-- end footer links -->
<!-- 
<div  style="float:right; margin:45px -132px 0 0;overflow:visible;" >
<img  src="images/footer_meerkat.png"  />
</div>
 -->
<div class="clearleft"></div>
<div class="clearboth"></div>
</div> <!-- footer -->
</div>
</div> <!-- footer wrapper -->
</div> <!-- container -->

</body>
</html>
