function initialize() {
    
	$(".addexercisetextbox").autocomplete('txt/exercise.php');
	$(".chooseoldexercise").live("click",function(){
		thiscache = $(this);
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/lists.php",
    			datatype: "json",
    			data: {
    					"action":"loadExerciseSearch"
    				},
    			success: function(r){
    				$('#popup').find('ul').remove();
    				$('#popup').append(JSON.parse(r)[0]);
    				//$(".searchexercise").autocomplete({
           			//	data:JSON.parse(r)[1];//BECOMES VERY WEIRD. WHEN YOU CLICK ON IT IT THINKS YOU"RE CLICKING SOMEWHERE ELSE AND DIES"           		
       				//});
       				var eid;
       				$(".exercisecontent").bind("click",function(){
       					$(".searchexercise").val($(this).text());
       					eid = parseInt($(this).attr('class'));
       					$("#popup").scrollTop(0);
   					});
   					var search =JSON.parse(r)[1];
       				$("#popupsubmit").bind("click",function(){//FIX THE MULTIPLE BINDING!!!!!!!!!!!!!!!!!!!!!!!set global variable = a certain chooseoldexercise
       					var match = new Array();
       					var exercise = 0;
       					for(i=0;i<search.length;i++){
       						if($(".searchexercise").val().toLowerCase()==search[i][0].toLowerCase()){
       							match.push(search[i][1]);
   							}
   						}
   						if (match.length==1){
   								exercise = match[0];
   						}else if(match.length>1){
   							for (j=0;j<match.length;j++){
   								if(match[j]==eid){
   									exercise = match[j];//shortcut this!!!!!!!!!!!!!!!!!
   								}
   							}
   							if (exercise==0){//for some reason, this fires before the for loop is done. So if you select 
   								alert("You have more than one exercise with the same name. Specify?");
   							}
   						}else{
   							alert("You do not have an exercise under this name. Memory loss much?");//CHANGE TO ERROR!!!!!!!!!!!!!!!!!!!
   						}
   						if (exercise!=0){
   							$(".addexercisesubmit").attr("disabled", true);
        					$(".chooseoldexercise").attr("disabled",true);
        					$("#popupsubmit").attr("disabled",true);
        					sid = thiscache.next().val();
        					pos = thiscache.parent().parent().prev().children().length+1;
           					 $.ajax({
    							type: "POST",
    							url: "/db-interaction/lists.php",
    							data: "action=add&sid=" + sid
    								+ "&eid=" + exercise
    								+ "&pos=" + pos,
    							success: function(r){
                 				 $("#"+sid+"split").find(".list").append("<ul id='" +r+ "' rel='"+pos+"' class='exerciseEdit' name='exerciseList'><p class='exercise'>"+$(".searchexercise").val()+"</p><span class='setnrep'></span><li class='setAdd' hidden><p class='set'></p><p class='weight'></p><p class='lbkg'></p> <p class='rep'></p><p class='comment'></p></li><div class='editbuttons'><input type='button' title='edit' class='jeditable-activate sp' /><input title='add more sets' type='button' class='morelists sp'/><div title='delete' class='deletetab sp'></div></div><div title='hold mouse to drag' class='draggertab sp'></div></ul>");
                				 bindAllTabs2("#"+r+" .setAdd");
                 //bindAllTabs3("#"+theResponse+" .exercisename .exercise");
    							},
    							error: function(){
    			    // should be some error functionality here
    							}
    						});
        	$(".addexercisesubmit").removeAttr("disabled");
        	$(".chooseoldexercise").removeAttr("disabled");
        	$("#popupsubmit").removeAttr("disabled");
        	thiscache.parent().parent().find(".addexercisecancel").click();
   						}
       				});			
    				},
    			error: function() {
    			    $("#main").prepend("Deleting the item failed...");//WTF IS THIS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!CHANGE ERROR
    			}
    		});
		position = $(this).position();
		$('#popup').show();
		$('#popup').css({left: position.left, top: position.top+20 });
		$(document.body).bind("click",function() {
			$('#popup').hide();
    		$(document.body).unbind();
    		$("#popup").unbind();
		});//COSTLYHYYYYY?
		$('#popup').bind("click", function(e) {
			e.stopPropagation();
   		});
	})

    $(".jeditable-activate").live("click",function(){
    	if ($(this).parent().prev().prev().children().length>0&&$(this).parent().prev().prev().find('.save_button').length==0){
   		$(this).parent().prev().prev().children().trigger("editclick");
   		$(this).parent().prev().prev().append("<button class='save_button'>Save</button>");
   		$(this).parent().prev().prev().append("<button class='cancel_button'>Cancel</button>");
   		}
	});
	$(".editSection").live("click",function(){
		$(this).prev().trigger("editclick");
	})
	$(".editProgram").live("click",function(){
		$(this).prev().trigger("editclick");
	})
	$(".save_button").live("click",function(){
		$(this).parent().find("form").submit();
		$(this).next().remove();
		$(this).remove();
	});
	$(".cancel_button").live("click",function(){
		$(this).parent().find('li').each(function(){
			$(this)[0].reset();
		})
		$(this).prev().remove();
		$(this).remove();
	});
	function setdelete(thiscache){
		var list = parseInt(thiscache.parent().attr("class")),
			setid=parseInt(thiscache.parent().attr("id")),
			pos = thiscache.parent().attr("rel");
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/lists.php",
    			data: {
    					"list":list,
    					"setid":setid,
    					"action":"deleteSet",
    					"pos":pos
    				},
    			success: function(r){
    				
    					var position = 0;
    	            	thiscache
    	            		.parent()
    	            			.hide("explode", 400, function(){$(this).remove()});
    	            	thiscache.parent().parent()
            				.children('li')
            					.not(thiscache.parent())
            					.each(function(){
    				            		$(this).attr('rel', ++position);
    				            	});
    				},
    			error: function() {
    			    $("#main").prepend("Deleting the item failed...");//WTF IS THIS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!CHANGE ERROR
    			}
    		});
	};
	function deletetab(thiscache){
		var split = parseInt(thiscache.parent().parent().parent().parent().attr('id')),
        	list = thiscache.parent().parent().attr("id"),
        	pos = thiscache.parent().parent().attr('rel');
        	$.ajax({
    			type: "POST",
    			url: "/db-interaction/lists.php",
    			data: {
    					"list":list,
    					"split":split,
    					"action":"delete",
    					"pos":pos
    				},
    			success: function(r){
    						var position = 0;
    	            	thiscache
    	            		.parent().parent()
    	            			.hide("explode", 400, function(){$(this).remove()});
    	            	thiscache.parent().parent().parent()
            				.children('ul')
            					.not(thiscache.parent().parent())
            					.each(function(){
    				            		$(this).attr('rel', ++position);
    				            	});
    				},
    			error: function() {
    			    $("#main").prepend("Deleting the item failed...");
    			}
    		});
	}
 // AJAX style deletion of list items
    $(".deletetab").live("click", function(){
    	var thiscache = $(this);
   		if (thiscache.data("readyToDelete") == "go for it") {
        }
        else
    	{	
    	$(document.body).bind("click",function() {
    		thiscache.animate({
        		width: "26px",
        		height: "26px",
        		top: "15px",
        		right: "0px"
        	}, 200)
        	.data("readyToDelete", "not yet");
        	thiscache.css('backgroundPosition', "-64.9px -101.5px");
    		$(document.body).unbind();
    		thiscache.unbind();
    		thiscache.parent().hide();
			$(this).parent().parent().find(".dragset").hide();
			$(".setdelete").hide();
		});
		$(".setdelete").bind("click",function(e){
			e.stopPropagation();
			setdelete($(this));
		});
		thiscache.bind("click",function(e){
			e.stopPropagation();
			deletetab(thiscache);
		});
        	thiscache.animate({
        		width: "78px",
        		height: "45px",
        		top: "-5px",
        		right: "-52px"
        	}, 200)
        	.data("readyToDelete", "go for it");
        	thiscache.css('backgroundPosition', "-64.9px -82px");
        	thiscache.parent().parent().find(".setdelete").show();
    	}
    });

