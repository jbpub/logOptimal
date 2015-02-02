<?php
/* vim: set ai sw=4 ts=4: */
?>
<style>
.ui-widget {
 font-size:0.85em;
}
.ui-autocomplete {
	max-height: 25em;
	overflow-y: auto;	/* prevent horizontal scrollbar */
	overflow-x: hidden;	/* add padding to account for vertical scrollbar 	padding-right: 20px;*/
	color: #404040;
	padding: .2em .4em;
}
.ui-widget-content a { 
	color: #404040; 
}
</style>
<?php
/*<script type="text/javascript" src="scripts/script.js"></script>*/
$siteid=$_SESSION['user']['siteid'];
$panel_name='';
$ondash='0';
if (isset($_REQUEST['panel_name'])) $panel_name = $_REQUEST['panel_name'];

// the update
if (isset($_POST) && isset($_POST['uri']) && $_POST['uri']!='') {
  $ondash = ((isset($_POST['ondash']) && $_POST['ondash']=='on' ) ? '1' : '0');
  $query = "select id from dashboard_graph_panels where graph_type='plot' and deleted = '0' and name= '".db_escstr($panel_name)."';";
  $res=db_query($query);
  if (!$res ||  db_numrows($res) == 0) {
    $query = "select MAX(seq) from  dashboard_graph_panels where graph_type='plot' and deleted = 0;";
    $res=db_query($query);
	$mo=0;
    if ($res &&  db_numrows($res) > 0) $mo = intval(db_fetch($res,DB_NUM)[0]);
	$mo+=10;
    $query = "insert into dashboard_graph_panels ( name, display, node_point_ids,graph_type,seq ) values ('".	db_escstr($panel_name)."', '".$ondash."','".
			db_escstr($_POST['uri'])."', 'plot',".$mo.") ;";
  }
  else {
  	$k=db_fetch($res,DB_NUM)[0];
    $query = "update dashboard_graph_panels set display='".$ondash.
	  	"' , node_point_ids='".db_escstr($_POST['uri']).
		"' where id = ".$k.";";
  }
  $res=db_query($query);
}

$query = "select distinct name, node_point_ids as uri,display as ondash, seq from dashboard_graph_panels where  `deleted` = '0' and graph_type = 'plot' order by name ;";
$res = db_query($query);
$panels = array();
$varp = "";
$selected_start='';
$selected_id1='';
$selected_id2='';
$selected_period='';
$panel_found=false;

