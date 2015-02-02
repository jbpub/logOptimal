/* vim: set ai sw=4 ts=4: */
/*
* rcsid : $Id$
*/

photo_dir='photos/';

function stripslashes(str) {
    str=str.replace(/\\'/g,'\'');
    str=str.replace(/\\"/g,'"');
    str=str.replace(/\\0/g,'\0');
    str=str.replace(/\\\\/g,'\\');
    return str;
}

function onclick_remline(event)
{
	if ($('.textentry_popup').length > 0) {
		return true;
	}

	var l_time = $(this).children('.rem_time').text();
	var l_text = $(this).children('.rem_text').text();
	l_text=l_text.replace(/\"/g,"&quot;");
	var btndlg = '<div class="textentry_popup"><label for="textentry">' 
		+ l_time + ' </label><input type="text" size="75" name="textentry" id="textentry" value="'+l_text+'" /><a href="#" id="okbutton" class="button">OK</a>&nbsp;<a href="#" id="delbutton" class="button">Delete</a><a href="#" id="xbtn"><img src="images/close.png" alt="Close"></a></div>';
	$(this).append(btndlg);

//	$('#textentry').val(l_text);
	$('#textentry').focus();


	$('.textentry_popup').on('click','#okbutton', function(event){
		var rl=$(this).parent().parent();
		rl.addClass("modified");
		var txt=$(this).prev().val();
		txt=txt.replace(/\"/g,"&quot;");

		rl.children('.rem_text').html(txt);
		var tim = rl.children('.rem_time').text();
		//var hinp='<input type="hidden" name="'+tim+'" />'; 
		var hinp='<input type="hidden" name="reminder['+tim+']" />'; 
		rl.children('input').remove();
		rl.prepend(hinp);
		rl.children('input').attr('value',txt);
		$(this).parent().remove();
		$('#save').show();
		return false;
	});
	$('.textentry_popup').on('click','#delbutton', function(event){
		var rl=$(this).parent().parent();
		rl.addClass("modified");
		rl.children('.rem_text').text('');
		var tim = rl.children('.rem_time').text();
		rl.children('input').remove();
		rl.prepend('<input type="hidden" name="reminder['+tim+']" value="--deleted--" />');
		$(this).parent().remove();
		$('#save').show();
		return false;
	});

	$('.textentry_popup').on('click','#xbtn', function(event){
		$(this).parent().remove();
		return false;
	});
	$('.textentry_popup').on('keypress keydown blur',  function(e){
		//console.log("keypress 1 - " + 'keycode=' + e.keyCode + '  which='+ e.which +' type='+e.type );
		if (e.type=='keypress')
		switch (e.which) {
		case 13:
			$('#okbutton').trigger('click');
			return false;
		default:
			break;
		}
		else if (e.type=='keydown') switch (e.which) {
		case 32:
		if (e.target.className == 'button') {
			$(e.target).trigger('click');
			return false;
		}
		break;
		case 27:
			$(this).remove();
			return false;
		}
		else {
			$(this).remove();
			return false;
		}

		return true;
	});


//		e.stopPropagation();

	return false;
}


/*
 *  PHOTO uploads
 */

function createUploader(o){            
    var uploader = new qq.FileUploader({
        element: o,
        action: 'index.php/upload',
        multiple: false,
        allowedExtensions: new Array( "jpg","jpeg","gif","png", "bmp" ),
        sizeLimit: 5 * 1024 * 1024,   
		showMessage: function(message){
				$('#file-uploader-msgholder').show();
				$('#file-uploader-msg').append(message + '<br />');
			},
        debug: false
    });           
/*
        params: {},
        button: null,
        multiple: true,
        maxConnections: 3,
        // validation        
        allowedExtensions: [],               
        sizeLimit: 0,   
        minSizeLimit: 0,                             
        // events
        // return false to cancel submit
        onSubmit: function(id, fileName){},
        onProgress: function(id, fileName, loaded, total){},
        onComplete: function(id, fileName, responseJSON){},
        onCancel: function(id, fileName){},
        messages: {
        },
		onComplete : function(id, name, response) {
				$('#file-uploader-msg').empty();
			},
		showMessage: function(message){
			$('#file-uploader-msg').append('<pre>'+message+'</pre>');
		
		}
*/
}

function ping()
{
	$.ajax(
		{url:"index.php",
		type:"get",
		async:true,
		dataType: "json",
		data:{pingsession : '1'},
	});
}

function onclick_thumb (e) {
	var t = $(this).children(':first')[0].src;
	var tbsix =t.lastIndexOf('_TB_');
	//var p1 = t.substr(0,tbsix);
	var p2 = t.substr(tbsix+4);
	var newextix = p2.indexOf('_');
	var newext = p2.substr(0,newextix);
	p2 = p2.substr(newextix+1);
	p2 = p2.substr(0, p2.length-3);
	var o = $('#photo_image a');
	o.children().remove();
	o.append('<img src="photos/'+p2+newext+'" title="click to view" alt="missing image" />');
	$('.thumb').removeClass("selected");
	$(this).addClass("selected");
	$('#photo_image').show();
	document.location.hash="imgtop";
	ping();

}

function onclick_photo(e)
{
	$(this).show();
	var aImage = new Image();
	var im= $('#photo_image img');
	aImage.src = im[0].src
	//css('background-image').replace(/"/g,"").replace(/url\(|\)$/ig, "");

	var width;
	var height;
	aImage.onload = function() {
    width = this.width;
    height = this.height;
//	var width = aImage.width;
//	var height = aImage.height;
	if (width < window.innerWidth)
		width =  window.innerWidth;
	if (width < 40) width=40;
	if (height <= 0) height=40;

	var html  = '<div id="photo_popup"><div id="photo_body" ' +
	//'style="background-image: url('+"'"+ aImage.src+"'); " +
	'style="' +
	'width:'+width+'px;' +
	'height:'+height+'px;' +
	'" title="click to return to mimo photos">' +
	'<img src="'+
	aImage.src +
	'" />'+
	'</div></div>';
	$('#container').children().hide('slow');
	$('#container').prepend(html);
	$('#photo_popup').on('click', function(e) {
		$('#container').children().show();
		$(this).remove();
		document.location.hash="imgtop";
	});
	ping();
	}
}


/*
 *  The Add new node
 */
function onclick_discover(e)
{
	var t=null;

	function onclick_newnode_confirm (e) 
	{
		$('.newnode .step1 input#confirm').off('click');

		if (typeof t != 'null') clearInterval(t);

		var s =  'index.php?page=toolbox&id=1&macid=' + 
			$('.nodelist').html().replace(/[\r\n\t ]/g,'').replace(/<br.*/g,'');
		document.location.href = s;
		/*$('.newnode .step1 input#confirm').attr('disabled',true);
		$('.newnode .step1 .left span').html('Completed');
		$('.newnode .step1 .left span').css('color','#3C8C32');
		$('.newnode .newnode .step2 input').attr('disabled',false);*/
		return false;
	}

	function showcountdown()
	{
		var secs = 120;
						
		var txt = '<span class="progresstext">Node discovery in progress. 120 seconds remaining.</span>';
		$('#discover_cnt').html(txt);
		$('.nodelist').html('');
		var found_one=false;
		$('.newnode .step1 .left span').html('<img src="images/pending.png" />');

		var lastcall=0;

		t = setInterval( function() { 
			secs -= 1;
			if (secs > 0 ) {
				if (!found_one && secs % 10 == 0  && secs < 115) {
					var now = new Date().getTime();
					if ((now - lastcall) >= 10000) {
						$.ajax({
						url: 'async.php/joined_nodes',
						type: 'get',
						dataType: 'json',
						success: function(data,textStatus,jqXHR) {
							lastcall = new Date().getTime();
							if (data &&	data.length > 0 && data[0].length>10) {
								found_one=true;
								$('.nodelist').html(data[0] + '<br />');
							}
						//	var items = [];
						//	$.each(data, function(key, val) {
						//		items.push(val + '<br />');
						//		});
						//		$('.nodelist').html(items.join(''));
							}
						});
					}
				}
				$('#discover_cnt').html(txt.replace(/\d+/,secs));
				if (found_one) {
					$('.newnode .step1 input#confirm').attr('disabled',false);
					$('.newnode .step1 input#confirm').on('click', onclick_newnode_confirm); 
				}
			}
			else /* secs expired and */ {
				clearInterval(t);
				t=null;
				if (found_one) {
					$('#discover_cnt').html('Refreshing network ...');
					// refresh updates site_nodes et al
					$.ajax({
						url: '/cgi-bin/qvmc/refreshzigbee',
						type: 'get',
						dataType: 'text',
						async : false,
						success: function(data,textStatus,jqXHR){
						}
					});
					$('#discover_cnt').html('<span class="donetext">Discovery completed</span>');
				//	$('.newnode .step1 input#confirm').attr('disabled',false);
				//	$('.newnode .step1 input#confirm').on('click', onclick_newnode_confirm); 
				}
				else {
					$('#discover_cnt').html('No nodes discovered.');
					$('#discover').attr('disabled',false);
					$('.newnode .step1 input#confirm').attr('disabled',true);
					$('.newnode .step1 .left span').html('<img src="images/incomplete.png" />');
				}
			}
		}, 1000 );

	}


	$('.newnode .step2 input').attr('disabled',true);
	$('.newnode .step3 input').attr('disabled',true);
	$('#discover').attr('disabled',true);
//		$('#allow_node_discovery .form_button .form_button_label').html('Stop Node Discovery');
//		$('#allow_node_discovery').live('click',function(){
//			seconds = 0;
//			$('#allow_node_discovery .form_button .form_button_label').html('Allow Node Discovery');
//			$('#allow_node_discovery').live('click', function(){
//				allowNodeDiscovery();
//			});	
//		});
		//Start the Join Procedure and countdown
	$.ajax({
		url: '/cgi-bin/qvmc/zigbeejoin',
		type: 'get',
		dataType: 'text',
		success: function(data,textStatus,jqXHR){
			if (data.indexOf("join mode now") == -1) {
				$('#discover_cnt').html('<span class="errortext">qvmanager is not functioning.</span>');
				$('#discover').attr('disabled',false);
			}
			else {
			  showcountdown();
			}
		}
	});


	return false;
}


/*
 *	rotates the spin buttons
 */
function rotate_spinh(e) {
	var up = this.value.charCodeAt(0) == 9650 ? false : true;
	var ids = $(this).attr('data').split(',');
	var tgt= $('#'+ ids[0]);
	var data= tgt.attr('data').split(',');
	var ix = data.indexOf(tgt.text());
	if (up) ix--; else ix++;
	if (ix < 0) ix = data.length -1;
	if (ix >= data.length) ix=0;
	tgt.text (data[ix]);  // sets the target field
	var i=0;
	for (i=1; i < ids.length; i++) {
		//optionally set post fields
		 $('#'+ ids[i]).attr('value',(data[ix]));
	}
	return false;
}


/* end newnode */


/* the graph plotting */
function show_history_graph(url, graph_id) 
{
	
	function on_success(g) {

		if (g.length <= 0 || g[0].count == 0) {
			$(graph_id).hide();
			if (save_enable) save_enable();
			return;
		}
		$(graph_id +' .graph_wrapper > H2').html(g[0].heading);
		var markings = [ 
			{ yaxis: {from : 0, to: 0 },color: "#cccccc" ,lineWidth:1},
			{ yaxis: {from : 1, to: 1  },color: "#cccccc" ,lineWidth:1}
		];
		var options = {
			lines: { 
				show: true,
				steps: false 
			},
			xaxis: {
				mode: "time",
				timeformat: "%H:%M%p %d%b%y",
				ticks: 5,
				tickLength: 5,
				tickColor: "#800000",
				min: g[0].xminval,
				max: g[0].xmaxval
			},
			yaxis: {
				tickLength :5,
				tickColor: "#800000"
			},
			yaxes:[] , 
			grid: {
				color: "#999999",
				show: true,
				borderWidth: 1,
				borderColor: "#cccccc"
			},
			shadowSize: 0
		};
		
		var data = [];
		var cat_1st=null;
		var inp_1st=-1;
		var cat_cnt=0;
		var yaxis_i, yaxis_s;
		for (var i = 0; i < g.length; i++) {
			if (g[i].category != cat_1st) cat_cnt++;
			if (cat_1st == null) cat_1st=g[i].category;
			if (inp_1st == -1 && g[i].category == 'Input') inp_1st=i;
		}
		//we only ever display 2 yaxes at most (for the moment)
		if (cat_cnt > 1) {
			if (cat_1st == 'Sensor') {options['yaxes'].push({});yaxis_s = 1;yaxis_i=2;}
			options['yaxes'].push({position: "left", min: -.2, max: 1.3 ,
				ticks : [[ 0, g[inp_1st].bottomlabel], [1, g[inp_1st].toplabel]],
				tickLength :5}
				);
			if (cat_1st != 'Sensor') {options['yaxes'].push({});yaxis_s = 2;yaxis_i=1;}
		}
		else /* cat_cnt =1 */if (cat_1st == 'Input') {
			options.yaxis = {
				tickLength :5,
				tickColor: "#800000",
				position: "left", min: -.2, max: 1.3 ,
				tickLength :5,
				ticks : [[ 0, g[0].bottomlabel], [1, g[0].toplabel]]
			};
		}
			

		for (var i = 0; i < g.length; i++) {

			var o = { data: g[i].data, label: g[i].heading };
			if (g[i].category == 'Input') {
				if (i == 0) {
					options.grid.markings = markings;
				}
				o.lines = { steps: true };
				if (yaxis_i != -1) o.yaxis = yaxis_i;
			}
			else { // Sensor
				o.lines = {steps: false };
				if (yaxis_s != -1) o.yaxis = yaxis_s;
			}

			data.push(o);
		}
		
		// plot
		var graph = $(graph_id+' .graph_holder');
		$.plot(graph, data, options);
		post_url = url;
	}


	$.ajax({
		url: url,
		method: 'GET',
		dataType: 'json',
		statusCode: {403: function() {window.location.href = 'index.php?timedout=1';}}, // 403 is NO session
		success: on_success
	});
}

$(document).ready(function() {


//				$('.newnode .step1 input#confirm').on('click', onclick_newnode_confirm); 
// reminder text entry
	$('.rem_line').on('click', onclick_remline);

// photos
	var t = $('#file-uploader');
	if (t.length) {
		createUploader(t[0]);
		$('#file-uploader-msgholder').hide();
		$('#file-uploader-msg').empty();
	}

	$('.thumb').on('click', onclick_thumb);

	$('.photo .thumbholder .photoscroll > a').on('click', function(e) {
		var im= $('#photo_image img')[0].src;
		var ix = im.lastIndexOf('/photos/');
		if (ix < 0) return true;
		ix+=7;
		var ixcur = $(this)[0].href.indexOf('&curimg');
		var s = $(this)[0].href; 
		s=s.replace(/#.*$/,'');
		if (ixcur > 0) {
			s = s.substr(0,ixcur); 
		}
		$(this)[0].href = s + '&curimg=' + im.substr(1+ix) +'#imgtop';
		return true;
	}
	);


	$('#photo_image  a').on('click', onclick_photo);


	if (document.location.href.indexOf('&curimg') > 0)
		$('#photo_image').show();

	$('#discover').on('click', onclick_discover);

	$('.newnode .step2 input#confirm2').on('click', function(e) {
		$('#p_area').attr('value',$('#area').html().trim());
		$('#p_location').attr('value',$('#location').html().trim());
		$('#p_zigb_rep1').attr('value',$('#zigb_rep1d').html().trim());
		$('#p_zigb_rep2').attr('value',$('#zigb_rep2d').html().trim());
		$('.step2 .left img').attr('src','images/completed.png');
		$('#confirm3').attr('disabled',false);
		return false;
		}
	); 
	$('.newnode .step3 input#confirm3').on('click', function(e) {
		//todo validate
		$('.step3 .right .upd').each(function(i,el) {
			$('#p_'+el.id).attr('value',$(el).html().trim());
			}
		);
		$('.newnode form#updnode').submit();
		return false;
		}
	); 
	$(/*.newnode*/ 'form#updnode').submit( function() {return true;}); 

	$('#managenode_save').on('click', function(e) {
		$('#p_area').attr('value',$('#area').html().trim());
		$('#p_location').attr('value',$('#location').html().trim());
		$('#p_zigb_rep1').attr('value',$('#zigb_rep1d').html().trim());
		$('#p_zigb_rep2').attr('value',$('#zigb_rep2d').html().trim());
		$('#p_is_active').attr('value',($('#is_active').hasClass('on')?'1':'0'));
		$('.step3 .right .upd').each(function(i,el) {
			$('#p_'+el.id).attr('value',$(el).html().trim());
			if (el.id.indexOf('is_active')!=-1) {
				$('#p_'+el.id).attr('value', ($(el).hasClass('on')?'1':'0'));	
			}
			}
		);
		$('.managenode form#updnode').submit();
		return false;
		}
	); 

	$('.spinh input[type="button"]').on('click',  rotate_spinh);


	/* begin edible */
	$('span.edible').attr('title', 'Edit');
	
	function onlick_edible(e) {
		var t= $(e.target);
		t.off('click');
		var txt= t.html();
		var w=t.css('width');
		t.html('');

		t.append('<input type=text value="'+txt+'" style="width:'+w+'" />');
		t.addClass('edibleinput');
		t.removeClass('edible');
		t.attr('title', '');
		var c= t.children().first();
		c.focus();
		t.focusout(onfocusout_edibleinput);
		return false;
	}

	$('span.edible').on('click', onlick_edible);
	
	function onfocusout_edibleinput(e) {
		var p= $(this); //span
		p.off('focusout',onfocusout_edibleinput);
		var t = p.children().first();
		p.addClass('edible');
		p.removeClass('edibleinput');
		p.html(t.val());
		p.attr('title', 'Edit');
		p.on('click', onlick_edible);
		return true;
    }
	/* end edible */

	/* the state graph config */



    $('.config_sensor').on('click', function(event) {
			//        img   a       div      div
			$('.config_sensor').css('visibility','hidden');
			var t= $(this).parent().parent().parent();
			t.children('.state_sensors').show();
			// position
			var o=t.children('.state_sensors').offset();
			var cmax = $('#content').offset().top + $('#content').height();
			var mh = t.children('.state_sensors').first().height(); 
			if (o.top <  $(window).scrollTop()) o.top = $(window).scrollTop(); 
			if (o.top + mh > cmax) o.top = cmax - mh;
			t.children('.state_sensors').first().offset({ top: o.top, left: o.left});
			// get data
			var h=t.children('input[type="hidden"]');
			var openstate = h.first().val();
			var closestate = h.first().next().val();
			var rev = h.last().val();

			// set data
			var c = t.find('div.state_graph div.state_circle');
			//var xx =	c.last();
			if (rev != '0') { //change
				c.first().addClass('green');
				c.first().removeClass('white');
				c.last().addClass('white');
				c.last().removeClass('green');
			}
			else {
				c.first().addClass('white');
				c.first().removeClass('green');
				c.last().addClass('green');
				c.last().removeClass('white');
			}
			c = t.find('div.state_sensors div span.edible');
			c.first().html(openstate);
			c.last().html(closestate);
			return false;
		}
	);

	$('div.state_graph div.state_circle').on('click', function(e) {
		var tgt = $(e.target);
		if (tgt.hasClass('green')) return false;
		var p =	tgt.parent().children('.state_circle');
		p.toggleClass('green');
		p.toggleClass('white');
	    return false;
	    }
    );

    $('.state_close').on('click', function(event){
        var t = $(this).parent().parent();
        var rev = t.find('div.state_graph div.state_circle').first().hasClass('green') ? 1 : 0;//change
        var c = t.find('div.state_sensors div span.edible');
	    c.off('focusout',onfocusout_edibleinput);
        var openstate = c.first().html();
        var closestate = c.last().html();
	    t=t.children('input[type="hidden"]');
	    t.first().val(openstate);
	    t.first().next().val(closestate);
		t.last().val(rev);
		t=t.last().next().next();
		t.text(openstate);
		t.next().text(closestate);
		$('.config_sensor').css('visibility','visible');
		$('.state_sensors').hide();
        return false;
   	    }
	);
	/* end - the state graph config */

    $('div.askmimo.actuators td > div.clickable').on('click', function(event) {
		$(this).toggleClass('on off');
		return false;
		}
	);
    $('#area_list').on('change', function(event) {
		$('#area_refresh').trigger('click');
		return false;
		}
	);

    $('#area_refresh').on('click', function(event) {
		document.location.href =($(this).parent().attr('href') + '&a='+ $('#area_list').val());
		return false;
		}
	);
    $('#node_list').on('change', function(event) {
		$('#node_refresh').trigger('click');
		return false;
		}
	);

    $('#node_refresh').on('click', function(event) {
		document.location.href =($(this).parent().attr('href')+ '&a='+ $('#area_list').val() + '&n='+ $('#node_list').val());
		return false;
		}
	);
	$('.tickcross').on('click', function(e) {
		$(this).toggleClass('on');
		$(this).toggleClass('off');
		return false;
		}
	);
}); /* ready */
