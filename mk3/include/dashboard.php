<!-- variable menu -->
<?php /* vim: set ai sw=4 ts=4: */ ?>
<div class="navigation" id="navigation">
<a href="index.php?page=share&amp;id=2"><div class="menubtn menubtn_y">Share<span class="btn_smalldescr"><hr />
<b>Communicate with ease</b><br/>Get mimo to deliver photos<br />Pass on a nice message<br />Update Reminders &amp; To Do lists</span></div></a>
<a href="index.php?page=askmimo&amp;id=1"><div class="menubtn menubtn_y">Ask Mimo<span class="btn_smalldescr"><hr /><b>Check out monitor details</b><br />Setup a new monitor node. Take a look at mimo profile. Update time schedules.</span></div></a>
<a href="index.php?page=alerts&amp;id=0"><div class="menubtn menubtn_y">Alerts<span class="btn_smalldescr"><hr /><b>Everything you should know.</b><br />Any alerts over 24 hrs. Any monitor batteries that might be low.Any monitors not reporting properly.</span></div></a>
<a href="index.php?page=toolbox&amp;id=0"><div class="menubtn menubtn_y">Toolbox<span class="btn_smalldescr"><hr /><b>Check out sense points.</b><br />Setup a new monitor node.<br />Take a look at mimo profiles.<br />Update time schedules.</span></div></a>
</div> <!-- navigation -->

<div class="clearnone"></div>
<div class="content" id="content">

<style>

div.dashboard
{
	min-height:29em;
	margin:0 2ex 0 2ex;
	margin:0;
}
div.dashboard table
{
	width: 99%;
	min-width: 99%;
	max-width: 99%;
}
div.dashboard td
{
	text-align: left;
	width: 40%;
	min-width: 40%;
	max-width: 40%;
	padding: 1ex 3ex;
	font-weight: normal;
	height:10em;
	min-height:10em;
}
div.dashboard .graph_view,
div.dashboard .graph_wrapper,
div.dashboard .graph_holder
{
	height:100%;
	min-height:100%;
	width:100%;
	min-width: 100%;
	max-width: 100%;
}

</style>

<div class="clearall"></div>

<div class="dashboard">

<table><tbody>
<?php

$query = "select id, name, node_point_ids as uri, seq from dashboard_graph_panels ".
	"where  deleted = 0 and graph_type='plot' and display = 1 ".
	"order by seq ;";
$i=0;
$rows=array();
if ($res = db_query($query)) {
  while ($r=db_fetch($res)) {
    if (strstr($r['uri'],'graphdata')) {
      $rows[$i++] = $r;
    }
  }
}
$nrows=$i;
$c=0;
foreach($rows as $i => $v) {
  if ($c == 0) echo '<tr>'; 
  echo '<td>';
  echo '<div class="graph_view" id="graph_view'.$i.'" data="'.$v['uri'].'">';
?>
<div class="graph_wrapper"  style="margin: 0 auto; width:80%;height:100%">
<H2>View  graph</H2>
<div class="graph_holder" style="height:100%;width:100%;"></div>
</div> <!-- graph_wrapper -->
</div> <!-- id="graph_view" -->
<?php
  echo '</td>';
  $c++;
  if ($c > 1) {echo '</tr>';$c=0;}
}
if ($c == 1) {echo '</td><td></td>';}
if ($c > 0) {echo '</tr>';$c=0;}

/*<tr><td></td><td></td></tr>*/
?>
</tbody></table>

 <div id="graph_viewx" style="max-width:400px;">
<div class="graph_wrapper"  style="margin: 0 auto; width:400px;height:400px">
<H2>View  graph</H2>
<div class="graph_holder" style="width:300px; height:300px;"></div>
</div> <!-- graph_wrapper -->
</div> <!-- id="graph_view" -->
<script type="text/javascript">
$(document).ready(function() {
		$('.graph_view').each(function(i,el) {
//			show_history_graph($(el).attr('data'), '#'+el.id);
			//console.log(el.id, $(el).attr('data'));
			}
		);
//		show_history_graph('async.php/get_graphdata/month?snpids=1',
			'#graph_viewx');
});
</script>

</div> <!-- class="dashboard" -->

<div class="dashboard_info" style="margin:0;">
<span style="font-size:1.3em;padding:0 1ex 0 5ex;"</div>Did you know?</span><span style="text-shadow: 1px 1px #cccccc;font-size:.85em;font-weight:normal;">It's easy to add a new graphic to this dashboard.</span><input type="image" src="images/addnewgraphic.jpg" />
</div><!-- dashboard_info -->
</div> <!-- content -->
<div style="height:1em;"></div>