/* and the js kludge */
while ($row =db_fetch($res)) {
	$panels[$row['name']] = $row;
	$varp .= '"'.$row['name'].'",';
	if ($panel_name == $row['name']) {
	  preg_match('@/([a-zA-Z]+)\?snpids=([0-9,]+)@', $row['uri'], $matches);
	  $selected_period = strtoupper($matches[1]);
	  $selids = explode(',', $matches[2]);
	  if (count($selids) > 0) $selected_id1 = $selids[0];
	  if (count($selids) > 1) $selected_id2 = $selids[1];
	  preg_match('/start=([0-9\- ]+)/', $row['uri'], $matches);
	  $selected_start = (count($matches) > 0 ? $matches[1] :'');
	  $ondash = $row['ondash'];
      $panel_found=true;
	}
}
if (!$panel_found) $panel_name='';
?>
<script type="text/javascript">
$(document).ready(function() {
<?php echo "\r\n".'var panels = ['.substr($varp,0,-1).'];'."\r\n\r\n";
echo 'var show_panel =' .($panel_found ? 'true':'false').";\r\n\r\n"; 
unset($varp); 
?>


/* graph functions */
    var post_url='';
	var data = [];
//    $.plot(graph, data, {});

    function save_enable()
	{
		var b = $('#graph_view').is(':visible') && $('#graphname').val().length > 0 ? false : true;
		$('#graph_save').attr('disabled',b);
	}
	function validate_prm()
	{
		var period=$('#graph_periodselect').val();
		var snpid=$('#graph_dataselect').val();
		var snpid2=$('#graph_dataselect2').val();
		if (period == '0') {
			$('div.history span.errormsg').html('Select a period');
			$('#graph_periodselect').focus();
			return false;
		}
		if (snpid == '0') {
			$('div.history span.errormsg').html('Select a data source');
			$('#graph_dataselect').focus();
			return false;
		}

		if (snpid2 != '0') {
			snpid += ',' + snpid2;
		}

		//$('#diag').html('async.php/get_graphdata/' +period.toLowerCase() +'?snpids='+ snpid);
		return  'async.php/get_graphdata/' +period.toLowerCase() +'?snpids='+ snpid;
	}
	$('#viewgraph').on('click', function(e) {
		
		$('span.errormsg').html('');
		var url = validate_prm();
		if (url == false) return false;
		$('#graph_view').show();
		$('#graph_dataview').hide();
		save_enable();

       //todo this needs to add &start= when we use adv selection 

		show_history_graph(url, '#graph_view');
		return false;
	}
	); //viewgraph


	$('#graph_save').on('click', function(e) {
		$('#graphname_p').val($('#graphname').val());
		var url=validate_prm();
		if (url == false) return false;
		$('#graphurl_p').val(url);
		$('form#graph_saveform').submit();
		return false;
	}
	);

	$('form#graph_saveform').submit( function() {
		return true;
	}
	); 
	$('#viewgraphdata').on('click', function(e) {
		$('div.history span.errormsg').html('');
		var period=$('#graph_periodselect').val();
		var snpid=$('#graph_dataselect').val();
		if (period == '0') {
			$('div.history span.errormsg').html('Select a period');
			$('#graph_periodselect').focus();
			return false;
		}
		if (snpid == '0') {
			$('div.history span.errormsg').html('Select a data source');
			$('#graph_dataselect').focus();
			return false;
		}

		$('#graph_view').hide();
		$('#graph_data').empty();
		$('#graph_dataview').show();
		save_enable();
        function on_success(ginfo) {
			$('#graph_data').append(ginfo);
		}
        
        $.ajax({
            url: 'async.php/get_graphdata_raw/' +period.toLowerCase() +'/'+ snpid,
            method: 'GET',
            dataType: 'html',
			statusCode: {403: function() {window.location.href = 'index.php?timedout=1';}}, // 403 is NO session
            success: on_success
        });
		return false;
	}
	);

	$( "#graphname" ).autocomplete({
		source: panels,
		change: function(e, ui) {
			save_enable(); 
		},
		select: function(e, ui) {
    		var t = ui.item.value.trim();
			if (t && t.length && $.inArray(t,panels) != -1) {
				document.location.href =$('#history_refresh').parent().attr('href') + t;
				return false;
			}
		},
		minLength:0
	});

	$("input + a > span.dnauto").on('click', function(e) {
		// close if already visible
		e.stopPropagation();
		var o = $(e.target).parent().prev('input');
		if ( o.autocomplete( "widget" ).is( ":visible" ) ) {
			o.autocomplete( "close" );
			return false;
		}
		//This option displays all results
		//$("#tags").autocomplete( "option", "minLength", 0 );
		o.autocomplete("search","");
		//This option displays results based on the input content
		//o.autocomplete("search"); 
		o.focus();

		return false;
	});

    $('#history_refresh').on('click', function(e) {
		document.location.href =($(this).parent().attr('href') + $('#graphname').val());
		return false;
		}
	);
	if (show_panel)	$('#viewgraph').trigger('click');
	save_enable();
});
</script>

<div class="clearall"></div>
<div class="preamble"> <!-- preamble -->
<p>History lets you look at graphs that tells you what's been happening.
</p>
<div id="diag"></div>
</div > <!-- preamble -->

<div class="askmimo history" style="min-height:38em;margin:0 2ex 0 2ex;">
<H2>Configure graph</H2>
<br />
<table><tbody class="dialog">


