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
}else if(evt){
	keynum = evt.keyCode;
}
// Allow: backspace, delete, tab and escape
if ( keynum == 46 || keynum == 8 || keynum == 9 || keynum == 27 || 
			// Allow: home, end, left, right
            (keynum >= 35 && keynum <= 39)) {
                 // let it happen, don't do anything
                 return;
        }


keychar = String.fromCharCode(keynum);
if(dots==0){
	numcheck = /\d/;
}else{
	numcheck = /\d|\./
}

return numcheck.test(keychar)
}

	//Retrieves one of the SplitID that the user entered records for for a specific date.
	//Picks a default one if no records are entered for that date.
	function getSplitIDByDate(date){
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"getSplitIDByDate",
    				"date":date
				},
    				
    			success: function(r){
    				//manually select Program Dropdown and manually select Split Dropdown
    				selectDropdownBySplitID(r);
                 	loadInputBySplitID(r, date);
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});    		
	}
	
	//chooses program from drop down list based on the sid selected
	function selectDropdownBySplitID(sid){
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"getProgramIDbySplitID",
    				"sid":sid
				},
    				
    			success: function(r){
    				$("#programoption").find("option[value='"+r+"']").attr("selected", true);
    				$("#splitoption").find("option[value='"+sid+"']").attr("selected", true);
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
	}
	
	//Populates Input records
	function loadInputBySplitID(sid, date){
		$("#InputTable").children().remove();
		$("#container").append("<div id='loading'><center><div>loading...please wait (or get faster internet)</div><br /><br /><img width='50px' src='/images/loading_circle.gif'/></center></div>");
		
		if(sid=="-1"){
			sid = $("#splitoption").find(":selected").attr("value");
		}
		
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"loadInputExercise",
    				"SID":sid,
    				"date":date
				},
    				
    			success: function(r){
                 	$("#InputTable").append(r);
					$("#loading").remove();
					$(".recordsComment").each(function(){
						height = $(this).parent().height()-57;
						if(height<50) height=50;
							$(this).css("height", height);
						})
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});    		
	}
	function loadExerciseOptions(selected){
		$("#exerciseoption").remove();
		$("#recordLine").append("<div id='loading'><center><div>loading...please wait (or get faster internet)</div><br /><br /><img width='50px' src='/images/loading_circle.gif'/></center></div>");
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
		$("#TrackTable").append("<div id='loading'><center><div>loading...please wait (or get faster internet)</div><br /><br /><img width='50px' src='/images/loading_circle.gif'/></center></div>");
		before = viewingDate;
		
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
				after = viewingDate;

				if(before.substring(0, 7)!= after.substring(0, 7)){
					populateFields(after.substring(5, 7), after.substring(0, 4), get);
				}
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
		$("#PhyTable").append("<div id='loading'><center><div>loading...please wait (or get faster internet)</div><br /><br /><img width='50px' src='/images/loading_circle.gif'/></center></div>");
		UID = get;//can delete hidden currentid
		$("#PhyTable").append("<form name='weightchartoptions' id='weightchartoptions'><div id='trendlineonoff'>Trendline</div><input type='checkbox' name='trendlinecheck' value='yes'/></form><div id='weightChart'></div><div id='weightstartfinal'><h3>Choose the start and end dates for the graph</h3><br /><br /><input id='weightstart' maxlength='10' size='7'/><input id='weightfinal' maxlength='10' size='7'/><input id='weightoptionsubmit' class='fitwidth box' type=button value='change'/></div><div id='weightcontentheader' class='ui-corner-top ui-widget-header'><span class='ui-jqgrid-title'>Weight</span></div><div><table id='weightheader'><tr><td>Date</td></tr><tr><td>Weight</td></tr></table><div id='weightdiv'><table id='weightcontent'></table></div></div>");
		//,{placeholder:"_"}
		$("#weightstart").mask("99/99/9999");
		$("#weightfinal").mask("99/99/9999");
		$('#newWeightInput').keypress(function(event) {
        		return onlyNumbers(event,1);
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
					refreshdata(get);
	}
	var weightdata;
	var weightoption;
	//Loads all weight associated with a user
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
    				refreshtable(r, get); 
    				refreshoption(get);   				
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});  
	}
	var weightmaxmin;
	//Gets maximum and minimum weight within a range
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
    				if (r[0]!=null){	//If max is not null, min is not null.
    					weightmaxmin=r;		//If there are points within the range, change maxmin
    				}	
    				plotGraph();
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});  
	}
	function refreshtable(r, get){
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
			optionstr=optionstr+"<td date="+dates[i]+">"+"<div date=\""+dates[i]+"options\">"+"<div class='weightdelete sp'></div></div></td>";
		}
		weightstr=weightstr+"</tr>";
		optionstr=optionstr+"</tr>";
		$("#weightcontent").children().remove();
		if(user_id == get){
			$("#weightcontent").append(datestr+weightstr+optionstr);//FIX THE WHOLE CLASS THING. USE ANOTHER ATTR AND MAKE j M Y WITHOUT HIVENS	
		}else{ 
			$("#weightcontent").append(datestr+weightstr);
			$("#weightdiv").height(75);	
		}
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
	//Retrieves the weight option of a user
	function refreshoption(get){
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			dataType: "json",
    			data: {
    				"action":"loadweightoption",
    				"UID":get//LATER CHANGE THIS TO GET
				},
    				
    			success: function(r){	//(Start, final, diff, vertical tick marks (in days), start(option), final(option))
    				if(parseInt(r[2])>0){	//If this fails, something is internally wrong with the database!!!
    					weightoption=r;
    					$("#weightstart").val(weightoption[3]);
    					$("#weightfinal").val(weightoption[4]);
    					datePickerController.setDateFromInput("weightstart");
    					datePickerController.setDateFromInput("weightfinal");
					}else{
						alert("start date cannot be greater than end date!");
					}
					getmaxminweight(get);
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
		$("#loading").remove();
		var plot = $.jqplot('weightChart',  [weightdata],
			{ title:'Weight',
  				axes:{
  					xaxis:{renderer:$.jqplot.DateAxisRenderer,
  						min:weightoption[0],//NEED TO USE AJAX TO STORE/FIND MIN DATE
  						max:weightoption[1],
  						tickInterval:weightoption[2],
          					tickOptions:{
            					formatString:'%e&nbsp;%b&nbsp;%y'}},
  					yaxis:{label: "lbs", min:parseInt(weightmaxmin[1]-1), max:parseInt(weightmaxmin[0]+1),
  							tickOptions:{
  								formatString:'%.1f' }
  								}},
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
	
	var user_id;
	
	function initializeProgress(get, uid){
				user_id = uid;
				var request = new Array();
				var pairs = location.search.substring(1).split('&');
				for (var i = 0; i < pairs.length; i++) {
  						var pair = pairs[i].split('=');
  						request[pair[0]] = pair[1];
				}
				
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
				
				
				/* UnCOMMENT when records can be accessed with ?date = in url to select which day to enter record
				if(!request['date'])
				loadRecordsByDate(get,stoday);
				else{
					//converting to javascript date
					myDateParts = request['date'].split("/");

					request['date'] = myDateParts[2]+"-"+myDateParts[0]+"-"+myDateParts[1];
					
					loadRecordsByDate(get, request['date']);
				} */
				
				
				//fill_Splits ($('#programoption').find(":selected").attr("value"));
				//loadInputBySplitID($("#splitoption").find(":selected").attr("value"));
				getSplitIDByDate(today);	//This calls loadInputBySplitID
				
				var currentdate;
				$("#weightdate").change(function(){
					if($(this).val()!=currentdate && testdate($(this).val())){
					//$("#splitoption").find("option[value='49']").attr("selected", true);
					//loadInputBySplitID(49);	
					getSplitIDByDate($("#weightdate").val());
					currentdate = $("#weightdate").val();
				}
				})
				$("#weightdate").focus(function(){
					//alert($(this).val());
					if($(this).val()!=currentdate && testdate($(this).val())){
					//$("#splitoption").find("option[value='49']").attr("selected", true);
					//loadInputBySplitID(49);	
					getSplitIDByDate($("#weightdate").val());
					currentdate = $("#weightdate").val();
				}
				})
		  		
				//Save previously viewed records in cache?
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
	

	//mouseout
	//Empties the recordsComment box
	$(".recordsCommentCncl").live("click",function(){
		$(this).prev().val("");
	})
	$(".recordsCommentBtn").live("click",function(){
		$(this).prev().slideDown();
		$(this).prev().prev().slideUp();
		$(this).fadeOut();
		$(this).next().next().fadeIn();
		height = $(this).parent().height()-57;
		if(height<50) height=50;
		$(this).next().css("height", height).slideDown();
	});

	$(".prevnotes").live("click",function(){
		$(this).prev().slideDown();
		$(this).next().fadeIn();
		$(this).slideUp();
		$(this).next().next().next().fadeOut();
		$(this).next().next().slideUp();
	})
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
		$("#TrackTable").empty(); //clear it for tracktable
		//flag = false;
		weightdate = $("#weightdate").val()
		if(testdate(weightdate)==true){
		$(".recordtable").each(function(){
			var $whitelist = '<b><i><strong><em><a>',
    		weight = strip_tags(cleanHREF($(this).find(".weightInputTable").val()), $whitelist);//doesn't work. take away parseInt
    		rep = strip_tags(cleanHREF($(this).find(".repInputTable").val()), $whitelist);
    		//URLtext = escape(text);
    		if (weight.length>0 || rep.length>0){
    			if (testFloat(weight)==true && testInt(rep)==true){
    				comment = $(this).parent().find("tr[list="+$(this).attr('list')+"]").find(".recordsComment").val();
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"recordsubmit",
    				"date":weightdate,
    				"lid":$(this).attr('list'),
    				"weight":$(this).find(".weightInputTable").val(),    //weight instead of $(this).find ?
    				"lbkg":$(this).find("select").val(),
    				"rep":$(this).find(".repInputTable").val(),
    				"pos":$(this).attr('rel'),
    				"comment":comment
				},
    				
    			success: function(){
    					$.ajax({
       						type: "POST",
      			 			url: "/db-interaction/users.php",
       						data: "action=newsForRecord&content="+weightdate+
    			   			"&newstype=4",
       						success: function(){
       						},
      						error: function(){
       						}
      					});
    				//shouldn't have to update news everytime it gets here. set a flag?
    			
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		}
	}
		});
		$(".newexercise").each(function(){
			var $whitelist = '<b><i><strong><em><a>',
    		weight = strip_tags(cleanHREF($(this).find(".weightInputTable").val()), $whitelist);
    		rep = strip_tags(cleanHREF($(this).find(".repInputTable").val()), $whitelist);
    		//URLtext = escape(text);
    		if (weight.length>0 || rep.length>0){
    			if (testFloat(weight)==true && testInt(rep)==true){
    				//rel >2? or just check if exist, if so, update?
    			$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"addSetToOthers",
    				"date":$("#weightdate").val(),
    				"lid":$(this).attr('list'),
    				"weight":$(this).find(".weightInputTable").val(),    //weight instead of $(this).find ?
    				"lbkg":$(this).find("select").val(),
    				"rep":$(this).find(".repInputTable").val(),
    				"pos":$(this).attr('rel')
				},
    				
    			success: function(){
    					$.ajax({
       						type: "POST",
      			 			url: "/db-interaction/users.php",
       						data: "action=newsForRecord&content="+weightdate+
    			   			"&newstype=4",
       						success: function(){
       							
       						},
      						error: function(){
       						}
      					});
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
		$(this).parent().parent().after("<tr class=\""+$(this).parent().parent().attr('class')+"\" list=\""+$(this).parent().parent().attr('list')+"\" class=recordtable rel=\""+(parseInt($(this).parent().parent().attr('rel'))+1)+"\"><td colspan='2'></td><td><input class='weightInputTable' maxlength = '5' size='4' onkeypress='return onlyNumbers(event,1)' /><select ><option selected value='lbs'>lbs</option><option value='kg'>kg</option></select></td><td><input class='repInputTable' maxlength = '3' size='1' onkeypress='return onlyNumbers(event,0)' /></td><td></td><td class='zeropadding'><div class='addnewset add_set_btn sp'></div></td></tr>");
		commentRow = $(this).parent().parent().parent().find('tr[list='+$(this).parent().parent().attr("list")+'][rel=1]').find(".commentSpan");
		
		commentRow.attr('rowspan',commentRow.attr('rowspan')+1);
		$(this).remove();
	});
	$("#addoldExerciseInputTable").live("click",function(){
		weightdate = $("#weightdate").val();
		//check to make sure it's not empty
		if(testdate($("#weightdate").val())==true){
			var $whitelist = '<b><i><strong><em><a>',
    		weight = strip_tags(cleanHREF($(this).parent().parent().find("#oldWeight").val()), $whitelist);
    		rep = strip_tags(cleanHREF($(this).parent().parent().find("#oldRep").val()), $whitelist);
    		osid = $(this).parent().parent().attr("sid");
    		//URLtext = escape(text);
    		if (weight.length>0 && rep.length>0){
    			if (testFloat(weight)==true && testInt(rep)==true){
					$.ajax({
    					type: "POST",
    					url: "/db-interaction/gsprogress.php",
    					data: {
    						"action":"addOldExerciseSet",
    						"date":$("#weightdate").val(),
    						"EID":$(this).parent().parent().find("#OldExerciseSelect").val(),
    						"weight":weight,
    						"lbkg":$(this).parent().parent().find("#oldlbkg").val(),
    						"rep":rep,
    						"OSID":osid,
    						"comment":$('#oldComment').val()
						},
    				
    					success: function(r){
    						$('#newExerciseHeader').show();
    						$("#somethingnewrow").before(r);
							$('#oldWeight').val('');
							$('#oldRep').val('');
							$("#oldComment").val('');
    					$.ajax({
       						type: "POST",
      			 			url: "/db-interaction/users.php",
       						data: "action=newsForRecord&content="+weightdate+
    			   			"&newstype=4",
       						success: function(){
       							
       						},
      						error: function(){
       						}
      					});							
							//need to label the comment box and clear it.!!!!
    					},
    					error: function(){
    			    		// should be some error functionality here
    					}
    				});
				}else{
					alert("Weight and Rep must be a number");	//might be unnecessary because the testint, testfloat and testdate already have errors
				}
			}else{
				alert("Weight and rep cannot be empty.");
			}
		}else{
			alert("Date is invalid.");
		}
	})
	$("#addExerciseInputTable").live("click",function(){
		//check to make sure it's not empty
		weightdate = $('#weightdate').val();
		if(testdate($("#weightdate").val())==true){
			var $whitelist = '<b><i><strong><em><a>',
			exercise = strip_tags(cleanHREF($(this).parent().parent().find("#newExercise").val()), $whitelist);
    		weight = strip_tags(cleanHREF($(this).parent().parent().find("#newWeight").val()), $whitelist);
    		rep = strip_tags(cleanHREF($(this).parent().parent().find("#newRep").val()), $whitelist);
    		osid = $(this).parent().parent().attr("sid");
    		//URLtext = escape(text);
    		if (weight.length>0 && rep.length>0 && exercise.length>0){
    			if (testFloat(weight)==true && testInt(rep)==true){
					$.ajax({
    					type: "POST",
    					url: "/db-interaction/gsprogress.php",
    					data: {
    						"action":"addnewset",
    						"date":$("#weightdate").val(),
    						"exercisename":exercise,
    						"weight":weight,
    						"lbkg":$(this).parent().parent().find("select").val(),
    						"rep":rep,
    						"OSID":osid,
    						"comment":$('#newComment').val()
						},
    				
    					success: function(r){
    						$('#newExerciseHeader').show();
    						$("#somethingnewrow").before(r);
							$('#newExercise').val('');
							$('#newWeight').val('');
							$('#newRep').val('');
							$('#newComment').val('');
							//need to label the comment box and clear it.!!!!
							
    					$.ajax({
       						type: "POST",
      			 			url: "/db-interaction/users.php",
       						data: "action=newsForRecord&content="+weightdate+
    			   			"&newstype=4",
       						success: function(){
       							
       						},
      						error: function(){
       						}
      					});							
    					},
    					error: function(){
    			    		// should be some error functionality here
    					}
    				});
				}else{
					alert("Weight and Rep must be a number");	//might be unnecessary because the testint, testfloat and testdate already have errors
				}
			}else{
				alert("Exercise, weight and rep cannot be empty.");
			}
		}else{
			alert("Date is invalid.");
		}
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
			$("#TrackTable").show();
			$("#trackbyExercise").hide();
			$("#progressSelector").hide();
			$("#inputLine").hide();
			$("#exerciseSelect").removeAttr("disabled");
		}
		})
		$("#exerciseSelect").live("click",function(){
			if($(this).attr("disabled")!=1){
			$("#exerciseSelect").attr("disabled",1);
			$("#TrackTable").hide();
			//$("#recordsbydate").hide();	
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
			$("#recordsbydate").show();
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
				
				if(!request['date']){
					loadRecordsByDate(get,stoday);
					calgen();
					currentMonth=today.getMonth()+1;
    				currentYear=today.getFullYear();
					populateFields(today.getMonth()+1, today.getFullYear(), get);
				}else{
					//converting to javascript date
					myDateParts = request['date'].split("/");

					request['date'] = myDateParts[2]+"-"+myDateParts[0]+"-"+myDateParts[1];
					
					loadRecordsByDate(get, request['date']);
					calgen();
					currentMonth=today.getMonth()+1;
    				currentYear=today.getFullYear();
					populateFields(myDateParts[0], myDateParts[2], get);
				}
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
			if (testFloat($("#newWeightInput").val()) ==true && testdate($("#weightdate").val())==true){
			
				weight = $("#newWeightInput").val();
				//temporarily doing this for the graph
				if($(".lbkgselect").val()=="kg"){
					weight = weight*2.25;
				}
				
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/gsprogress.php",
    			data: {
    				"action":"addweight",
    				"weight":weight,
    				"lbkg":"lbs",
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
		//Changes weight option if owner changes dates.
		//Updates weightoption if nonowner changes dates.
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
    				if(parseInt(r[2])>0){
    					weightoption=r;
    					$("#weightstart").val(weightoption[3]);
    					$("#weightfinal").val(weightoption[4]);
    					datePickerController.setDateFromInput("weightstart");
    					datePickerController.setDateFromInput("weightfinal");
					}else{
						alert("start date cannot be greater than end date!");
					}
					getmaxminweight(get);
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		});
		
		/*$(".day").live("mouseover", function() {
			$("#calHover").empty();
    		$("#calHover").append($(this).html());//alert("LOL");//$(this).addClass("over");
    		//position = $(this).position();
    		position = $("#calendar").position();
    		$('#calHover').css({left: position.left-200, top: position.top+50 });
    		//alert(position.left);
    		//$('#calHover').css({left: position.left, top: position.top-30 });
  		});
  		$(".day").live("mouseout", function() {
    			//alert("lol");//$(this).addClass("over");
  		});*/
		
		
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
	
	//Goes to the corresponding views
	if(request['view']=="track"){
		$("#TrackSelect").attr("disabled", 0);
		//$("#TrackTable").empty();
		$("#TrackSelect").click();
	}else if(request['view']=="physique") $("#PhysiqueSelect").click();
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
	month = month + "";
	if(month.substr(0,1)=="0"){
		month = month.substr(1,2);	
	};
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
            $("#day"+i).attr("title","");		//Small hover to show all programs in calendar
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
						title = $("div[date=\""+search[j][0]+"\"]").attr("title");	//hover to show all programs in calendar
						$("div[date=\""+search[j][0]+"\"]").attr("title", title+search[j][1]+"\n");
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