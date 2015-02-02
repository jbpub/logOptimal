<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<noscript>
        <zzMETA HTTP-EQUIV=Refresh CONTENT="0; URL=noscript.html">
</noscript>




<link rel="stylesheet" href="ui/css/custom-theme/jquery-ui-1.8.20.custom.css" />
<link rel="stylesheet" href="ui/combobox.css" />
<link href="style.css" type="text/css" rel="stylesheet" />
<?php 
?>

<script type="text/javascript" src="scripts/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="ui/js/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="ui/combobox.js"></script>
<script type="text/javascript" src="scripts/script.js"></script>

<script >
//.autocomplete( "search" , [value] )
/* demo */
$(document).ready(function() {
var availableTags = [
			"ActionScript",
			"AppleScript",
			"Asp",
			"BASIC",
			"C",
			"C++",
			"Clojure",
			"COBOL",
			"ColdFusion",
			"Erlang",
			"Fortran",
			"Groovy",
			"Haskell",
			"Java",
			"JavaScript",
			"Lisp",
			"Perl",
			"PHP",
			"Python",
			"Ruby",
			"Scala",
			"Scheme"
		];
		$( "#tags" ).autocomplete({
			source: availableTags,
			   change: function(e, ui) { alert('changed');},
			minLength:0
		});
		$("input + a > span.dnauto").on('click', function(e) {
			// close if already visible
			var o = $(e.target).parent().prev('input');
			if ( o.autocomplete( "widget" ).is( ":visible" ) ) {
				o.autoomplete( "close" );
				return;
			}
			//This option displays all results
			//$("#tags").autocomplete( "option", "minLength", 0 );
			o.autocomplete("search","");
			//This option displays results based on the input content
			//o.autocomplete("search"); 
			o.focus();
			
			return false;
		});
		
		$("input + a > span.dncat").on('click', function(e) {
			// close if already visible
			var o = $(e.target).parent().prev('input');
			if ( o.catcomplete( "widget" ).is( ":visible" ) ) {
				o.catcomplete( "close" );
				return;
			}
			//This option displays all results
			//$("#tags").autocomplete( "option", "minLength", 0 );
			o.catcomplete("search","");
			//This option displays results based on the input content
			//o.autocomplete("search"); 
			o.focus();
			
			return false;
		});
		$.widget( "custom.catcomplete", $.ui.autocomplete, {
			_renderMenu: function( ul, items ) {
			var self = this, currentCategory = "";
			$.each( items, function( index, item ) {
				if ( item.category != currentCategory ) {
					ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
					currentCategory = item.category;
				}
				self._renderItem( ul, item );
			});
		}
		});

var data = [
			{ label: "anders", category: "" },
			{ label: "andreas", category: "" },
			{ label: "antal", category: "" },
			{ label: "annhhx10", category: "Products" },
			{ label: "annk K12", category: "Products" },
			{ label: "annttop C13", category: "Products" },
			{ label: "anders andersson", category: "People" },
			{ label: "andreas andersson", category: "People" },
			{ label: "andreas johnson", category: "People" }
		];
		
		$('#graph_periodselect').autocomplete({
			minLength:0,
			source: [
			{value: "DAY",   label: "Last Day"},
			{value: "WEEK",  label: "Last Week"},
			{value: "MONTH", label: "Last Month"}
			]
		});
		$( '#graph_periodselect' ).css(
		 { 'border':'none', 'border-bottom': '1px dotted #cccccc', 'width': '15em'});
		$( "#search" ).catcomplete({
			//delay: 0,
			minLength:0,
			source: data
		});

		$( "#combobox" ).combobox();
});

</script>
</head>
<body style="">


<div style="">
<style>
.ui-widget {
 font-size:0.85em;
}
.ui-autocomplete {
	max-height: 25em;
	overflow-y: auto;
	/* prevent horizontal scrollbar */
	overflow-x: hidden;
	/* add padding to account for vertical scrollbar 	padding-right: 20px;
*/
	color: #404040;
	padding: .2em .4em;
/*	padding-left: 10ex;
	width: 40ex;*/
}
.ui-widget-content a { 
	color: #404040; 
}
.ui-autocomplete .ui-menu-item {
	/*padding-left: .4em;
*/
}

.ui-autocomplete-category {
	font-weight: bold;
	/*padding: .2em .4em;*/
	color: #0082CD;
	padding: .2em .4em, .2em, 1px;
	margin: .8em 0 .2em;
	line-height: 1.5;
}
#tags { border-color: #0082CD; border-radius: 5px;}
#search { border:none; border-bottom: 1px dotted #cccccc; width: 15em;}
</style>


<div class="ui-widget">
	<label for="tags">Tags: </label>
	<input id="tags"   /><a href="#"><span id="dnclk" class="dnauto">&#9660;</span></a>
</div>

</div>


<div class="ui-widget">
	<label for="search">Search: </label>
	<input id="search" value="xxxxxxxxxxxxx" readonly /><a href="#"><span  class="dncat">&#9660;</span></a>
</div><!-- End demo -->



<div class="ui-widget">
	<label >Select: </label>
	<input id="graph_periodselect" readonly /><a href="#"><span  class="dnauto">&#9660;</span></a>
</div>

<div class="demo">
<div class="ui-widget">
<label>Your preferred programming language: </label>
<select id="combobox">
<option value="">Select one...</option>
<option value="ActionScript">ActionScript</option>
<option value="AppleScript">AppleScript</option>
<option value="Asp">Asp</option>
<optgroup label="xxxxx">
<option value="BASIC">BASIC</option>
<option value="C">C</option>
<option value="C++">C++</option>
</optgroup>
<option value="Clojure">Clojure</option>
<option value="COBOL">COBOL</option>
<option value="ColdFusion">ColdFusion</option>
<option value="Erlang">Erlang</option>
<option value="Fortran">Fortran</option>
<option value="Groovy">Groovy</option>
<option value="Haskell">Haskell</option>
<option value="Java">Java</option>
<option value="JavaScript">JavaScript</option>
<option value="Lisp">Lisp</option>
<option value="Perl">Perl</option>
<option value="PHP">PHP</option>
<option value="Python">Python</option>
<option value="Ruby">Ruby</option>
<option value="Scala">Scala</option>
<option value="Scheme">Scheme</option>
</select>
</div>

</div><!-- End demo -->



</body>
</html>
