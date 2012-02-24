//FOR THE PROGRESS PAGE
function onlyNumbers(e,dots){
var keynum
var keychar
var numcheck

if(window.event) // IE
{
keynum = e.keyCode
}
else if(e.which) // Netscape/Firefox/Opera
{
keynum = e.which
}
keychar = String.fromCharCode(keynum);
if(dots==0){
	numcheck = /\d/;
}else{
numcheck = /\d|\./
}
return numcheck.test(keychar)
}
	function loadInputBySplitID(sid){
		$("#InputTable").children().remove();
		$("#container").append("<p id='loading'>loading...please wait (or get faster internet)</p>");
		today = new Date();
		if (today.getMonth()+1<10){
    		month = "0"+eval(today.getMonth()+1);
    	} else {
    		month = today.getMonth()+1;
    	}
    	    	if (today.getDate()<10){
    		day = "0"+today.getDate();
    		}else{
    			day = today.getDate();
    		}
       	today = month+"/"+day+"/"+today.getFullYear();//FIX ALL THE DAYSS!!!!!!!!!!!!!!!!!!!!!!!!!!!!! AND REMOVE SOME OF THE TODAY FUNCTIONS
       	
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"loadInputExercise",
    				"SID":sid,
    				"date":today
				},
    				
    			success: function(r){
                 	$("#InputTable").append(r);
					$("#loading").remove();
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});    		
	}
	function loadExerciseOptions(selected){
		$("#exerciseoption").remove();
		$("#recordLine").append("<p id='loading'>Loading...</p>");
		$.ajax({
    		type: "POST",
    		url: "/db-interaction/gsprogress.php",
    		data: {
    			"action":"loadTrackExercise",
    			"SID":selected
				},
    				
    		success: function(r){
    			$("#loading").remove();
        		$("#selectandbutton").prepend(r);
			},
			error: function(){
			}			
		});
	}
       	var viewingDate;
	function loadRecordsByDate(get,date){
		$("#TrackTable").append("<div id='recordsbydate'><div id='recordleft'></div><div id='recordright'></div>");
		viewingDate = date;
		prevnextRecords(get,1);   
	}
	function prevnextRecords(get,prevnext){		
		$("#TrackTable").append("<p id='loading'>loading...please wait (or get faster internet)</p>");
		$.ajax({
    		type: "POST",
    		url: "/db-interaction/gsprogress.php",
    		data: {
    			"action":"loadPrevRecordByDate",
    			"prevnext":prevnext,
    			"uid":get,
    			"date":viewingDate
				},
    				
    		success: function(r){
        		$("#recordsbydate").append(r);//WHY!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! :( CLICK LEFT KEEPS GOING EVEN NO LOAD
        		if($("#recordsbydate table").height()<300){
        			$("#recordsbydate").height(300);//minimum height of 300
        		}else{
        			$("#recordsbydate").height($("#recordsbydate table").height());
        		}
        		$('#recordleft').height($('#recordsbydate').height());
    			$('#recordright').height($('#recordsbydate').height());
				$("#loading").remove();
				viewingDate = $("#recordsbydate table").attr("class");
				$(".day").removeClass("selected");
				$("div[date=\""+viewingDate+"\"]").addClass("selected");
			},
			error: function(){
			}			
		});
	}
	function loadExerciseTable(ListItemID,get){
		$("#generalbyExercise").remove();
		$.ajax({
    		type: "POST",
    		url: "/db-interaction/gsprogress.php",
    		data: {
    			"action":"loadGeneralExercise",
    			"startdate":"2010-09-09",
    			"enddate":"2012-09-09",
    			"uid":get,
    			"LID":ListItemID
				},
    				
    		success: function(r){
        		$("#tablebyExercise").append(r);
			},
			error: function(){
			}			
		});
	}
	function loadWeightTable(ListItemID,eName,get){
	//$("#trackbyExercise").append("<table id='byExerciseTable'></table><div id='pager'></div>");//INputTable is fucking hidden WTF was I doing..
	$('#byExerciseTable').jqGrid('GridUnload');	
	jQuery("#byExerciseTable").jqGrid({
   			url:'/db-interaction/gsprogress.php',
			datatype: "json",
			mtype: "POST",
   			colNames:['Date','Weight', 'Rep', 'Set','Rest','Notes'],
   			colModel:[
   				{name:'date',index:'date', width:100, sortable:false},
   				{name:'weight',index:'weight', width:90,align:"center",sortable:false},
   				{name:'rep',index:'weight asc, date', width:100,align:"center",sortable:false},
   				{name:'set',index:'amount', width:50, align:"center",sortable:false},
   				{name:'rest',index:'rest', width:50, align:"center",sortable:false},				
   				{name:'note',index:'note', width:300, sortable:false}		
   				],
   				rowNum:20,
   				rowList:[10,20,30],
   				pager: '#pager',
   				sortname: $("#sortbyCol").find(":selected").attr("value"),
    			viewrecords: false,
    			sortorder: $("#sortbyDESCASC").find(":selected").attr("value"),
    			height: 460,
    			caption:eName,
    			loadonce: false, // to enable sorting on client side
				sortable: false, //to enable sorting
    			postData: {
    				"action":"loadbyExercise",
    				"exercise":ListItemID,
    				"UserID":get
    				//startdate:function() {return startdateExercise}
				},
				loadComplete: function(){
					//startdateExercise = jQuery("#byExerciseTable").jqGrid('getGridParam','userData')['startdate'];
				}
		});
	jQuery("#byExerciseTable").jqGrid('navGrid','#pager',{edit:false,add:false,del:false});
	}

	function loadWeightByUserID(get){
		UID = get;//can delete hidden currentid
		$("#PhyTable").append("<form name='weightchartoptions' id='weightchartoptions'><div id='trendlineonoff'>Trendline</div><input type='checkbox' name='trendlinecheck' value='yes'/></form><div id='weightChart'></div><div id='weightstartfinal'><input id='weightstart' maxlength='10' size='7'/><input id='weightfinal' maxlength='10' size='7'/><input id='weightoptionsubmit' type=button value='change'/></div><div id='weightcontentheader' class='ui-corner-top ui-widget-header'><span class='ui-jqgrid-title'>Weight</span></div><div><table id='weightheader'><tr><td>Date</td></tr><tr><td>Weight</td></tr></table><div id='weightdiv'><table id='weightcontent'></table></div></div>");
		//,{placeholder:"_"}
		$("#weightstart").mask("99/99/9999");
		$("#weightfinal").mask("99/99/9999");
		$('#newWeightInput').keypress(function(event) {
        		return /\d/.test(String.fromCharCode(event.keyCode));
    	});

		datePickerController.createDatePicker({
 						formElements:{"weightstart":"m-sl-d-sl-Y"},
 						statusFormat:"l-cc-sp-d-sp-F-sp-Y",
 						finalOpacity:100, 
 						noFadeEffect:true, 
 						noDrag:true, 
 						fillGrid:true, 
 						constrainSelection:false 
					});
					datePickerController.createDatePicker({
 						formElements:{"weightfinal":"m-sl-d-sl-Y"},
 						statusFormat:"l-cc-sp-d-sp-F-sp-Y",
 						finalOpacity:100, 
 						noFadeEffect:true, 
 						noDrag:true, 
 						fillGrid:true, 
 						constrainSelection:false  
					});
					refreshoption(get);
	}
	var weightdata;
	var weightoption;
	function refreshdata(get){
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			dataType: "json",
    			data: {
    				"action":"loadweight",
    				"UID":get//LATER CHANGE THIS TO GET
				},
    				
    			success: function(r){
    				weightdata=r;
    				refreshtable(r); 
    				getmaxminweight(get);   				
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});  
	}
	var weightmaxmin;
	function getmaxminweight(get){
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			dataType: "json",
    			data: {
    				"action":"maxminweight",
    				"startdate":weightoption[3],
    				"enddate":weightoption[4],
    				"UID":get//LATER CHANGE THIS TO GET
				},
    				
    			success: function(r){
    				weightmaxmin=r; 				
    				plotGraph();
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});  
	}
	function refreshtable(r){
			var dates = new Array(),
			weights = new Array(),
			datestr = "<tr>",
			weightstr = "<tr>";
			optionstr= "<tr>";
		for (var i = 0; i<r.length; i++){
				dates.push(r[i][0]);
				weights.push(r[i][1]);
		}
		for (var i = 0; i<dates.length;i++){
			datestr=datestr+"<td date="+dates[i]+">"+dates[i].replace(/-/,' ').replace(/-20/g,' ')+"</td>";
		}
		datestr=datestr+"</tr>";
		for (var i = 0; i<weights.length;i++){
			weightstr=weightstr+"<td date="+dates[i]+">"+weights[i]+" lbs</td>";
			optionstr=optionstr+"<td date="+dates[i]+">"+"<div date=\""+dates[i]+"options\">"+"<div class='weightedit sp'></div><div class='weightdelete sp'></div></div></td>";
		}
		weightstr=weightstr+"</tr>";
		optionstr=optionstr+"</tr>";
		$("#weightcontent").children().remove();
		$("#weightcontent").append(datestr+weightstr+optionstr);//FIX THE WHOLE CLASS THING. USE ANOTHER ATTR AND MAKE j M Y WITHOUT HIVENS
		document.getElementById('weightdiv').scrollLeft = $("#weightcontent").width(); //WHY ARE WE BINDING IT THREE TIMES???!?!?!?!
		$("#weightcontent").find("td").each(function(){//TRY LiVE? SO DON"T HAVE TO BIND AGAIN LATER? RESEARCH ABOUT LIVE"
			$("#weightcontent").find("div[date="+$(this).attr('date')+"options]").hide();
			$(this)[0].onmouseover=function(){
				$("#weightcontent").find("div[date="+$(this).attr('date')+"options]").show();
				$("#weightcontent").find("td[date="+$(this).attr('date')+"]").addClass('weightcontenthover');
			};
			$(this)[0].onmouseout=function(){
				$("#weightcontent").find("div[date="+$(this).attr('date')+"options]").hide();
				$("#weightcontent").find("td[date="+$(this).attr('date')+"]").removeClass('weightcontenthover');
			};
		});
	}
	function refreshoption(get){
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			dataType: "json",
    			data: {
    				"action":"loadweightoption",
    				"UID":get//LATER CHANGE THIS TO GET
				},
    				
    			success: function(r){
    				weightoption=r;
    				refreshdata(get);
    				$("#weightstart").val(weightoption[3]);
    				$("#weightfinal").val(weightoption[4]);
    				datePickerController.setDateFromInput("weightstart");
    				datePickerController.setDateFromInput("weightfinal");
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});  
	}
	var trendy=false;

	function plotGraph(){//CHANGE IT TO DYNAMIC AND MOVE EVERYTHING TO PROGRESS>JS
		$("#weightChart").empty();//CHANGE ALL THE CHILDREN.REMOVE TO EMPTY
		//maxmin = String(weightdata).replace(/[0-9]{2}-[A-Za-z]{3}-[0-9]{4},/g, "")
		//alert(parseInt(maxmin));
		//maxmin.split(",");
		//alert(Math.max(127,128));
		var plot = $.jqplot('weightChart',  [weightdata],
			{ title:'Weight',
  				axes:{
  					xaxis:{renderer:$.jqplot.DateAxisRenderer,
  						min:weightoption[0],//NEED TO USE AJAX TO STORE/FIND MIN DATE
  						max:weightoption[1],
  						tickInterval:weightoption[2],
          					tickOptions:{
            					formatString:'%e&nbsp;%b&nbsp;%y'}},
  					yaxis:{label: "lbs", min:weightmaxmin[1]-1, max:weightmaxmin[0]+1}},
  					highlighter: {
        				show: true,
        				sizeAdjust: 7.5
      					},
  				series:[], seriesDefaults: {
        trendline: {
            show: trendy,
            color: '#666666'
        }
    }
			});//replot?
			
	}
	
	function initializeProgress(get){
		$("#recordleft").live("click",function(){//MAKE MORE EFFICIENT!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			$("#recordsbydate table").remove();//animate({width:'toggle'},350);
			prevnextRecords(get,0);
		});
		$("#recordright").live("click",function(){
			$("#recordsbydate table").remove();//animate({width:'toggle'},350);
			prevnextRecords(get,2);
		});
		$("input[name='trendlinecheck']").live("click",function(){//SET THIS UP LATER WHEN ALL FEATURES OF GRAPH HAS BEEN OUTLINED
			if ($("input[name='trendlinecheck']").attr("checked")==true){
				trendy=true;
				plotGraph();
		}else{
			trendy=false;
			plotGraph();
		}
	});
	$(".sameprev").live("click",function(){
		thisparent = $(this).parent().parent();
		thisprev = thisparent.find(".prevInputTable");
		thisparent.find(".weightInputTable").val(thisprev.attr('weight'));
		thisparent.find("select").val(thisprev.attr('lbkg'));
		thisparent.find(".repInputTable").val(thisprev.attr('rep'));
	});
	$(".samelast").live("click",function(){
		thisparent = $(this).parent().parent();
		if (thisparent.attr('list')==thisparent.prev().attr('list')){
			thisparent.find(".weightInputTable").val(thisparent.prev().find(".weightInputTable").val());
			thisparent.find("select").val(thisparent.prev().find("select").val());
			thisparent.find(".repInputTable").val(thisparent.prev().find(".repInputTable").val());
		}
	});
	$("#recordsubmit").live("click",function(){
		if(testdate($("#weightdate").val())==true){
		$(".recordtable").each(function(){
			var $whitelist = '<b><i><strong><em><a>',
    		weight = strip_tags(cleanHREF($(this).find(".weightInputTable").val()), $whitelist);//doesn't work. take away parseInt
    		rep = strip_tags(cleanHREF($(this).find(".repInputTable").val()), $whitelist);
    		//URLtext = escape(text);
    		if (weight.length>0 || rep.length>0){
    			if (testFloat(weight)==true && testInt(rep)==true){
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"recordsubmit",
    				"date":$("#weightdate").val(),
    				"lid":$(this).attr('list'),
    				"weight":$(this).find(".weightInputTable").val(),
    				"lbkg":$(this).find("select").val(),
    				"rep":$(this).find(".repInputTable").val(),
    				"pos":$(this).attr('rel')
				},
    				
    			success: function(){
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		}
	}
		});
		alert("Record saved.");//unsafe. Some records might not have been saved yet.
	}
	});
	$(".addnewset").live("click",function(){
		$(this).parent().parent().after("<tr class='recordtable' list=\""+$(this).parent().parent().attr('list')+"\" class=recordtable rel=\""+(parseInt($(this).parent().parent().attr('rel'))+1)+"\"><td colspan='2'></td><td><input class='weightInputTable' maxlength = '5' size='4' onkeypress='return onlyNumbers(event,1)' /><select ><option selected value='lbs'>lbs</option><option value='kg'>kg</option></select></td><td><input class='repInputTable' maxlength = '3' size='1' onkeypress='return onlyNumbers(event,0)' /></td><td></td><td><input/></td><td class='zeropadding'><input class='addnewset' type=button value=ad /></td></tr>");
		$(this).remove();
	});
	$("#addExerciseInputTable").live("click",function(){
		//check to make sure it's not empty
		if($("#newlbkg").find(":selected").attr("value")=='lbs'){
			newselectoption = "<select ><option selected value='lbs'>lbs</option><option value='kg'>kg</option></select>";
		}else{
			newselectoption = "<select ><option value='lbs'>lbs</option><option selected value='kg'>kg</option></select>";
		}
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"addnewset",
    				"date":$("#weightdate").val(),
    				"lid":$(this).attr('list'),
    				"weight":$(this).find(".weightInputTable").val(),
    				"lbkg":$(this).find("select").val(),
    				"rep":$(this).find(".repInputTable").val(),
    				"pos":$(this).attr('rel')
				},
    				
    			success: function(){
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		$("#somethingnewrow").before("<tr class='recordtable' rel='1'><td colspan='2'><input size='27' value=\""+$('#newExercise').val()+"\"/></td><td><input class='weightInputTable' maxlength = '5' size='4' onkeypress='return onlyNumbers(event,1)' value=\""+$('#newWeight').val()+"\"/>"+newselectoption+"</td><td><input class='repInputTable' maxlength = '3' size='1' onkeypress='return onlyNumbers(event,0)' value=\""+$('#newRep').val()+"\"/></td><td></td><td><input/></td><td class='zeropadding'><input class='addnewset' type=button value=ad /></td></tr>");
		$('#newExercise').val('');
		$('#newWeight').val('');
		$('#newRep').val('');
	})
		$(".weightdelete").live("click",function(){
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"deleteweight",
    				"date":$(this).parent().parent().attr('date')
				},
    				
    			success: function(){
    				refreshdata(get);
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		});
		$(".weightedit").live("click",function(){
			alert($(this).parent().parent().attr('date'));
		});
		$("#printbyExercise").live("click",function(){
			$.ajax({
    		type: "POST",
    		url: "/db-interaction/gsprogress.php",
    		data: {
    			"action":"printbyExercise",
    			"startdate":"2010-09-09",
    			"enddate":"2012-09-09",
    			"sidx":$("#sortbyCol").find(":selected").attr("value"),
    			"sord":$("#sortbyDESCASC").find(":selected").attr("value"),
    			"eName":$("#exerciseoption").find(":selected").text(),
    			"exercise":$("#exerciseoption").find(":selected").attr("value"),
    			"UserID":get
				},
    				
    		success: function(r){
    			//alert(r);
    			$("#tablebyExercise").append('<form id="super_form" method="POST" action="exceldownload.php"><input type="hidden" id="download" name="download" value=""/></form>');
    			$('#download').val(r);
				$('#super_form').submit();				
    		},
			error: function(){
			}			
		});
		})
		$("#viewbyExercise").live("click",function(){
			loadWeightTable($("#exerciseoption").find(":selected").attr("value"),$("#exerciseoption").find(":selected").text(),get);
			$("#tablebyExercise").show();
			$("#sortby").show();
			loadExerciseTable($("#exerciseoption").find(":selected").attr("value"),get);
		});
		$("#sortbyExercise").live("click",function(){
			loadWeightTable($("#exerciseoption").find(":selected").attr("value"),$("#exerciseoption").find(":selected").text(),get);
		})
		$("#daySelect").live("click",function(){
			if($(this).attr("disabled")!=1){
			$("#daySelect").attr("disabled",1);
			$("#recordsbydate").show();
			$("#trackbyExercise").hide();
			$("#progressSelector").hide();
			$("#inputLine").hide();
			$("#exerciseSelect").removeAttr("disabled");
		}
		})
		$("#exerciseSelect").live("click",function(){
			if($(this).attr("disabled")!=1){
			$("#exerciseSelect").attr("disabled",1);
			$("#recordsbydate").hide();	
			$("#trackbyExercise").show();
			$("#progressSelector").show();
			$("#ExerciseSel").show();
			$("#inputLine").show();	
			$("#recordLine").show();
			$("#recordsubmit").hide();
			$("#weightdate").hide();			
			$("#selectandbutton").show();
			$("#programoption").unbind();	
			$("#programoption").removeAttr("onchange").change(function(){fill_Splits ($("#programoption").find(":selected").attr("value"));loadExerciseOptions($("#splitoption").find(":selected").attr("value"))});
			$("#splitoption").unbind();
			$("#splitoption").removeAttr("onchange").change(function(){loadExerciseOptions($("#splitoption").find(":selected").attr("value"));});
			loadExerciseOptions($("#splitoption").find(":selected").attr("value"));
			
			$("#daySelect").removeAttr("disabled");
		}
		})
		$("#PhysiqueSelect").live("click", function(){
			if($(this).attr("disabled")!=1){		
			$("#PhysiqueSelect").attr("disabled",1);
			$("#progressSelector").hide();
			$("#recordLine").hide();
			$("#weightLine").show();
			$("#weightsubmit").show();
			$("#recordsubmit").hide();
			$("#TrackTable").hide();
			$("#InputTable").hide();
			$("#inputLine").show();
			$("#trackoptions").hide();
			$("#trackbyExercise").hide();
			$("#PhyTable").show();
			if (!$("#PhyTable").children().length){
				loadWeightByUserID(get);	
			}
			$("#weightdate").show();
			$(".date-picker-control").show();
			$("#TrackSelect").removeAttr("disabled");
			$("#RecordSelect").removeAttr("disabled");
		}
		});
		
		$("#RecordSelect").live("click", function(){//INSTEAD OF LOADING IT AGAIN, FIND A WAY TO STORE ALREADY LOADED INFO FIX!!!!!!!! PLEASE
			if($(this).attr("disabled")!=1){
			$("#RecordSelect").attr("disabled",1);
			$("#progressSelector").show();
			$("#recordLine").show();
			$("#weightLine").hide();			
			$("#weightsubmit").hide();
			$("#recordsubmit").show();
			$("#InputTable").show();
			$("#TrackTable").hide();
			$("#PhyTable").hide();			
			$("#trackoptions").hide();			
			$("#inputLine").show();
			$("#weightdate").show();
			$(".date-picker-control").show();
			$("#selectandbutton").hide();			
			$("#trackbyExercise").hide();
			//fill_Splits ($('#programoption').find(":selected").attr("value"));
			//if (!$("#InputTable").length){
			//	alert("lolol");
			//loadInputBySplitID($("#splitoption").find(":selected").attr("value"));
		//} useless, since I'm not deleting inputtable
			$("#programoption").unbind();	
			$("#programoption").removeAttr("onchange").change(function(){fill_Splits ($("#programoption").find(":selected").attr("value"));loadInputBySplitID($("#splitoption").find(":selected").attr("value"));});
			$("#splitoption").unbind();
			$("#splitoption").removeAttr("onchange").change(function(){loadInputBySplitID($(this).find(":selected").attr("value"));});
			$("#TrackSelect").removeAttr("disabled");
			$("#PhysiqueSelect").removeAttr("disabled");
		}
		});
		$("#TrackSelect").live("click",function(){			
			if($(this).attr("disabled")!=1){
			$("#TrackSelect").attr("disabled",1);
			$("#weightsubmit").hide();
			$("#recordsubmit").hide();
			$("#recordLine").hide();
			$("#progressSelector").hide();
			$("#weightLine").hide();
			$("#weightdate").hide();
			$("#PhyTable").hide();
			$(".date-picker-control").hide();			
			$("#weightsubmit").hide();
			$("#InputTable").hide();
			$("#inputLine").hide();
			$("#trackoptions").show();
				today = new Date();
				if (today.getMonth()+1<10){
    				month = "0"+eval(today.getMonth()+1);
    			} else {
    				month = today.getMonth()+1;
    			}
    			if (today.getDate()<10){
    				day = "0"+today.getDate();
    			}else{
    			day = today.getDate();
    		}
  		stoday =  today.getFullYear()+"-"+month+"-"+day;
  		startdateExercise = stoday;
  		$("#TrackTable").show();
  			if (!$("#TrackTable").children().length){
				loadRecordsByDate(get,stoday);
				calgen();
				currentMonth=today.getMonth()+1;
    			currentYear=today.getFullYear();
				populateFields(today.getMonth()+1, today.getFullYear(), get);
			}
			$("#RecordSelect").removeAttr("disabled");
			$("#PhysiqueSelect").removeAttr("disabled");
			$("#exerciseSelect").removeAttr("disabled");
			$("#dayeSelect").attr("disabled",1);
		}
			
		});
		$("#weightdate").mask("99/99/9999");
		today = new Date();
    	$("#weightdate").val(eval(today.getMonth()+1)+"/"+today.getDate()+"/"+today.getFullYear());
    	if (today.getMonth()+1<10){
    		month = "0"+eval(today.getMonth()+1);
    	} else {
    		month = today.getMonth()+1;
    	}	
    	if (today.getDate()<10){
    		day = "0"+today.getDate();
    		}else{
    			day = today.getDate()
    		}
    	datePickerController.createDatePicker({
 			formElements:{"weightdate":"m-sl-d-sl-Y"},
 			rangeLow:eval(today.getFullYear()-2)+""+month+day, 	 
 			rangeHigh:today.getFullYear()+""+month+day,
 			statusFormat:"l-cc-sp-d-sp-F-sp-Y",
 						finalOpacity:100, 
 						noFadeEffect:true, 
 						noDrag:true, 
 						fillGrid:true, 
 						constrainSelection:false 
		});
		function testFloat(text){
			if (isNaN(text)|| text==""||parseFloat(text)<=0){
				$("#inputLine #errordate").text("Invalid weight input").show();
				t = setTimeout(function(){$("#inputLine #errordate").fadeOut(1000)},3000);
				return false;
			}else{
				return true;
			}
		}
		function testInt(text){
			if (parseInt(text)!=parseFloat(text)||isNaN(text)|| text==""||parseInt(text)<=0){
				$("#inputLine #errordate").text("Invalid rep input").show();
				t = setTimeout(function(){$("#inputLine #errordate").fadeOut(1000)},3000);
				return false;
			}else{
				return true;
			}
		}
		function testdate(date){
			var dateArray = date.split("/");
			var testDate = new Date(dateArray[2], dateArray[0]-1, dateArray[1]);
        	if (testDate.getDate()!=dateArray[1] || testDate.getMonth()!=dateArray[0]-1 || testDate.getFullYear()!=dateArray[2]) {
        		$("#inputLine #errordate").text("This is not a valid date. (MM/DD/YYYY)").show();
            	t = setTimeout(function(){$("#inputLine #errordate").fadeOut(1000)},3000);
            	return false;
       		} else {
       			today = new Date();
       			if (testDate.getTime()>today.getTime()){
       				$("#inputLine #errordate").text("You can predict the future!? Today is "+eval(today.getMonth()+1)+"/"+today.getDate()+"/"+today.getFullYear()).show();
            		t = setTimeout(function(){$("#inputLine #errordate").fadeOut(1000)},3000);
            		return false;
   				} else if (today.getFullYear()-testDate.getFullYear()>21){
   					$("#inputLine #errordate").text("Let's forget about the past shall we? no more than 20 years ago").show();
            		t = setTimeout(function(){$("#inputLine #errordate").fadeOut(1000)},3000);
            		return false;
        		}else{
        			return true;
    			}
			}
		}
		$("#weightsubmit").live("click",function(){
			if (testInt($("#newWeightInput").val()) ==true && testdate($("#weightdate").val())==true){
			
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"addweight",
    				"weight":$("#newWeightInput").val(),
    				"lbkg":$(".lbkgselect").val(),
    				"date":$("#weightdate").val()//PASS IN THE DATE(AVOID DIFFERENT TIMEZONE AND ALLOW USERS TO CHOOSE DATE)
				},
    				
    			success: function(){
    				var dateArray = $("#weightdate").val().split("/");
					var testDate = new Date(dateArray[2], dateArray[0]-1, dateArray[1]);
    				dateArray = $("#weightfinal").val().split("/");
			 		var testDate1 = new Date(dateArray[2], dateArray[0]-1, dateArray[1]);
			 		dateArray = $("#weightstart").val().split("/");
			 		var testDate2 = new Date(dateArray[2], dateArray[0]-1, dateArray[1]);
    				if (testDate.getTime()>testDate1.getTime()){
    					$("#weightfinal").val($("#weightdate").val());
    					$("#weightoptionsubmit").click();
    				}else if (testDate.getTime()<testDate2.getTime()){
    					$("#weightstart").val($("#weightdate").val());
    					$("#weightoptionsubmit").click();
    				}
    				$("#newWeightInput").val("");
					refreshdata(get);
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
    		}	
		});
		$("#weightoptionsubmit").live("click",function(){
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			dataType: "json",
    			data: {
    				"action":"changeweightoption",
    				"UID":get,
    				"weightstart":$("#weightstart").val(),
    				"weightfinal":$("#weightfinal").val()//PASS IN THE DATE(AVOID DIFFERENT TIMEZONE AND ALLOW USERS TO CHOOSE DATE)
				},
    				
    			success: function(r){
					weightoption=r;
    				plotGraph();
    				$("#weightstart").val(weightoption[3]);
    				$("#weightfinal").val(weightoption[4]);
    				datePickerController.setDateFromInput("weightstart");
    				datePickerController.setDateFromInput("weightfinal");
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		});
		
// deferred function to fill fields of table MAYBE CALL THIS IN CALGEN OR SOMETHING SO NO NECESSARILY BIND
	$(".day").live("click",function(){
		if ($(this).hasClass("next")){
			$("#calright").click();
		}else if($(this).hasClass("previous")){
			$("#calleft").click();
		}else{
			$("#recordsbydate table").remove();//WHAT IF THERE"S NO PREVIOUS ONES"
			viewingDate=$(this).attr("date");
			prevnextRecords(get,1);
			$(this).attr("");
		}
	});
	$("#calleft").live("click",function(){
		if(currentMonth>1){
			currentMonth-=1;
		}else{
			currentMonth=12;
			currentYear-=1;
		}
		populateFields(currentMonth, currentYear, get);
	});
	$("#calright").live("click",function(){
		if(currentMonth<12){
			currentMonth+=1;
		}else{
			currentMonth=1;
			currentYear+=1;
		}
		populateFields(currentMonth, currentYear, get);
	});
}
function getFirstDay(theYear, theMonth){
    	var firstDate = new Date(theYear,theMonth,1);
    	return firstDate.getDay();
	}
// number of days in the month
	function getMonthLen(theYear, theMonth) {
    	var oneDay = 1000 * 60 * 60 * 24;
    	var thisMonth = new Date(theYear, theMonth, 1);
    	var nextMonth = new Date(theYear, theMonth + 1, 1);
    	var len = Math.ceil((nextMonth.getTime() - thisMonth.getTime())/oneDay);
   		return len;
	}
// correct for Y2K anomalies
	function getY2KYear(today) {
    	var yr = today.getYear();
    	return ((yr < 1900) ? yr+1900 : yr);
	}
// create basic array
	theMonths = new MakeArray(12);
// load array with English month names
	function MakeArray(n) {
    	this[0] = "January";
    	this[1] = "February";
    	this[2] = "March";
    	this[3] = "April";
    	this[4] = "May";
    	this[5] = "June";
    	this[6] = "July";
    	this[7] = "August";
    	this[8] = "September";
    	this[9] = "October";
    	this[10] = "November";
   		this[11] = "December";
    	this.length = n;
    	return this;
	}
var currentMonth;
var currentYear;
function populateFields(month, theYear, get) {
    // which is the first day of this month?
    var theMonth = month-1;
    var firstDay = getFirstDay(theYear, theMonth);
    // total number of <TD>...</TD> tags needed in for loop below
    var howMany = getMonthLen(theYear, theMonth);
    // set month and year in top field
    $("#oneMonth").text(theMonths[theMonth] + " " + theYear);
    // fill fields of table
    if(firstDay==0){
    	firstDay=7;
    }
    if(month<10){
        month = "0"+month;
            }
    for (var i = 0; i < 43; i++) {
        if (i < firstDay || i >= (howMany + firstDay)) {
            // before and after actual dates, empty fields
            // address fields by name and [index] number
            $("#day"+i).text("");
            if (i < firstDay){
            	$("#day"+i).attr("class", "day previous");
        	}else{
        		$("#day"+i).attr("class", "day next");
        	}
            $("#day"+i).removeAttr("date");
        } else {
        	day = eval(i-firstDay+1);
            if (day<10){
            	day = "0"+day;
            }
            if (i%7==0||i%7==6){
				color='weekend';
			}else{
				color='weekday';
			}
            $("#day"+i).attr("class","day "+color)
            $("#day"+i).attr("date", theYear+"-"+month+"-"+day);
            $("#day"+i).text(i - firstDay + 1);
       }
    }
     $.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			datatype: "json",
    			data: {
    				"action":"fillCalendar",
    				"UID":get,
    				"date":theYear+"-"+month+"-01"
				},
    			success: function(r){
					var search =JSON.parse(r);
					for (j=0;j<search.length;j++){
						$("div[date=\""+search[j][0]+"\"]").append("<p>"+search[j][1]+"</p>");
					}
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
    		
				$(".day").removeClass("selected");
				$("div[date=\""+viewingDate+"\"]").addClass("selected");    	
	}	
function calgen(){

	// initialize variable with HTML for each day's field
// all will have same name, so we can access via index value
// empty event handler prevents
// reverse-loading bug in some platforms
// start assembling HTML for raw table
var content = "<div><br /><br /></div><div id='calleft'></div><TABLE id='calendar'>";
// field for month and year display at top of calendar
content += "<TR><TH COLSPAN=7><div id='oneMonth'></div></TH></TR>";
// days of the week at head of each column
content += "<TR><TH>Mon</TH><TH>Tue</TH><TH>Wed</TH>";
content += "<TH>Thu</TH><TH>Fri</TH><TH>Sat</TH><TH>Sun</TH></TR>";
content += "<TR>";

// layout 6 rows of fields for worst-case month
for (var i = 1; i < 43; i++) {
    content += "<TD ALIGN='middle'><div id=\"day"+i+"\" ></div></TD>";
    if (i % 7 == 0) {
        content += "</TR><TR>";
    }
}

content += "</TABLE><div id='calright'></div>";
// blast empty table to the document
$("#TrackTable").append(content);
}
function cleanHREF(str) {
    return str.replace(/\<a(.*?)href=['"](javascript:)(.+?)<\/a>/gi, "Naughty!");
}
function strip_tags(str, allowed_tags) {
 // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Luke Godfrey
    // +      input by: Pul
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // +      input by: Alex
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Marc Palau
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Brett Zamir (http://brettz9.blogspot.com)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Eric Nagel
    // +      input by: Bobby Drake
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: strip_tags('<p>Kevin</p> <br /><b>van</b> <i>Zonneveld</i>', '<i><b>');
    // *     returns 1: 'Kevin <b>van</b> <i>Zonneveld</i>'
    // *     example 2: strip_tags('<p>Kevin <img src="someimage.png" onmouseover="someFunction()">van <i>Zonneveld</i></p>', '<p>');
    // *     returns 2: '<p>Kevin van Zonneveld</p>'
    // *     example 3: strip_tags("<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>", "<a>");
    // *     returns 3: '<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>'
    // *     example 4: strip_tags('1 < 5 5 > 1');
    // *     returns 4: '1 < 5 5 > 1'
    var key = '', allowed = false;
    var matches = [];
    var allowed_array = [];
    var allowed_tag = '';
    var i = 0;
    var k = '';
    var html = '';

    var replacer = function(search, replace, str) {
        return str.split(search).join(replace);
    };

    // Build allowes tags associative array
    if (allowed_tags) {
        allowed_array = allowed_tags.match(/([a-zA-Z]+)/gi);
    }

    str += '';

    // Match tags
    matches = str.match(/(<\/?[\S][^>]*>)/gi);

    // Go through all HTML tags
    for (key in matches) {
        if (isNaN(key)) {
            // IE7 Hack
            continue;
        }

        // Save HTML tag
        html = matches[key].toString();

        // Is tag not in allowed list? Remove from str!
        allowed = false;

        // Go through all allowed tags
        for (k in allowed_array) {
            // Init
            allowed_tag = allowed_array[k];
            i = -1;

            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+'>');}
            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+' ');}
            if (i != 0) { i = html.toLowerCase().indexOf('</'+allowed_tag)   ;}

            // Determine
            if (i == 0) {
                allowed = true;
                break;
            }
        }

        if (!allowed) {
            str = replacer(html, "", str); // Custom replace. No regexing
        }
    }

    return str;
}