<tr><td style="width:15ex;">Graph Name:</td><td><div class="ui-widget"><input type="text" size="20" id="graphname" value="<?= $panel_name;?>" /><a href="#" ><span  class="dnauto">&#9660;</span></a></div></td><td>
<a href="index.php?page=askmimo&amp;id=4&amp;panel_name="><input style="margin-left:9ex;;" type="image" name="refresh" id="history_refresh" src="images/refresh.png" tabindex="-1"  /></a></td></tr>
<tr><td style="width:15ex;">Select a period:</td><td>
<select id="graph_periodselect" >
<option value="0">&lt;Select...&gt;</option>
<?php 
$a = array('DAY' => 'Last Day', 'WEEK' => 'Last Week' , 'MONTH' => 'Last Month');
foreach($a as $k => $v) {
  echo sprintf('<option value="%s"%s>%s</option>'."\r\n", $k, ($k==$selected_period ? ' selected': ''), $v);
}
?>
</select></td><td></div></td></tr>
<tr><td style="width:15ex;">Select data source:</td><td>
<select id="graph_dataselect">
<option value="0">&lt;Select...&gt;</option>
<?php
$query="select distinct sp.id, sp.user_name as what, sp.units, npt.name as typename, npt.node_point_cat, a.name as area_name from site_node_points sp inner join site_nodes sn on sp.site_node_id = sn.id  inner join areas a on sn.area_id = a.id inner join node_points np on sp.node_point_id = np.id inner join node_point_types npt on np.node_point_type_id =  npt.id ";
$query .=  "where sn.site_id = $siteid and np.node_point_type_id != 17 ".
           "and npt.node_point_cat in ('Sensor', 'Input') ".
           "order by a.name, sp.id;";
$res =  db_query($query);
$carea='';
$cblist='';
while ($res && $r=db_fetch($res)) {
  if ($carea != $r['area_name']) {
    if ($carea!='') {$cblist .= '</OPTGROUP>';}
    $cblist .= '<OPTGROUP label="'.$r['area_name'].'">';
    $carea=$r['area_name'];
  }
  $cblist .= '<option value="'.$r['id'].'"'.
  ($r['id']==$selected_id1 ? ' selected1': '').
  ($r['id']==$selected_id2 ? ' selected2': '').
  '>'.  $r['what'].' ( '.$r['typename'].' )'.'</option>'."\r\n";
  
}
if ($carea!='') {$cblist .= "</OPTGROUP>\r\n";}
echo str_replace(array('selected1','selected2'),array('selected',''),$cblist);
?>
</select>
</td><td></td></tr>
<tr><td style="width:15ex;">Add a field:</td><td>
<select id="graph_dataselect2" >
<option value="0">&lt;Select...&gt;</option>
<?php 
echo str_replace(array('selected1','selected2'),array('','selected'),$cblist);
?>
</select>
</td><td></div></td></tr>
</tbody></table>
<br />
<div style="margin-left:61%;"><a href="#"><input style="" type="button" id="viewgraph" value="Show Graph"  /></a>
<a href="#"><input style="margin-left:3ex;float:none;" type="button" id="viewgraphdata" value="Show Data"  /></a></div>

<div><span class="errormsg" style="color:red;font-weight:bold;"></span></div>

<div id="graph_view" style="display:none;">
<div style="margin:2em 0;border-bottom: 1px solid #cccccc;"></div>
<div class="graph_wrapper"  style="margin: 0 auto; width:80%;">
<H2>View  graph</H2>
<div class="graph_holder" style="height:25em;width:100%;"></div>
</div> <!-- graph_wrapper -->
<p style="font-size:10pt;">
<form action="index.php?page=askmimo&amp;id=4" id="graph_saveform" method="post">
<input type="checkbox" <?=($ondash == '1' ? 'checked' : '');?> id="graph_dashboard" name="ondash" />Show this graph on Dashboard <span class="hilitered">Important</span>
<input type="hidden" name="panel_name" id="graphname_p" />
<input type="hidden" name="uri" id="graphurl_p" />
<div style="display:none;"><input  type="submit" name="update" id="graph_update" /></div>
</form>
</p>
</div> <!-- id="graph_view" -->
<br />




<div id="graph_dataview" style="display:none;">
<div style="margin:2em 0;border-bottom: 1px solid #cccccc;"></div>
<div style="margin: 0 auto; width:80%;">
<H2>View  data</H2>
<p style=""></p>
<div id="graph_data">
<!-- filled in by ajax -->
</div> <!-- id="graph_data" -->
</div>
</div> <!-- id="graph_dataview" -->

<div style="margin:1em 0;border-bottom: 1px solid #cccccc;"></div> 
<br />
<div style="margin:1em 0 4em 0;">
<a href="#"><input style="" type="button" id="advanced" value="Advanced Info."  /></a>
<div style="float: right; margin-right:10ex;">
<a href="#"><input style="" type="button" id="graph_save" value="Save"  /></a>
<?php /*<a href="#"><input style="margin-left:3ex;" type="button" id="cancel" value="Close"  /></a>*/ ?>
</div>
</div>
<br class="clearboth" />
</div> <!-- askmimo history -->
