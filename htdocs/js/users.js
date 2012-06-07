function profileEdit(mofb,yofb,ddofb,sex,wunit,privacy,trackO,previousLocation,previousEmail,previousPhone,previousHeight,userweight,mUnit,settings,search)
{
//!!!!BEGIN CREATING DATE PICKER!!!
	d = new Date;
	thisyear = d.getFullYear();
	function dropBoxCreateDays(days){
	var daydropbox = " <select id='DDOfB' class='"+days+"days'>";
	for (var i=1; i<days+1; i++){
		var append = "<option value="+i+">"+i+"</option>";
		daydropbox = daydropbox+append;
		}
	daydropbox = daydropbox+"</select>"
	return daydropbox;//Creates a string for the drop down menu
	};
	var daydropbox31 = dropBoxCreateDays(31);//For the number of days in the month to prevent stuff like 30th of Feb
	var daydropbox30 = dropBoxCreateDays(30);
	var daydropbox28 = dropBoxCreateDays(28);
	var daydropbox29 = dropBoxCreateDays(29);
	var yeardropbox = " <select id='YOfB'>"; //Creates the dropbox for the years
	for (var i=0; i<100; i++){
		year = thisyear-i;
		var append = "<option value="+year+">"+year+"</option>";
		yeardropbox = yeardropbox+append;
	}
	yeardropbox = yeardropbox+"</select>";
	$("#MOfB").val(mofb);
	if ($('#MOfB').val()==1||3||5||7||8||10||12){
		$("#datepicker").append(daydropbox31);//Puts in day drop box for months with 31 days
	}
	else if($('#MOfB').val()==4||6||9||11){
		$("#datepicker").append(daydropbox30);//Puts in day drop box for months with 30 days
	}
	else if($('#MOfB').val()==2&&yofb%4==0){
		$("#datepicker").append(daydropbox29);//Puts in day drop box for a leap year in feb
	}
	else{
		$("#datepicker").append(daydropbox28);//Puts in day drop box for february
	}
	$("#DDOfB").val(ddofb);
	$("#datepicker").append(yeardropbox);
	$("#YOfB").val(yofb);
	$("#MOfB").change(function(){//If user changes the month, ensures that the day drop box matches with the number of days in the given month
		if ($('#MOfB').val()==1||3||5||7||8||10||12){
		$("#DDOfB").replaceWith(daydropbox31);//Puts in day drop box for months with 31 days
	}
		if($('#MOfB').val()==4||6||9||11){

		$("#DDOfB").replaceWith(daydropbox30);//Puts in day drop box for months with 31 days
	}
		if($('#MOfB').val()==2&&$("#YOfB").val()%4==0){
		$("#DDOfB").replaceWith(daydropbox29);//Puts in day drop box for a leap year in feb
	}
	else{
		$("#DDOfB").replaceWith(daydropbox28);//Puts in day drop box for february
	}
	});
	$("#YOfB").change(function(){//If user changes year, ensure that day dropbox matches with number of days in given month (only applies to feb for leap years)
		if($('#MOfB').val()==2&&$("#YOfB").val()%4==0){
		$("#DDOfB").replaceWith(daydropbox29);//Puts in day drop box for a leap year in feb
	}
	if($('#MOfB').val()==2&&$("#YOfB").val()%4!=0){//Only applies if user is changing from a leap year to a non leap year and the month currently selected is feb
		$("#DDOfB").replaceWith(daydropbox28);
	}
	});
//!!!FINISH DATEPICKER!!!!
	$("#topbar").hide();//hide it in the sidebar?	
	var priarray = new Array
	priarray = baseTenConvert(privacy,16);
	for (var i=0; i<16; i++)
	{
		$("#privacy"+i).val(priarray[i]);
	}
	var trackarray = new Array
	trackarray = baseTwoConvert(trackO, 7);
	for (var i=0; i<7; i++)
	{
		$("#tprivacy"+i).val(trackarray[i]);
	}
	var mUnitArray = new Array
	mUnitArray = baseTwoConvert(mUnit,9);
	for (var i=0; i<9; i++)
	{	thiscache3 = $("#m"+i);
		if(thiscache3.val()==0){
			thiscache3.val("");
		}
		$("#inchorcm"+i).val(mUnitArray[i]);
	}
	var settingsArray = new Array
	settingsArray = baseTwoConvert(settings,1);
	$("#setting0").val(settingsArray[0]);
	$("#refersetting0").val(settingsArray[0]);
	$("#setting0").change(function(){
		$("#refersetting0").val($(this).val());
	});
	$("#refersetting0").change(function(){
		$("#setting0").val($(this).val());
	});
	$("#eprofile").addClass("hovered");
	$("#trackertable").hide();
	$("#privacytable").hide();
	$("#goaltable").hide();
	$("#privacyoptions").click(function(){
		$("#edittable").hide();
		$("#privacytable").show();
		$("#trackertable").hide();
		$("#goaltable").hide();
		$(this).addClass("hovered");
		$("#eprofile").removeClass("hovered");
		$("#trackeroptions").removeClass("hovered");
		$("#goaloptions").removeClass("hovered");
		$("#measurements").hide();
		$(".hoverheading:contains('Body Measurements')").removeClass("hovered");
	});
	$(".hoverheading:contains('Body Measurements')").live("click",function(){
		$("#privacytable").hide();
		$("#edittable").hide();
		$("#trackertable").hide();
		$("#goaltable").hide();
		$("#eprofile").removeClass("hovered");
		$(this).addClass("hovered");
		$("#privacyoptions").removeClass("hovered");
		$("#trackeroptions").removeClass("hovered");
		$("#goaloptions").removeClass("hovered");
		$("#measurements").show();
	});	
	$("#eprofile").click(function(){
		$(".hoverheading:contains('Body Measurements')").removeClass("hovered");
		$("#privacytable").hide();
		$("#edittable").show();
		$("#trackertable").hide();
		$("#goaltable").hide();
		$(this).addClass("hovered");
		$("#privacyoptions").removeClass("hovered");
		$("#trackeroptions").removeClass("hovered");
		$("#goaloptions").removeClass("hovered");
		$("#measurements").hide();
	});
	$("#trackeroptions").click(function(){
		$(".hoverheading:contains('Body Measurements')").removeClass("hovered");
		$("#trackertable").show();
		$("#edittable").hide();
		$("#privacytable").hide();
		$("#goaltable").hide();
		$(this).addClass("hovered");
		$("#eprofile").removeClass("hovered");
		$("#privacyoptions").removeClass("hovered");
		$("#goaloptions").removeClass("hovered");
		$("#measurements").hide();
	});
	$("#goaloptions").live("click",function(){
		$(".hoverheading:contains('Body Measurements')").removeClass("hovered");
		$("#trackertable").hide();
		$("#edittable").hide();
		$("#privacytable").hide();
		$("#goaltable").show();
		$(this).addClass("hovered");
		$("#privacyoptions").removeClass("hovered");
		$("#trackeroptions").removeClass("hovered");
		$("#eprofile").removeClass("hovered");
		$("#measurements").hide();
	});
	$("#measurements").hide();
	var success = 0;
// !!!!!!!!!!!!!! START GOALS !!!!!!!!!!!!!!!!!!!!!!
function showerror(thiscache){t = setTimeout(function(){thiscache.fadeOut(1000)},3000)};
function addNewGoalDB(type,weight,reps,lbkg,date,other){
	$.ajax({
       type: "POST",
       url: "db-interaction/users.php",
       data: "action=newgoal&goaltype="+type+
       "&goalweight="+weight+
       "&goalreps="+reps+
       "&goallbkg="+lbkg+
       "&goalother="+other+
       "&goaldate="+date,
       success: function(r){
        //window.location.replace("profile.php?user="+r);
                 // bindAllTabs(".list li[rel='"+newListItemRel+"'] span");
                 // $("#add-new-submit").removeAttr("disabled");
        if(type==0){
        $("#newgoaltable").append("<tr id='"+r+"' style='display:none'><td><input type='checkbox' class='goalshowornot' id='showgoal"+r+"' checked='checked'></td><td><label for='showgoal"+r+"'>I will be "+weight+" "+getlbkg(lbkg)+" massive by "+date+"</label></td><td><button type='button' class='removebutton'>Remove</button></td></tr>");
    	}
    	if(type==1){
    		$("#newgoaltable").append("<tr id='"+r+"' style='display:none'><td><input type='checkbox' class='goalshowornot' id='showgoal"+r+"' checked='checked'></td><td><label for='showgoal"+r+"'>I will be "+weight+" "+getlbkg(lbkg)+" slim by "+date+"</label></td><td><button type='button' class='removebutton'>Remove</button></td></tr>");
    	}
    	if(type==2){
    		$("#newgoaltable").append("<tr id='"+r+"' style='display:none'><td><input type='checkbox' class='goalshowornot' id='showgoal"+r+"' checked='checked'></td><td><label for='showgoal"+r+"'>I will Bench "+weight+" "+getlbkg(lbkg)+" for "+reps+" reps by "+date+"</label></td><td><button type='button' class='removebutton'>Remove</button></td></tr>")
    	}
    	if(type==3){
    		$("#newgoaltable").append("<tr id='"+r+"' style='display:none'<td><input type='checkbox' class='goalshowornot' id='showgoal"+r+"' checked='checked'></td>><td><label for='showgoal"+r+"'>I will Deadlift "+weight+" "+getlbkg(lbkg)+" for "+reps+" reps by "+date+"</label></td><td><button type='button' class='removebutton'>Remove</button></td></tr>");
    	}
    	if(type==4){
    		$("#newgoaltable").append("<tr id='"+r+"' style='display:none'><td><input type='checkbox' class='goalshowornot' id='showgoal"+r+"' checked='checked'></td><td><label for='showgoal"+r+"'>I will Squat "+weight+" "+getlbkg(lbkg)+" for "+reps+" reps by "+date+"</label></td><td><button type='button' class='removebutton'>Remove</button></td></tr>");
    	}
    	if(type==5){
    		$("#newgoaltable").append("<tr id='"+r+"' style='display:none'><td><input type='checkbox' class='goalshowornot' id='showgoal"+r+"' checked='checked'></td><td><label for='showgoal"+r+"'>"+other+"</label></td><td><button type='button' class='removebutton'>Remove</button></td></tr>");
    	}
    	$("#"+r).slideToggle();
    	$("#"+r).find(".removebutton").bind("click",function(){
    		var thiscache = $(this).parent().parent(); //Targets the row
			$.ajax({
			type: "POST",
			url: "db-interaction/users.php",
			data: "action=deletegoal&goalid="+thiscache.attr('id'),
			success: function(){
			if($("#newgoaltable").find("tr").length==1){
				$("#newgoalheading").slideToggle("normal",function(){$(this).remove()});
			}else{
			thiscache.slideToggle("normal",function(){$(this).remove()});
		};
			//alert($("#newgoaltable").find("tr").length);
       	//alert("lol");
   },
       error: function(){
       	
       }
   });
		});
       },
       error: function(){
           // should be some error functionality here
       }
      });
}
function testInt(text){
   if (parseFloat(text) != parseInt(text) || isNaN(text) || text==""||parseInt(text)<=0){
    $("#errorline").text("Invalid weight input (no decimals please).").show();
    t = setTimeout(function(){$("#errorline").fadeOut(1000)},3000);
    return false;
   }else{
    return true;
   }
  }
	$(".removebutton").bind("click",function(){
    	var thiscache = $(this).parent().parent(); //Targets the row
		$.ajax({
		type: "POST",
		url: "db-interaction/users.php",
		data: "action=deletegoal&goalid="+thiscache.attr('id'),
		success: function(){
			if($("#existinggoals").find("tr").length==2){
				$("#existinggoals").remove();
			}else{
		thiscache.remove();
	}
		},
		error: function(){}});});
	$("#addnewgoalbutt").live("click",function(){
		if ($("input[name='goal massive']").attr("checked")==true){
			if ($("#goalweight0").val()=="" && $("#goaldate0").val()==""){//IF WEIGHT AND DATE FAIL
				$("#gainweightrow").find(".errormessage").text("Please enter data into the fields").show();
				t = setTimeout(function(){$("#gainweightrow").find(".errormessage").fadeOut(1000)},3000);
				return false;
			}
			else if ($("#goalweight0").val()==""){//IF WEIGHT FAIL
				$("#gainweightrow").find(".errormessage").text("Bit pointless trying to gain 0 weight").show();
				t = setTimeout(function(){$("#gainweightrow").find(".errormessage").fadeOut(1000)},3000);
				return false;				
			}
			else if ($("#goaldate0").val()==""){//IF DATE FAIL
				$("#gainweightrow").find(".errormessage").text("Please enter a date").show();
				t = setTimeout(function(){$("#gainweightrow").find(".errormessage").fadeOut(1000)},3000);
				return false;				
			}
			else {//IF NOTHING FAILS
				if (weightchecker($("#pweight").val(),$("#goalweight0").val(),$("#plbkg").val(),$("#goallbkg0").val())<=0){//IF USER FAIL
					$("#gainweightrow").find(".errormessage").text("How much weight are you trying to gain exactly? (P.S. Your goal weight is the same or lighter than your actual weight)").show();
					t = setTimeout(function(){$("#gainweightrow").find(".errormessage").fadeOut(1000)},4500);
					return false;
				}
				else if (testdateGoal($("#goaldate0").val())==0){//USER FAIL ON DATE
					$("#gainweightrow").find(".errormessage").text("This is not a valid date. (MM/DD/YYYY)").show();
					t = setTimeout(function(){$("#gainweightrow").find(".errormessage").fadeOut(1000)},3000);
					return false;
				}
				else if (testdateGoal($("#goaldate0").val())==2){
					$("#gainweightrow").find(".errormessage").text("No point setting a goal in the past. Today is "+eval(today.getMonth()+1)+"/"+today.getDate()+"/"+today.getFullYear()).show();
	    			t = setTimeout(function(){$("#gainweightrow").find(".errormessage").fadeOut(1000)},3000);
	    			return false;
				}
				else{//IF USER SUCESS
					if(testInt($("#goalweight0").val())==true){
						if ($("#newgoalheading").text().search(" ")==-1){//IF THE HEADING IS NOT THERE
							$("#addnewgoal").append("<span id='newgoalheading' style='display:none'><h2>Newly added Goals</h2><table id='newgoaltable'></table></span>");
							$("#newgoalheading").slideToggle();
						}
						addNewGoalDB(0,$("#goalweight0").val(),0,$("#goallbkg0").val(),$("#goaldate0").val(),"");
						$("#gainweight").slideToggle();
						$("input[name='goal massive']").attr('checked',false);
						$("#goalweight0").val("");
						$("goallbkg0").val(0);
						$("#goaldate0").val("");
						$("#othergoals").slideToggle();
					}
				}
			}
		}
		if ($("input[name='goal less']").attr("checked")==true){
			if ($("#goalweight1").val()=="" && $("#goaldate1").val()==""){//IF WEIGHT AND DATE FAIL
				$("#loseweightrow").find(".errormessage").text("Please enter data into the fields").show();
				t = setTimeout(function(){$("#loseweightrow").find(".errormessage").fadeOut(1000)},3000);
				return false;
			}
			else if ($("#goalweight1").val()==""){//IF WEIGHT FAIL
				$("#loseweightrow").find(".errormessage").text("Bit pointless trying to lose 0 weight").show();
				t = setTimeout(function(){$("#loseweightrow").find(".errormessage").fadeOut(1000)},3000);
				return false;				
			}
			else if ($("#goaldate1").val()==""){//IF DATE FAIL
				$("#loseweightrow").find(".errormessage").text("Please enter a date").show();
				t = setTimeout(function(){$("#loseweightrow").find(".errormessage").fadeOut(1000)},3000);
				return false;				
			}
			else {//IF NOTHING FAILS
				if (weightchecker($("#pweight").val(),$("#goalweight1").val(),$("#plbkg").val(),$("#goallbkg1").val())>=0){//IF USER FAIL
					$("#loseweightrow").find(".errormessage").text("How much weight are you trying to lose exactly? (P.S. Your goal weight is the same or heavier than your actual weight)").show();
					t = setTimeout(function(){$("#loseweightrow").find(".errormessage").fadeOut(1000)},4500);
					return false;					
				}
				else if (testdateGoal($("#goaldate1").val())==0){//USER FAIL ON DATE
					$("#loseweightrow").find(".errormessage").text("This is not a valid date. (MM/DD/YYYY)").show();
					t = setTimeout(function(){$("#loseweightrow").find(".errormessage").fadeOut(1000)},3000);
					return false;
				}
				else if (testdateGoal($("#goaldate1").val())==2){
					$("#loseweightrow").find(".errormessage").text("No point setting a goal in the past. Today is "+eval(today.getMonth()+1)+"/"+today.getDate()+"/"+today.getFullYear()).show();
	    			t = setTimeout(function(){$("#loseweightrow").find(".errormessage").fadeOut(1000)},3000);
	    			return false;
				}
				else{//IF USER SUCESS
					if(testInt($("#goalweight1").val())==true){
						if ($("#newgoalheading").text().search(" ")==-1){//IF THE HEADING IS NOT THERE
							$("#addnewgoal").append("<span id='newgoalheading' style='display:none'><h2>Newly added Goals</h2><table id='newgoaltable'></table></span>");
							$("#newgoalheading").slideToggle();
						}
						addNewGoalDB(1,$("#goalweight1").val(),0,$("#goallbkg1").val(),$("#goaldate1").val(),"");
						$("#loseweight").slideToggle();
						$("input[name='goal less']").attr('checked',false);
						$("#goalweight1").val("");
						$("goallbkg1").val(wunit);
						$("#goaldate1").val("");
						$("#othergoals").slideToggle();
					}
				}
			}
		}
		$("#pumpiron").find("td").each(function(){
			if($(this).find("input[type='checkbox']").attr('checked')==true){
				if($(this).find(".goalweight").val()=="" && $(this).find(".goaldate").val()=="" && $(this).find(".goalreps").val()==""){
					$(this).find(".errormessage").text("Please enter data into the fields").show();
					showerror($(this).find(".errormessage"));
					
				}
				else if ($(this).find(".goalweight").val()==(""||0)){
					if($(this).attr('id')=="iron0"){
						$(this).find(".errormessage").text("So you're trying to bench air?").show();
						showerror($(this).find(".errormessage"));
					}
					if($(this).attr('id')=="iron1"){
						$(this).find(".errormessage").text("Surely you can do better than deadlifting nothing").show();
						showerror($(this).find(".errormessage"));
					}
					if($(this).attr('id')=="iron2"){
						$(this).find(".errormessage").text("What are you squatting exactly?").show();
						showerror($(this).find(".errormessage"));
					}
				}
				else if ($(this).find(".goalreps").val()==""){
					$(this).find(".errormessage").text("Please enter number of reps").show();
					showerror($(this).find(".errormessage"));
				}
				else if ($(this).find(".goaldate").val()==""){
					$(this).find(".errormessage").text("Please enter a date").show();
					showerror($(this).find(".errormessage"));
				}
				else {
					if (testdateGoal($(this).find(".goaldate").val())==0){//USER FAIL ON DATE
						$(this).find(".errormessage").text("This is not a valid date. (MM/DD/YYYY)").show();
						showerror($(this).find(".errormessage"));
					}
					else if (testdateGoal($(this).find(".goaldate").val())==2){
						$(this).find(".errormessage").text("No point setting a goal in the past. Today is "+eval(today.getMonth()+1)+"/"+today.getDate()+"/"+today.getFullYear()).show();
						showerror($(this).find(".errormessage"));
					}
					else{
						if(testInt($(this).find(".goalreps").val())==true && testInt($(this).find(".goalweight").val())==true){
							if ($("#newgoalheading").text().search(" ")==-1){//IF THE HEADING IS NOT THERE
								$("#addnewgoal").append("<span id='newgoalheading' style='display:none'><h2>Newly added Goals</h2><table id='newgoaltable'></table></span>");
								$("#newgoalheading").slideToggle();
							}
							if($(this).attr('id')=="iron0"){
								addNewGoalDB(2,$(this).find(".goalweight").val(),$(this).find(".goalreps").val(),$(this).find(".goallbkg").val(),$(this).find(".goaldate").val(),"");
							}
							if($(this).attr('id')=="iron1"){
								addNewGoalDB(3,$(this).find(".goalweight").val(),$(this).find(".goalreps").val(),$(this).find(".goallbkg").val(),$(this).find(".goaldate").val(),"");
							}
							if($(this).attr('id')=="iron2"){
								addNewGoalDB(4,$(this).find(".goalweight").val(),$(this).find(".goalreps").val(),$(this).find(".goallbkg").val(),$(this).find(".goaldate").val(),"");
							}
							$(this).find("input[tupe='checkbox']").attr('checke',false);
							$("input[name='goal iron']").attr('checked',false);
							$(this).find(".goalweight").val("");
							$(this).find(".goallbkg").val(wunit);
							$(this).find(".goaldate").val("");
							$(this).find(".goalreps").val("");
							success = 1;
						}
					}	
				}	
			}
		});
		if (success==1){
			$("#pumpiron").slideToggle();
			$("#othergoals").slideToggle();
			success=0;
		}
		if ($("input[name='goal less']").attr("checked")==false && $("input[name='goal massive']").attr("checked")==false && $("input[name='goal iron']").attr("checked")==false){
			if($("#othergoals").val()!=""){
				if ($("#newgoalheading").text().search(" ")==-1){//IF THE HEADING IS NOT THERE
					$("#addnewgoal").append("<span id='newgoalheading' style='display:none'><h2>Newly added Goals</h2><table id='newgoaltable'></table></span>");
					$("#newgoalheading").slideToggle();
				}
				addNewGoalDB(5,"","","","",$("#othergoals").val());
				$("#othergoals").val("");
			}
		}
	}); 
	
	$("input[name='goal massive']").live("click",function(){
		$("#gainweight").slideToggle();
		if ($("input[name='goal less']").attr("checked")==true || $("input[name='goal iron']").attr('checked')==true){
			$("#loseweight").hide();
			$("#pumpiron").hide();
			$("#goalweight1").val("");
			$("#goallbkg1").val(wunit);
			$("#goaldate1").val("");
			$("#pumpiron").find("td").each(function(){
				$(this).find(".goallbkg").val(wunit);
				$(this).find(".goaldate").val("");
				$(this).find(".goalweight").val("");
				$(this).find(".goalreps").val("");
				$(this).find("input[type='checkbox']").attr('checked',false);
			});		
		}
		else {
			$("#othergoals").slideToggle();
			$("#goalweight0").val("");
			$("#goallbkg0").val(wunit);
			$("#goaldate0").val("");
		}
		$("input[name='goal less']").attr('checked',false);
		$("input[name='goal iron']").attr('checked',false);
	});
	$("input[name='goal less']").live("click",function(){
		$("#loseweight").slideToggle();
		if ($("input[name='goal massive']").attr("checked")==true || $("input[name='goal iron']").attr('checked')==true){
			$("#gainweight").hide();
			$("#pumpiron").hide();
			$("#goalweight0").val("");
			$("#goallbkg0").val(wunit);
			$("#goaldate0").val("");
			$("#pumpiron").find("td").each(function(){
				$(this).find(".goallbkg").val(wunit);
				$(this).find(".goaldate").val("");
				$(this).find(".goalweight").val("");
				$(this).find(".goalreps").val("");
				$(this).find("input[type='checkbox']").attr('checked',false);
			});
		}
		else {
			$("#othergoals").slideToggle();
			$("#goalweight1").val("");
			$("#goallbkg1").val(wunit);
			$("#goaldate1").val("");
		}
		$("input[name='goal massive']").attr('checked',false);
		$("input[name='goal iron']").attr('checked',false);
	});
	$("input[name='goal iron']").live("click",function(){
		$("#pumpiron").slideToggle();
		if ($("input[name='goal less']").attr("checked")==true || $("input[name='goal massive']").attr('checked')==true){
			$("#loseweight").hide();
			$("#gainweight").hide();
			$("#goalweight0").val("");
			$("#goallbkg0").val(wunit);
			$("#goaldate0").val("");
			$("#goalweight1").val("");
			$("#goallbkg1").val(wunit);
			$("#goaldate1").val("");
		}
		else {
			$("#othergoals").slideToggle();
			$("#pumpiron").find("td").each(function(){
				$(this).find(".goallbkg").val(wunit);
				$(this).find(".goaldate").val("");
				$(this).find(".goalweight").val("");
				$(this).find(".goalreps").val("");
				$(this).find("input[type='checkbox']").attr('checked',false);
			});
		}
		$("input[name='goal less']").attr('checked',false);
		$("input[name='goal massive']").attr('checked',false);
	});
	for (var i=0; i<3; i++){
		$("#subtype"+i).live("click",function(){
			thiscache0 = $(this).parent().find(".goalweight");
			thiscache1 = $(this).parent().find(".goallbkg");
			thiscache2 = $(this).parent().find(".goaldate");
			thiscache3 = $(this).parent().find(".goalreps");
			if ($(this).attr('checked')==false){
				thiscache0.val("");
				thiscache1.val(wunit);
				thiscache2.val("");
				thiscache3.val("");
			}
		});
	}
	$(".goalweight").live("keydown",function(){
		$(this).parent().find("input[type='checkbox']").attr('checked',true);
	});
	$(".goallbkg").live("click",function(){
		$(this).parent().find("input[type='checkbox']").attr('checked',true);
	});
	$(".goaldate").live("keydown",function(){
		$(this).parent().find("input[type='checkbox']").attr('checked',true);
	});
	$(".goalreps").live("keydown",function(){
		$(this).parent().find("input[type='checkbox']").attr('checked',true);
	});
	$(".goallbkg").val(wunit);
	if ($("#existinggoals").find("tr").length==1){
		$("#existinggoals").hide();
	}
// END GOALS!!!!!!!!!!!!!!!!!1
//START SPORTS!!!!!!!!!!1
$("#sportsinput").autocomplete({
           		data:search           		
       		});
$("#addsportsbutton").live("click",function(){
	var found=false;
	for(i=0;i<search.length;i++){
		if($("#sportsinput").val()==search[i][0]){//short cut the search function!!!!!!!!!!!!!!!!!!!!!!!
			var sportid = search[i][1];
			found=true;
		$.ajax({
	    type: "POST",
	    url: "db-interaction/users.php",
	    data: "action=addsport&sportid="+sportid+
	    "&sportname="+$("#sportsinput").val(),		
			success:function(){
			$("#existingsports").append("<li sports='"+sportid+"'>"+$("#sportsinput").val()+" <button type='button' class='sportremove small red box'>Remove Sport</button></li>");
			$("li:contains('"+$("#sportsinput").val()+"')").find(".sportremove").click(function(){
				var thiscache = $(this).parent();
				$.ajax({
			type: "POST",
			url: "db-interaction/users.php",
			data: "action=deletesport&sportattr="+thiscache.attr('sports'),
			success:function(){thiscache.remove();},
			error:function(){}
		});
			});
			$("#sportsinput").val("");
			},
			error:function(){}
			});				
		}
	}
	if (found==false){
		var sportid="";
		$.ajax({
	    type: "POST",
	    url: "db-interaction/users.php",
	    data: "action=addsport&sportid="+sportid+
	    "&sportname="+$("#sportsinput").val(),		
			success:function(){
			$("#existingsports").append("<li sports='"+$("#sportsinput").val()+"'>"+$("#sportsinput").val()+" <button type='button' class='sportremove'>Remove Sport</button></li>");
			$("li:contains('"+$("#sportsinput").val()+"')").find(".sportremove").click(function(){
				var thiscache = $(this).parent();
				$.ajax({
			type: "POST",
			url: "db-interaction/users.php",
			data: "action=deletesport&sportattr="+thiscache.attr('sports'),
			success:function(){thiscache.remove();},
			error:function(){}
		});
			});
			$("#sportsinput").val("");
			},
			error:function(){}
		});
	}
});
$(".sportremove").live("click", function(){
	thiscache = $(this).parent();
	$.ajax({
			type: "POST",
			url: "db-interaction/users.php",
			data: "action=deletesport&sportattr="+thiscache.attr('sports'),
			success:function(){thiscache.remove();},
			error:function(){}
		});
});
//END SPORTS!!!!!!!!!!!!!111
    $("#gender").val(sex);
    $("#plbkg").val(wunit);
	var $whitelist = '<b><i><strong><em><a>';
	if($("#pweight").val()==0){
		$("#pweight").val("");
	}
	if ($("#heightf").val()==0){
		$("#heightf").val("");
	}
	if ($("#heighti").val()==0){
		$("#heighti").val("");
	}
	if ($("#height").val()==0){
		$("#height").val("");
	}
	if ($("#phone").val()==0){
		$("#phone").val("");
	}
function sendToNewsDB (content,newstype)
{
	 $.ajax({
       type: "POST",
       url: "db-interaction/users.php",
       data: "action=newsupdate&content="+content+
       "&newstype="+newstype,
       success: function(){
        //window.location.replace("profile.php?user="+r);
                 // bindAllTabs(".list li[rel='"+newListItemRel+"'] span");
                 // $("#add-new-submit").removeAttr("disabled");
       },
       error: function(){
           // should be some error functionality here
       }
      });
}
$("#privacy1").change(function(){
	if($(this).val()==2) {
	$("#tprivacy1").val(1);}
	else{
		$("#tprivacy1").val(0);
	}
});
$("#privacy2").change(function(){
	if($(this).val()==2) {
	$("#tprivacy2").val(1);}
	else{
		$("#tprivacy2").val(0);
	}
});
$("#privacy3").change(function(){
	if($(this).val()==2) {
	$("#tprivacy5").val(1);}
	else{
		$("#tprivacy5").val(0);
}
});
$("#privacy4").change(function(){
	if($(this).val()==2) {
	$("#tprivacy5").val(1);}
	else{
		$("#tprivacy5").val(0);
}
});
$("#privacy5").change(function(){
	if($(this).val()==2) {
	$("#tprivacy5").val(1);}
	else{
		$("#tprivacy5").val(0);
}
});
 $('#editpsave').live("click",function(){
 	if ($("#YOfB").val()==thisyear&&($("#MOfB").val()-1)==d.getMonth()&&$("#DDOfB").val()>d.getDate()){
 		$("#funnyerror").text("You are not terminator (P.S. your birthday is in the future)").show();
		t = setTimeout(function(){$("#funnyerror").fadeOut(1000)},3000);
		return false;
 	}
 	else if($("#YOfB").val()==thisyear&&($("#MOfB").val()-1)>d.getMonth()){
 		$("#funnyerror").text("You are not terminator (P.S. your birthday is in the future)").show();
		t = setTimeout(function(){$("#funnyerror").fadeOut(1000)},3000);
		return false;
 	}
 	//Test for phone number
 	text = strip_tags(cleanHREF($("#phone").val()), $whitelist);
 	if(text!=""){
 	if (parseInt(text)!=parseFloat(text)||isNaN(text)||parseInt(text)<=0){
				alert("Invalid phone number");
				return false;
		}
	}
 var genderVal = $("#gender").val(),
    birthMonth = $("#MOfB").val(),
    birthDay = $("#DDOfB").val(),
    birthYear = $("#YOfB").val(),
    lbkg = $("#plbkg").val(),
    fweight = strip_tags(cleanHREF($("#pweight").val()), $whitelist),
    fheight = strip_tags(cleanHREF($("#height").val()), $whitelist),
    fsports = strip_tags(cleanHREF($("#sports").val()), $whitelist),
    fphone = strip_tags(cleanHREF($("#phone").val()), $whitelist),
    femail = strip_tags(cleanHREF($("#email").val()), $whitelist),
    flocation = strip_tags(cleanHREF($("#location").val()), $whitelist),
    fheightf = strip_tags(cleanHREF($("#heightf").val()), $whitelist),
    fheighti =  strip_tags(cleanHREF($("#heighti").val()), $whitelist);
 if (isNaN(parseInt(fheighti))==true)
 {
  fheighti = 0;
 }
  var    
    heighti = escape(parseInt(fheightf)*12 + parseInt(fheighti)),
    weight = escape(fweight),
    height = escape(fheight),
    sports = escape(fsports),
    phone = escape(fphone),
    email = escape(femail),
    location = escape(flocation);
	for (var i=0; i<9; i++){
		thiscache2 = $("#m"+i);
		if($("#m"+i).val()==""){
			thiscache2.val(0);
		}
	}
       // HTML tag whitelist. All other tags are stripped.
    //INSERT REMOVE ATTR DISABLE BUTTON BEFORE SAVE IS DONE
        
    // prevent multiple submissions by disabling button until save is successful
    // $("#add-new-submit").attr("disabled", true); //CHANGE THESE STUFF
	if(userweight!=$("#pweight").val()){
		today = new Date();
  if (today.getMonth()+1<10){
      month = "0"+eval(today.getMonth()+1);
     } else {
      month = today.getMonth()+1;
     }
        today = month+"/"+today.getDate()+"/"+today.getFullYear();
		$.ajax({
       type: "POST",
       url: "db-interaction/users.php",
       data: "action=weightcheck&weight="+$("#pweight").val()+
       "&lbkg="+$("#plbkg").val()+
       "&date="+today,
       success: function(r){},
       error: function(){}
   });
	}
	$("#existinggoals").find("input").each(function(){		 
		idshow = $(this).parent().parent().attr('id');
		if ($(this).attr('checked')==true){
			$.ajax({
			type: "POST",
			url: "db-interaction/users.php",
			data: "action=goaldisplay&goalid="+idshow+
			"&goalshown="+0,
			success: function(){},
			error: function(){}
		});
	}
		else{
			$.ajax({
			type: "POST",
			url: "db-interaction/users.php",
			data: "action=goaldisplay&goalid="+idshow+
			"&goalshown="+1,
			success: function(){},
			error: function(){}
		});
	}
	});
	$("#newgoaltable").find("input").each(function(){		 
		idshow = $(this).parent().parent().attr('id');
		if ($(this).attr('checked')==true){
			$.ajax({
			type: "POST",
			url: "db-interaction/users.php",
			data: "action=goaldisplay&goalid="+idshow+
			"&goalshown="+0,
			success: function(){},
			error: function(){}
		});
	}
		else{
			$.ajax({
			type: "POST",
			url: "db-interaction/users.php",
			data: "action=goaldisplay&goalid="+idshow+
			"&goalshown="+1,
			success: function(){},
			error: function(){}
		});
	}
	});
$.ajax({
       type: "POST",
       url: "db-interaction/users.php",
       data: "action=editProfile&gender="+genderVal+
       "&birthmonth="+birthMonth+
       "&birthday="+birthDay+
       "&birthyear="+birthYear+
       "&weight="+$("#pweight").val()+
       "&lbkg="+lbkg+
       "&height="+height+
       "&heighti="+heighti+
       "&sports="+sports+
       "&phone="+phone+
       "&email="+email+
       "&location="+location+
       "&privacy1="+$("#privacy0").val()+
       "&privacy2="+$("#privacy1").val()+
       "&privacy3="+$("#privacy2").val()+
       "&privacy4="+$("#privacy3").val()+
       "&privacy5="+$("#privacy4").val()+
       "&privacy6="+$("#privacy5").val()+
       "&privacy7="+$("#privacy6").val()+
       "&privacy8="+$("#privacy7").val()+
       "&privacy9="+$("#privacy8").val()+
       "&privacy10="+$("#privacy9").val()+
       "&privacy11="+$("#privacy10").val()+
       "&privacy12="+$("#privacy11").val()+
       "&privacy13="+$("#privacy12").val()+
       "&privacy14="+$("#privacy13").val()+
       "&privacy15="+$("#privacy14").val()+
       "&privacy16="+$("#privacy15").val()+
       "&tprivacy0="+$("#tprivacy0").val()+
       "&tprivacy1="+$("#tprivacy1").val()+
       "&tprivacy2="+$("#tprivacy2").val()+
       "&tprivacy3="+$("#tprivacy3").val()+
       "&tprivacy4="+$("#tprivacy4").val()+
       "&tprivacy5="+$("#tprivacy5").val()+
       "&tprivacy6="+$("#tprivacy6").val()+
       "&setting0="+$("#setting0").val()+
       "&m0="+$("#m0").val()+
       "&m1="+$("#m1").val()+
       "&m2="+$("#m2").val()+
       "&m3="+$("#m3").val()+
       "&m4="+$("#m4").val()+
       "&m5="+$("#m5").val()+
       "&m6="+$("#m6").val()+
       "&m7="+$("#m7").val()+
       "&m8="+$("#m8").val()+
       "&u0="+$("#inchorcm0").val()+
       "&u1="+$("#inchorcm1").val()+
       "&u2="+$("#inchorcm2").val()+
       "&u3="+$("#inchorcm3").val()+
       "&u4="+$("#inchorcm4").val()+
       "&u5="+$("#inchorcm5").val()+
       "&u6="+$("#inchorcm6").val()+
       "&u7="+$("#inchorcm7").val()+
       "&u8="+$("#inchorcm8").val(),
       success: function(r){
        window.location.replace("profile.php?user="+r);
                 // bindAllTabs(".list li[rel='"+newListItemRel+"'] span");
                 // $("#add-new-submit").removeAttr("disabled");
       },
       error: function(){
           // should be some error functionality here
       }
      });
if (flocation!=previousLocation && flocation!="" && $("#tprivacy5").val()==0){
	sendToNewsDB (location,9);
}
if (femail!=previousEmail && femail!="" && $("#tprivacy5").val()==0){
	sendToNewsDB (email,10);
}
if (fphone!=previousPhone && fphone!="" && $("#tprivacy5").val()==0){
	sendToNewsDB (phone,11);
}
if (fheight!=previousHeight && fheight!="" && $("#tprivacy2").val()==0){
	sendToNewsDB (height,12)
}
if (sex!=genderVal){
	sendToNewsDB (genderVal,15)
}
});
var newObj = new Object()
function convertMetersToFI()
{
 var height = parseInt($("#height").val());
 if (isNaN(height)==true)
 {
  $("#heightf").val(0);
  $("#heighti").val(0);
  $("#height").val(0);
 }
 else
 {
  var mToI = height*0.393700787,
  onlyInches = mToI.toFixed();
  $("#heightf").val((onlyInches - onlyInches%12)/12);
  $("#heighti").val(onlyInches%12);
 }
}
function convertFtIToM()
{
 var fheightf = parseInt($("#heightf").val()),
 fheighti = parseInt($("#heighti").val());
 
 if (isNaN(fheighti)==true && isNaN(fheightf)==false) // only entering in the feet field with inch field empty, or (naughty) deleting whats in the inch field with feet field non-empty
 {
  var rheighti = 0,
  rheightf = fheightf,
  heighti = rheightf*12 + rheighti,
  iToM = heighti*2.54;
  $("#height").val(iToM.toFixed());
  $("#heighti").val(0);
 }
 else if (isNaN(fheighti)==false && isNaN(fheightf)==true) // only entering in the inch field with feet field empty, or (naughty) deleteing the feet field with inch fiend non-empty
 {
  var fheightf = 0,
  rheighti = fheighti;
  if (fheighti<12)
  {
   var rheightf = fheightf,
   heighti = rheightf*12 + rheighti,
   iToM = heighti*2.54;
   $("#height").val(iToM.toFixed());
   $("#heightf").val(0);
  }
  else //naughty (entering inch higher than 12)
  {
   var rheightf = ((fheighti-fheighti%12)/12)+fheightf,
   rheighti = fheighti%12,
   heighti = rheightf*12 + rheighti,
   iToM = heighti*2.54;
   $("#height").val(iToM.toFixed());
   $("#heightf").val(rheightf);
   $("#heighti").val(rheighti);
  }
 }
 else if (isNaN(fheighti)==false && isNaN(fheightf)==false) //entering in either inch or feet with the other one already filled.
 {
  if (fheighti<12)
  {
   var rheighti = fheighti,
   rheightf = fheightf,
   heighti = rheightf*12 + rheighti,
   iToM = heighti*2.54;
   $("#height").val(iToM.toFixed());
  }
  else //naughty
  {
   var rheightf = ((fheighti-fheighti%12)/12)+fheightf,
   rheighti = fheighti%12,
   heighti = rheightf*12+rheighti,
   iToM = heighti*2.54;
   $("#height").val(iToM.toFixed());
   $("#heightf").val(rheightf);
   $("#heighti").val(rheighti);   
  }
 }
 else //(very naughty) deleting either inch or feet with the other one empty
 {
  $("#height").val(0);
  $("#heighti").val(0);
  $("#heightf").val(0);
 }
}
function numbersOnly(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
        {
            return false;
		}
		else
		{
        return true;
     	}
}
function numbersOneDecimalPointWeight(evt)
{
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if ($("#weight").val().indexOf(".")!=-1)
	{
		if (charCode > 31 && (charCode < 48 || charCode > 57))
        {
        	return false;
		}
		else
		{
        	return true;
     	}
	}
	else
	{
		if ((charCode >= 48 && charCode <=57) || charCode == 46 || charCode <=31)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
newObj.convertMetersToFI = convertMetersToFI,
newObj.numbersOnly = numbersOnly,
newObj.numbersOneDecimalPointWeight = numbersOneDecimalPointWeight,
newObj.convertFtIToM = convertFtIToM;
return newObj;
function getlbkg(lbkg){
	if(lbkg==1){
		return "kg";
	}
	else {
		return "lbs";
	}
}
function weightchecker(oldweight,gweight,oldunit,gunit){
	if(oldunit==1){
		oldweight = oldweight*2.20462262;
	}
	if(gunit==1){
		gweight = gweight*2.20462262;
	}
	return (gweight-oldweight);
}
function testdateGoal(date){
	var dateArray = date.split("/");
	var testDate = new Date(dateArray[2], dateArray[0]-1, dateArray[1]);
    if (testDate.getDate()!=dateArray[1] || testDate.getMonth()!=dateArray[0]-1 || testDate.getFullYear()!=dateArray[2]) {
    	return 0;
    }
    else {
    	today = new Date();
    	if (testDate.getTime()<today.getTime()){
    		return 2;
   		}
    else{
    	return true;
    	}
	}
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
}
function profile(chest, forearm, waist, thigh, hip, calve, bicep, neck, shoulder, unit, fheighti, fprimaryWeight, lbkg, height, gender, birthday)
{	var splitDOB = birthday.split("-");
    	today = new Date();
	if (birthday==""){
		$("#DOfB").replaceWith("<a href='editprofile.php' class='toEditProfile'>Edit your profile to enter a birthday</a>");
		$("#privacya").remove();
		$("#age").text("");
	}
	$("#gender").val(gender);
	$("#goalsbmi").hide();
	var measurements = new Array(chest, forearm, waist, thigh, hip, calve, bicep, neck, shoulder, unit);
	//var mUnitArray = new Array
	mUnitArray = baseTwoConvert(unit,9);
	for (var i=0; i<9; i++){
		thiscache = $("#m"+i);
		primaryMeasurement = measurements[i];
		if(mUnitArray[i]==0){
			var secondaryMeasurement = primaryMeasurement*2.54;
			thiscache.text(parseFloat(primaryMeasurement).toFixed(1)+" in / "+secondaryMeasurement.toFixed(1)+" cm");
		}
		else if(mUnitArray[i]==1){
			var secondaryMeasurement = primaryMeasurement*0.393700787;
			thiscache.text(secondaryMeasurement.toFixed(1)+" in / "+parseFloat(primaryMeasurement).toFixed(1)+" cm");
		}
		if(measurements[i]==0){
			thiscache.prev().remove();
			thiscache.remove();
		}
	}
	
	if ($("#goals").find("tr").length==1){
		$("#goals").remove();
	}
	if($("#bodymeasurements").find("td").length==0){
		$("#bodymeasurements").prev().remove();
		$("#bodymeasurements").remove();
	}
	$(".hoverheading:contains('User Information')").live("click",function(){
		$("#userinformation").show();
		$("#goalsbmi").hide();
		$(".hoverheading:contains('Goals and Body Measurements')").removeClass("hovered");
		$(this).addClass("hovered");
	});
	$(".hoverheading:contains('Goals and Body Measurements')").live("click",function(){
		$("#userinformation").hide();
		$("#goalsbmi").show();
		$(this).addClass("hovered");
		$(".hoverheading:contains('User Information')").removeClass("hovered");
	});
	if (fheighti!=""&&fheighti!=0){
	var heighti = parseInt(fheighti),
	heightf = (heighti - heighti%12)/12,
	onlyi = heighti%12;
	$(".heightrow").each(function(){
		$(this).append(heightf+" ft "+onlyi+" in");
	});}
	else{
		$(".heightrow").each(function(){
			$(this).html("<a href='editprofile.php' class='toEditProfile'>Edit your profile to enter height</a>");
		});
	}
	if (fprimaryWeight!=""&&fprimaryWeight!=0){
	var primaryWeight = parseFloat(fprimaryWeight);
	$(".weightrow").each(function(){
		if (lbkg==0) //user chose lbs as primary weight
		{//calculate kg
			var secondaryWeight = primaryWeight*0.45359237;
			$(this).append(primaryWeight+" lbs ("+secondaryWeight.toFixed(1)+" kg)");
		}
		else //user chose kg as primary weight
		{//calculate lbs
			var secondaryWeight = primaryWeight*2.20462262;
			$(this).append(primaryWeight+" kg ("+secondaryWeight.toFixed(1)+" lbs)");				
		}
	});}
	else{
		$(".weightrow").each(function(){
			$(this).html("<a href='editprofile.php' class='toEditProfile'>Edit your profile to enter weight</a>");
		});
	}
//!!!!START BMI CALCULATOR!!!!//
$("#calc").live("click",function(){
	var weight = $("#weightbmi").val();
	if ($("#bmilbkg").val()==0){
		weight = weight*0.45359237
	}
	var bmi = weight/Math.pow(($("#heightbmi").val())/100,2);
	$("#bmibox").val(bmi.toFixed(2));
	
});
$("#loadstats").live("click",function(){
	$("#weightbmi").val(fprimaryWeight);
	$("#bmilbkg").val(lbkg);
	$("#heightbmi").val(height);
});
pct = new Array(2)
for (i = 0; i < 2; i++) {
   pct[i] = new Array(19)
}

pct[0][0] = new Array(14.6,19.9,17.2,4.5974644,-128.5099,900.63047,0,-2.01467,91.603202,-929.6572,0,0,0)
pct[0][1] = new Array(14.4,19,16.5,8.0084129,-225.8683,1596.6707,0,-3.840346,154.60113,-1455.577,0,0,0)
pct[0][2] = new Array(14,18.4,16,10.144836,-281.8513,1962.5378,0,-4.862358,186.22083,-1684.927,0,0,0)
pct[0][3] = new Array(13.8,18.1,15.8,8.4018375,-225.8422,1521.1476,0,-7.345051,268.36966,-2356.446,0,0,0)
pct[0][4] = new Array(13.7,18, 15.5,12.899116,-351.9558,2406.0664,0,-7.016402,252.73142,-2181.287,0,0,0)
pct[0][5] = new Array(13.6,18.1,15.4,9.5231433,-251.2705,1661.0014,0,-5.6235, 204.77956,-1769.631,0,0,0)
pct[0][6] = new Array(13.6,18.9,15.5,7.0078568,-180.1728,1159.0779,16.5,-5.681818,206.81818,-1790.625,-3.472222,131.25, -1145.313)
pct[0][7] = new Array(13.7,19.7,15.7,7.0665829,-185.3957,1218.7955,0,-2.849003,112.04624,-1006.758,0,0,0)
pct[0][8] = new Array(14,20.9,16, 4.666799,-117.6612,737.82339,0,-1.893939,79.031345,-729.5582,0,0,0)
pct[0][9] = new Array(14.2,22.2,16.6,3.0675692,-75.72176,461.6862,0,-1.498702,66.155292,-635.1081,0,0,0)
pct[0][10] = new Array(14.6,23.5,17.2,2.4852546,-61.77326,377.23969,19.2,-1.30662,60.060976,-596.4983,-1.132588,53.012685,-525.3262)
pct[0][11] = new Array(15.1,24.8,17.8,1.8298339,-43.53786,245.20803,20, -1.075977,52.035573,-535.3206,-0.942029,46.369565,-475.5797)
pct[0][12] = new Array(15.6,25.8,18.4,2.0573725,-53.97538,346.55949,0,-0.849432,43.608961,-464.7563,0,0,0)
pct[0][13] = new Array(16.1,26.8,19.1,1.8117346,-48.73513,319.92319,0,-0.786164,41.845615,-462.1297,0,0,0)
pct[0][14] = new Array(16.6,27.7,19.7,2.5054069,-76.59692,586.52726,22.2,-0.932018,49.051535,-554.6086,-0.457016,26.441453,-286.7646)
pct[0][15] = new Array(17.2,28.4,20.5,2.178566,-68.54933,539.67771,22.9,-1.023065,54.817708,-643.8198,-0.457016,27.081275,-305.4975)
pct[0][16] = new Array(17.7,29, 21.2,1.932012,-62.26754,501.77516,23.4,-1.240857,66.705852,-806.4734,-0.297619,19.166667,-210.5357)
pct[0][17] = new Array(18.3,29.7,21.9,1.7205506,-56.51408,462.60923,24, -1.35357,74.03364,-922.1508,-0.272641,18.149597,-203.5491)
pct[0][18] = new Array(19,30.1,22.5,2.3074556,-82.98123,748.85294,24.4,-1.605473,88.454558,-1127.457,-0.187434,13.723947,-148.2734)

pct[1][0] = new Array(14.7,19.3,16.6,6.5745111,-182.3837,1265.7856,0,-4.852769,190.85499,-1780.938,0,0,0)
pct[1][1] = new Array(14.3,18.7,16, 9.6601537,-266.0637,1834.1067,0,-4.102342,159.18945,-1446.988,0,0,0)
pct[1][2] = new Array(13.9,18.3,15.6,10.929732,-295.4886,2000.0595,0,-4.102342,155.90758,-1383.969,0,0,0)
pct[1][3] = new Array(13.6,18.2,15.4,10.400031,-276.3876,1840.0429,0,-4.02173,151.26489,-1325.746,0,0,0)
pct[1][4] = new Array(13.5,18.3,15.3,10.400031,-274.3076,1812.5082,0,-4.588485,168.95286,-1460.582,0,0,0)
pct[1][5] = new Array(13.3,18.8,15.3,10.144836,-267.6486,1770.2128,0,-3.787879,141.86394,-1233.534,0,0,0)
pct[1][6] = new Array(13.4,19.7,15.5,8.0084129,-209.8514,1378.8109,16.7,-4.166667,155, -1351.458,-1.388889,57.222222,-493.2639)
pct[1][7] = new Array(13.6,21, 16, 4.8623579,-124.9701,804.91472,17.2,-4.122103,157.68717,-1417.736,-0.97189,42.389354,-366.573)
pct[1][8] = new Array(14,22.7,16.6,3.5880286,-92.50822,596.90511,18, -2.97619,120.83333,-1135.714,-0.579907,27.857543,-238.5458)
pct[1][9] = new Array(14.3,24.2,17.1,3.3517478,-88.94666,591.09876,19, -1.659734,73.074308,-714.2477,-0.629579,31.043956,-287.5572)
pct[1][10] = new Array(14.6,25.7,17.8,1.3605238,-29.68961,147.63683,19.8,-1.502404,68.990385,-702.0072,-0.480618,25.257965,-236.6861)
pct[1][11] = new Array(15,26.8,18.3,1.2853726,-29.01195,150.55772,20.4,-1.362835,64.646465,-676.6306,-0.458211,24.752566,-239.2632)
pct[1][12] = new Array(15.4,27.9,18.9,0.721787,-11.71291,13.651678,21.2,-1.135147,56.388948,-610.2654,-0.40645,22.941764,-228.6906)
pct[1][13] = new Array(15.7,28.6,19.4,0.7268851,-13.11623,31.041935,21.8,-1.039144,53.229419,-591.5583,-0.407436,23.475936,-243.1457)
pct[1][14] = new Array(16.1,29.4,19.9,0.8137127,-17.27052,71.571639,22.4,-0.956284,50.45082,-575.2732,-0.385154,22.808123,-242.6471)
pct[1][15] = new Array(16.4,30, 20.2,1.0986916,-28.25778,172.58255,22.8,-0.882751,47.573673,-550.7905,-0.36465,22.031317,-237.7542)
pct[1][16] = new Array(16.9,30.5,20.7,1.0986916,-29.35647,186.98611,23.3,-0.885628,48.582996,-576.1855,-0.343997,21.28483,-234.1839)
pct[1][17] = new Array(17.2,31, 21.1,1.4948127,-45.64661,347.70261,23.7,-0.882751,49.162624,-594.3218,-0.365091,22.71021,-258.164)
pct[1][18] = new Array(17.5,31.3,21.4,1.835396,-59.80232,489.29761,24, -0.882751,49.692275,-609.1501,-0.365091,22.929265,-265.0099)

function percentile(g,a,b) {

	if(a==""){
		$("#percentileresult").text("Age can't be empty");
	}else if(parseInt(a-1)>19){
		$("#percentileresult").text("Sorry we can only do percentiles up to age of 19");
	}else{
		var p = pct[g][parseInt(a - 1)];
		
   if (b < p[0]) {
      result = "<  5th";
   } else if (b > p[1]) {
      result = "> 95th";
   } else if (b < p[2]) {
      result = ((p[3]*b*b)+(p[4]*b)+p[5]);
   } else if ((p[6] == 0) || (b < p[6])) {
      result = ((p[7]*b*b)+(p[8]*b)+p[9]);
   } else {
      result = ((p[10]*b*b)+(p[11]*b)+p[12]);
   }
   $("#percentileresult").text("Your BMI Percentile is "+result);
   }
}
$("#percentile").live("click",function(){
	percentile($("#gender").val(),$("#agebmi").val(),$("#bmibox").val());
});
$("#loadage").live("click",function(){
	$("#agebmi").val(parseInt($("#age").text()));
});
}
function baseTenConvert(n,r) //For privacy options
{
		var converted = new Array();
		for (var i=0; i<r; i++)
		{
			converted[i]=n%3
			n=(n-n%3)/3;
		}
	return converted
}
function baseTwoConvert(n, r) //For tracker options
{
	var converted = new Array();
	for (var i=0; i<r; i++)
	{
		converted[i]=n%2;
		n=(n-n%2)/2;
	}
	return converted
}
function getTrackerO(UID){
	$.ajax({
    			type: "POST",
    			url: "/db-interaction/users.php",
    			data: {
    				"action":"getTrackerO",
    				"UID":UID
				},
    				
    			success: function(r){
					converted = baseTwoConvert(r,7);
					initializeTrack (UID, converted[0]);	//0 index = auto accept trackers
					
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
}
function initializeTrack(trackee, TrackerO){

		$.ajax({
    			type: "POST",
    			url: "/db-interaction/users.php",
    			data: {
    				"action":"trackthisperson",
    				"trackee":trackee,
    				"TrackerO":TrackerO
				},
    				
    			success: function(){
    				if(TrackerO==1){
    					$("#trackthisperson").remove();		//If auto accepted
    					$("#profileinfo").prepend("<div class='mid box font14' id='stoptrack'>Tracking</div>");
    				} else {
					$("#trackthisperson").text("request pending");//remove onclick thing
					$("#trackthisperson").removeClass("lightgreen");
					$("#trackthisperson").addClass("grey");
				}
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
}