// MAKE THE LIST SORTABLE VIA JQUERY UI
// calls the SaveListOrder function after a change
// waits for one second first, for the DOM to set, otherwise it's too fast.
 $(".list").sortable({
    	handle   : ".draggertab",
    	update   : function(event, ui){
    		var id = ui.item.attr('id'),
    			rel = ui.item.attr('rel'),
    			sid = parseInt(ui.item.parent().parent().attr('id'));
    			t = setTimeout("saveListOrder('"+sid+"','"+id+"', '"+rel+"')",500);
    	},
    	forcePlaceholderSize: true
    });
    
    
	// THE SPANS NEED ID's for the CLICK-TO-EDIT
    // "listitem" is appended to avoid conflicting ID's and stripped by PHP

	 
 // AJAX style adding of list items

	$(".morelists").live("click",function(){
		if($(this).parent().parent().find(".setAdd").find(".canceladd").length==0){
		$(this).parent().parent().find(".setAdd").trigger("editclick");
		$(this).parent().parent().find(".setAdd").show();
		$(this).parent().parent().find(".setAdd").append("<button class='canceladd'>Cancel</button>");
	}
	});
	$(".canceladd").live("click",function(){
		$(this).parent().css("display","none");
		$(this).parent()[0].reset();
	})
 	$('#add-split').submit(function(){
 		// HTML tag whitelist. All other tags are stripped.
    	var $whitelist = '<b><i><strong><em><a>',
    		forList = $("#current-list").val(),//CHANGE THIS SHOULD = PROGRAMID
    		newListItemText = strip_tags(cleanHREF($("#addsplittextbox").val()), $whitelist),
    		URLtext = escape(newListItemText),//escapes get the space as %20
    		newListItemRel = $("#splits").children("ul").length+1;
    		//INSERT REMOVE ATTR DISABLE BUTTON BEFORE SAVE IS DONE
			 if (newListItemText.length > 0) {
        
            // prevent multiple submissions by disabling button until save is successful
           $("#add-split").attr("disabled", true); //CHANGE THESE STUFF
        	
            $.ajax({//NEEEEEEEEDDD SPLIT ID. LOOK BELOW
    			type: "POST",
    			url: "/db-interaction/lists.php",
    			data: "action=addSplit&list=" + forList
    				+ "&text=" + URLtext
    				+ "&pos=" + newListItemRel,
    			success: function(r){
    			$("#splits").append("<ul id=\""+r+"split\" rel="+newListItemRel+">\n <div class='sectionname'><h1>"+newListItemText+"</h1><div class='edit editSection'>Edit</div><div class='deletered sp deletesection'></div></div><h3>&emsp;&emsp;Exercise &emsp;&emsp;&emsp;&emsp;&emsp;Sets &emsp;&emsp;&nbsp;Weight &emsp;&emsp;&emsp;Reps&emsp;&emsp;Comments</h3>\t\t\t<ul class=\"list\">\n\t\t\t</ul><form action='db-interaction/lists.php' class='exerciseAdd' method='post'><input type='button' class='addsubmit sp' value='Add'/><div class='addexercisediv'><input type='text' class='addexercisetextbox' autocomplete='off' style='width:0px; display:none' /><div class='addexercisebuttons' hidden ><input type='submit' class='addexercisesubmit' value='Add' /><input type='button' class='addexercisecancel' value='Cancel' /></div></div><div hidden class='oldexercisediv'> OR &emsp;<a class='chooseoldexercise'>Choose from your existing exercises</a><input type='hidden' class='current-split' name='current-list' value="+r+" /><input type='hidden' class='new-exercise-position' name='new-list-item-position' value="+newListItemRel+" /></form></ul>");                 
                  $("#addsplitcancel").click();
                 $("#add-split").removeAttr("disabled");
                 $(".addexercisetextbox").autocomplete('txt/exercise.php');
                 bindAllTabs3("#"+r+"split .sectionname h1");
                 $(".list").sortable({//duplicate function. DUnno any other way how to. Can reduce by targetting the rsplit
    	handle   : ".draggertab",
    	update   : function(event, ui){
    		var id = ui.item.attr('id'),
    			rel = ui.item.attr('rel'),
    			sid = parseInt(ui.item.parent().parent().attr('id'));
    			t = setTimeout("saveListOrder('"+sid+"','"+id+"', '"+rel+"')",500);
    	},
    	forcePlaceholderSize: true
    });
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
        } else {
        	$("#addsplitcancel").click();
        }
        return false; // prevent default form submission
 	})
    $('.addexercisesubmit').live("click",function(){
		thiscache = $(this).parent().parent().parent();
        // HTML tag whitelist. All other tags are stripped.
    	var $whitelist = '<b><i><strong><em><a>',
    		sid = thiscache.find(".current-split").val(),
    		newListItemText = strip_tags(cleanHREF(thiscache.find(".addexercisetextbox").val()), $whitelist),
    		URLtext = escape(newListItemText),
    		newListItemRel = thiscache.prev().children().length+1;
        if (newListItemText.length > 0) {
        
            // prevent multiple submissions by disabling button until save is successful
            $(".addexercisesubmit").attr("disabled", true);
        	$(".chooseoldexercise").attr("disabled",true);
        	$("#popupsubmit").attr("disabled",true);
            $.ajax({
    			type: "POST",
    			url: "/db-interaction/lists.php",
    			data: "action=addnew&sid=" + sid
    				+ "&text=" + URLtext
    				+ "&pos=" + newListItemRel,
    			success: function(r){
                  $("#"+sid+"split").find(".list").append("<ul id='" +r+ "' rel='"+newListItemRel+"' class='exerciseEdit' name='exerciseList'><p class='exercise'>"+newListItemText+"</p><span class='setnrep'></span><li class='setAdd' hidden><p class='set'></p><p class='weight'></p><p class='lbkg'></p> <p class='rep'></p><p class='comment'></p></li><div class='editbuttons'><input type='button' title='edit' class='jeditable-activate sp' /><input title='add more sets' type='button' class='morelists sp'/><div title='delete' class='deletetab sp'></div></div><div title='hold mouse to drag' class='draggertab sp'></div></ul>");
                 bindAllTabs2("#"+r+" .setAdd");
                 //bindAllTabs3("#"+theResponse+" .exercisename .exercise");
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
        	$(".addexercisesubmit").removeAttr("disabled");
        	$(".chooseoldexercise").removeAttr("disabled");
        	$("#popupsubmit").removeAttr("disabled");
        	thiscache.find(".addexercisecancel").click();
        } else {
        	thiscache.find(".addexercisecancel").click();
        }
        return false; // prevent default form submission
    });
		$("#addsplitbuttons").hide();//PUT IN THE INC AS HIDDEN WHEN INITIALIE
		$("#addsplittextbox").hide();
		$("#addsplittrigger").click(function(){
			$("#addsplittextbox").animate({"height": "20px","width": "300px",}, "slow" );
			$("#addsplittextbox").fadeIn("slow");
			$("#addsplittextbox").focus();
			$("#addsplitbuttons").fadeIn("slow");
			$("#addsplittrigger").fadeOut("slow");
			$(this).val('');
			return false;
		});
		$("#addsplitcancel").live("click",function(){
			$("#addsplittextbox").val('');
			$("#addsplittextbox").animate({"height": "20px","width": "160px",}, "slow" );
			$("#addsplitbuttons").fadeOut("slow");
			$("#addsplittextbox").fadeOut("slow");
			$("#addsplittrigger").fadeIn("slow");
			});
			

		
		$(".addsubmit").live("click",function(){
			$(this).parent().find(".addexercisetextbox")
			.animate({"height": "20px","width": "300px",}, "slow" )
			.fadeIn("slow")
			.focus();
			$(this).parent().find(".addexercisebuttons").fadeIn("slow");
			$(this).parent().find(".oldexercisediv").fadeIn("slow");
			$(this).fadeOut("slow");
			$(this).val('Add');
		});
		$(".addexercisecancel").live("click",function(){
			$(this).parent().parent().find(".addexercisetextbox")
			.val('')
			.animate({"height": "20px","width": "0px",}, "slow" )
			.fadeOut("slow");
			$("#popupsubmit").unbind();
			$(this).parent().parent().find(".addexercisebuttons").fadeOut("slow");
			$(this).parent().parent().parent().find(".oldexercisediv").fadeOut("slow");
			$(this).parent().parent().parent().find(".addsubmit").fadeIn("slow");
			});	

		$(".exerciseEdit").live("mouseover", function(){
			$(this).find(".editbuttons").show();
			$(this).find(".dragset").show();
		});
		$(".exerciseEdit").live("mouseout", function(){
			if($(this).find(".editbuttons").find(".deletetab").data("readyToDelete") != "go for it"){
			$(this).find(".editbuttons").hide();
			$(this).find(".dragset").hide();
		}
		});
		
		$(".dragup").live('click', function(){
			thiscache = $(this).parent().parent();
			set = parseInt(thiscache.attr('id'));
			list = thiscache.attr('class');
			pos = thiscache.attr('rel');
			maxpos = thiscache.parent().children().length;
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/lists.php",
    			data: "action=dragset&set=" + set
    				+ "&list=" + list
    				+ "&direction=up&pos=" + pos
    				+ "&maxpos=" +maxpos,
    			success: function(){
                  
					thiscache.insertBefore(thiscache.prev());

					var count=1;
	        		$("#"+list).find('.setnrep li').each(function() {
	        			$(this).attr('rel', count);
	        			count++;
	        		});
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		});
		$(".dragdown").live('click', function(){
			thiscache = $(this).parent().parent();
			set = parseInt(thiscache.attr('id'));
			list = thiscache.attr('class');
			pos = thiscache.attr('rel');
			maxpos = thiscache.parent().children().length;
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/lists.php",
    			data: "action=dragset&set=" + set
    				+ "&list=" + list
    				+ "&direction=down&pos=" + pos
    				+ "&maxpos=" +maxpos,
    			success: function(){
                  
					
					thiscache.insertAfter(thiscache.next());
				var count=1;
	        		$("#"+list).find('.setnrep li').each(function() {
	        			$(this).attr('rel', count);
	        			count++;
	        		});
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		});
    	$.editable.addInputType('exerciseEdit', {
                element : function(settings, original) {
                	//var exerciseSelect = $('<textarea id="exercise" COLS=15 ROWS=2 >');
                	var setInput = $('<input id="set" maxlength = "2" size="2"/>');
                	var weightInput = $('<input id="weight" maxlength = "5" size="4" />');
                	var lbkgSelect = $('<select id="lbkg">');
                	var repInput = $('<input id="rep" maxlength = "3" size="3" />');
                	var commentInput = $('<textarea id="comment" COLS=20 ROWS=2>');
            //$(exerciseSelect).keypress(function(e) {
			//	if(e.keyCode==96) return false;
			//	});
			//$(commentInput).keypress(function(e) {
			//	if(e.keyCode==96) return false;
			//	}); DISABLING CERTAIN KEYS
          	$(setInput).keypress(function(event) {
        		return /\d/.test(String.fromCharCode(event.keyCode));
    		})
    		$(weightInput).keypress(function(event) {
        		return /\d|\./.test(String.fromCharCode(event.keyCode));
    		})
    		$(repInput).keypress(function(event) {
        		return /\d/.test(String.fromCharCode(event.keyCode));
    		})
                    
                   $(this).append(setInput);
                   $(this).append("&emsp;");
                   $(this).append(weightInput);
				   $(this).append("&nbsp;");
					var option = $('<option>').val('lbs').append('lbs');
					lbkgSelect.append(option);
					var option = $('<option>').val('kg').append('kg');
					lbkgSelect.append(option);
					$(this).append(lbkgSelect);
                   $(this).append("&emsp;");
					$(this).append(repInput);
					$(this).append("&emsp;");
					$(this).append(commentInput);
					
                    /* Hidden input to store value which is submitted to server. */
                    var hidden = $('<input type="hidden">');
                    $(this).append(hidden);
                    return(hidden);
                },
                submit: function (settings, original) {

                },
                content : function(string, settings, original) {

                	var $data = $(string);            	
					var data = {};
					$data.each(function () {
					    var $t = $(this);
					    data[$t.attr('class')] = {
                           class: $t.attr('class'),
                           value: $t.text()};
                           //alert(data['set'].value);
					});
      					//$("#exercise", this).val(data['exercise'].value);
        				$("#set", this).val(data['set'].value);
        				$("#lbkg", this).val(data['lbkg'].value);
        				$("#weight", this).val(data['weight'].value);
        				$("#rep", this).val(data['rep'].value);
        				$("#comment", this).val(data['comment'].value);
    				}
     
            });
             function deletesection(thiscache){
             	split = parseInt(thiscache.parent().parent().attr('id'));
             	list = $("#current-list").val();
             	pos = thiscache.parent().parent().attr('rel');
             	$.ajax({
    			type: "POST",
    			url: "/db-interaction/lists.php",
    			data: {
    					"list":list,
    					"split":split,
    					"action":"deletesection",
    					"pos":pos
    				},
    			success: function(r){
    						var position = 0;
    	            	thiscache
    	            		.parent().parent()
    	            			.hide("explode", 400, function(){$(this).remove()});
    	            	thiscache.parent().parent().parent()
            				.children('ul')
            					.not(thiscache.parent().parent())
            					.each(function(){
    				            		$(this).attr('rel', ++position);
    				            	});
    				},
    			error: function() {
    			    $("#main").prepend("Deleting the item failed...");//FIX THIS!!!!!!!!!!!!!!!!!!!!!!!!!!!
    			}
    		});
         };
          $(".deletesection").live("click", function(){
    	var thiscache = $(this);
   		if (thiscache.data("readyToDelete") != "go for it")	{	
    	$(document.body).bind("click",function() {
    		$(".readydelete").remove();
        	thiscache.data("readyToDelete", "not yet");
    		$(document.body).unbind();
    		thiscache.unbind();
		});
		
		thiscache.bind("click",function(e){
			e.stopPropagation();
			deletesection(thiscache);
		});
        	thiscache.parent().append("<p class='readydelete' hidden>are you sure?</p>");
        	$(".readydelete").slideToggle("fast");
        	thiscache.data("readyToDelete", "go for it");
    	}
    });
    bindAllTabs2(".setAdd");
    bindAllTabs(".setnrep li");
    bindAllTabs3(".sectionname h1");
    bindAllTabs4(".programName h1");

};
// This is seperated to a function so that it can be called at page load
// as well as when new list items are appended via AJAX

//function bindAllTabs3(editableTarget) {
     
    // CLICK-TO-EDIT on list items
   // $(editableTarget).editable("/db-interaction/lists.php", {
    //    id        : 'listItemID',
     //   indicator : 'Saving...',
        //tooltip   : 'Double-click to edit...',
     //   event     : 'editclick',
    //	select : false,
    //	onblur: "ignore",
    //    submit    : '<button class="save_button">Save</button>',
    //    submitdata: function(){
    //    	return {action : "update",
        			//listID: $(this).parent().parent().parent().attr('id')};
    //	}
  //  });
    
	
//}
function bindAllTabs(editableTarget){
             $(editableTarget).editable("/db-interaction/lists.php", {
             		id         : 'listItemID', 
                    type       : "exerciseEdit",
                    //submit     : '<button class="save_button">Save</button>',
                    style      : "display: inline",
                    event     : 'editclick',
                    submitdata: function(){
                    	var key = parseInt($(this).attr('id'));
                    	var value = [$("#"+key+"set").find("#set").val(), $("#"+key+"set").find("#weight").val(), $("#"+key+"set").find("#lbkg").val(), $("#"+key+"set").find("#rep").val(), $("#"+key+"set").find("#comment").val()];
                    	var hash = {};
                    	hash["set"] = value[0];
                    	hash["weight"]=value[1];
                    	hash["lbkg"]=value[2];
                    	hash["rep"]=value[3];
                    	hash["comment"]=value[4];
                    	hash["listID"]=parseInt($(this).attr('id'));
                    	hash["action"] = "updateExercise";
                    	return hash;
                	}
            });

}
function bindAllTabs2(editableTarget){
             $(editableTarget).editable("/db-interaction/lists.php", {
             		id         : 'setID', 
                    type       : "exerciseEdit",
                    submit     : 'Add',
                    style      : "display: inline",
                    //onblur	:cancel,
                    event     : 'editclick',
                    submitdata: function() {
                    	$(this).find(":submit").attr("disabled","true");
                    	var value = [$(this).find("#set").val(), $(this).find("#weight").val(), $(this).find("#lbkg").val(), $(this).find("#rep").val(), $(this).find("#comment").val()];
                    	var hash = {};
                    	hash["set"] = value[0];
                    	hash["weight"]=value[1];
                    	hash["lbkg"]=value[2];
                    	hash["rep"]=value[3];
                    	hash["comment"]=value[4];
                    	hash["listID"]=$(this).parent().attr('id');
                    	hash["pos"]=$(this).prev().children().length+1;
                    	hash["action"] = "addSet";
           			return  hash;
            }
            });

}
function bindAllTabs3(editableTarget){
	$(editableTarget).editable("db-interaction/lists.php", {
        id        : 'SectionID',
        indicator : 'Saving...',
        event: 'editclick',
    	select : true,
    	//onblur: "cancel",
    	//cancel:"cancel",
    	submit:"save",
        submitdata: function(){
                    	var hash = {};
                    	hash["sname"] = $(this).find('input').val();
                    	hash["sid"] = parseInt($(this).parent().parent().attr('id'));
                    	hash["action"] = "updateSectionname";
                    	return hash;
	}
    });
	}
function bindAllTabs4(editableTarget){
	$(editableTarget).editable("db-interaction/lists.php", {
        id        : 'SectionID',
        indicator : 'Saving...',
        event: 'editclick',
    	select : true,
    	//onblur: "cancel",
    	//cancel:"cancel",
    	submit:"save",
        submitdata: function(){
                    	var hash = {};
                    	hash["pname"] = $(this).find('input').val();
                    	hash["pid"] = parseInt($(this).parent().attr('pid'));
                    	hash["action"] = "updateProgramname";
                    	return hash;
	}
    });
	}
function initializeProgram(){
	
	 $(".addpostcomment").autogrow(); 
        $(".addpostcomment").charCounter(150);
           
	$("#commentcancel").live("click",function(){
		$(".addpostcomment").val("");
		});
	$("#commentsubmit").live("click",function(){
		var $whitelist = '<b><i><strong><em><a>',
    		text = strip_tags(cleanHREF($(this).prev().prev().val()), $whitelist);
    		//URLtext = escape(text);
        if (text.length > 0) {
        	$("#commentsubmit").attr("disabled", true);
		thiscache = $(this);
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"addComment",
    				"type":1,
    				"pid":$(this).parent().parent().attr("id"),
    				"text":text
				},
    			success: function(r){
    				thiscache.parent().next().prepend(r);
					$(".addpostcomment").val("");
					$("#"+$(r).attr('id')).each(function(){
					$(this)[0].onmouseover=function(){
						$(this).find(".delete").show();
						$(this).css('background','#e1f0fd');
					}
					$(this)[0].onmouseout=function(){
						$(this).find(".delete").hide();
						$(this).css('background','none');
					}
				});
				$("#commentsubmit").removeAttr("disabled"); //PUT IT IN THE SECOND SUCCESS WHEN SUBMIT TO NEWS
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		}else {
        	$(this).prev().prev().val("");
        }
		});
	$(".delete").live("click",function(){
		thiscache=$(this);
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"deleteComment",
    				"type":1,
    				"cid":parseInt(thiscache.parent().attr('id'))
				},
    			success: function(){
    				thiscache.parent().remove();
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		});
	$(".comments").children().each(function(){
		thiscache=$(this);
		$(this)[0].onmouseover=function(){
			$(this).find(".delete").show();
			$(this).css('background','#e1f0fd');
		}
		$(this)[0].onmouseout=function(){
		$(this).find(".delete").hide();
		$(this).css('background', 'none')
	}
	});
	$(".kudos2u").live("click",function(){
			thiscache=$(this);
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"givekudos",
    				"type":1,
    				"pid":$(this).parent().attr('id')
				},
    			success: function(){
    				if(thiscache.next().children().length==0){
						thiscache.next().append("Kudos to you Sir!<div class='kudoscount'>x1</div>");
						thiscache.remove();
					}else{						thiscache.next().find(".kudoscount").text("x"+eval(parseInt(thiscache.next().find(".kudoscount").text().substring(1))+1));
					thiscache.remove();
					}
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		})
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
function saveListOrder(sectionID, itemID, itemREL){
	var i = 1;
	$('#'+sectionID+'split').find('.list .exerciseEdit').each(function() {
		if($(this).attr('id') == itemID) {
			var startPos = itemREL,
				currentPos = i;
			if(startPos < currentPos) {
				var direction = 'down';
			} else {
				var direction = 'up';
			}
			var token = $('#token').val(),
				postURL = "action=sort&currentSectionID="+sectionID
					+"&startPos="+startPos
					+"&currentPos="+currentPos
					+"&direction="+direction
					+ "&token=" + token;

			$.ajax({
				type: "POST",
				url: "/db-interaction/lists.php",
				data: postURL,
				success: function(msg) {
	        		// Resets the rel attribute to reflect current positions
					var count=1;
	        		$('#'+sectionID+'split').find('.list ul').each(function() {
	        			$(this).attr('rel', count);
	        			count++;
	        		});
	        	},
	        	error: function(msg) {
	        	    // (chris): I commented this out for now, was throwing sometimes...
	        		//alert(msg);
	        	}
			});
	    }
		i++;
	});
}
