<?php
    include_once "common/base.php";
        $pageTitle = "Programs";
        include_once "common/header.php";
        echo "<div id='container'>";
		if(isset($_GET['user'])):
			$UID = $_GET['user'];
		else:
			$UID = $_SESSION['UserID'];
		endif;
		include_once "common/sidebar.php";
		include_once "common/rsidebar.php";?>
        <div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
<?php
	if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']==1):
		if($_SESSION['UserID']==$UID):
		echo "<h2>My Programs</h2>";
		else:
		echo "<h2>".$Forename1." ".$Surname1."'s Programs</h2>";
		endif;
		echo "\t\t\t<table id=\"programlist\">\n";
        include_once 'inc/class.lists.inc.php';
   		$lists = new GymScheduleItems($db);
   		list($order) = $lists->loadProgramsByUser($UID);
   		echo "\t\t\t</table>";
	   	echo "<input type='button' class='programprivacy mid lightgreen box' id='updateprogram' value='Update'/><div id='updateconfirmation'></div>";
	 if ($_SESSION['UserID']!=$UID):
	 ?><script> $("input[name='mainprogram']").filter("[value="+<?php echo $lists->loadMainProgramByUser($UID)?> +"]").removeAttr("class"); $(".programprivacy").remove();</script><?php
	 endif;
?>
<br /><br />

            <form action="db-interaction/lists.php" id="add-program" method="post"> <!--DOESN"TWORK YET"-->
				<a id='addprogramtrigger' class='noUnderline' ><div class='fitwidth box font20'>Click to add a new Program</div></a>
				<input type='text' id='addprogramtextbox' placeholder='Enter the name of your Program' style='display:none' autocomplete='off'/>
				<div id='addprogrambuttons' hidden>
					<input type='submit' id='addprogramsubmit' class='lightgreen fitwidth box' value='Add' />
					<input type='button' id='addprogramcancel' class='grey fitwidth box' value='Cancel' />
				</div>
			<input type="hidden" id="new-list-item-position" name="new-list-item-position" value="<?php echo ++$order; ?>" />
			</form>
			<br />
			<br />
			<br />
	<?php
		echo "<h3>Your Exercises</h3><ul>";
	   	echo $lists->loadExercise($UID);
	   	echo "</ul>";
	   	?>

<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
            <script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
            <script type="text/javascript" src="js/lists.js"></script>
            <script type="text/javascript">      
             //initialize();                       CHANGE CUSER IF PUT INTO JS!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
             $("#addprogramtrigger").click(function(){
			$("#addprogramtextbox").animate({"height": "20px","width": "300px",}, "slow" );
			$("#addprogramtextbox").fadeIn("slow");
			$("#addprogramtextbox").focus();
			$("#addprogrambuttons").fadeIn("slow");
			$("#addprogramtrigger").fadeOut("slow");
			$(this).val('');
			return false;
		});
		$("#addprogramcancel").live("click",function(){
			$("#addprogramtextbox").val('');
			$("#addprogramtextbox").animate({"height": "20px","width": "160px",}, "slow" );
			$("#addprogrambuttons").fadeOut("slow");
			$("#addprogramtextbox").fadeOut("slow");
			$("#addprogramtrigger").fadeIn("slow");
			});
		$('#add-program').submit(function(){
 		// HTML tag whitelist. All other tags are stripped.
    	var $whitelist = '<b><i><strong><em><a>',
    		forList = '<?php echo $cuser?>',//CHANGE ONCE PUT INTO JS OR WON"T WORK!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
    		newListItemText = strip_tags(cleanHREF($("#addprogramtextbox").val()), $whitelist),
    		URLtext = escape(newListItemText),//escapes get the space as %20
    		newListItemRel = $("#programlist").find('tr').length/2+1;
    		//INSERT REMOVE ATTR DISABLE BUTTON BEFORE SAVE IS DONE
			 if (newListItemText.length > 0) {
        
            // prevent multiple submissions by disabling button until save is successful
           $("#add-program").attr("disabled", true); //CHANGE THESE STUFF
        	
            $.ajax({//NEEEEEEEEDDD SPLIT ID. LOOK BELOW
    			type: "POST",
    			url: "db-interaction/lists.php",
    			data: "action=addProgram&list=" + forList
    				+ "&text=" + URLtext
    				+ "&pos=" + newListItemRel,
    			success: function(r){
    			$("#programlist").append("<tr id=\""+r+"\" rel=\""+newListItemRel+"\" class=\"exerciseEdit\" name=\"exerciseList\"><td><Input type = 'Radio' class='programprivacy' name='mainprogram' value= '"+r+"'><td class=program>"+newListItemText+"</td><td class='toggle'><a class='small grey box noUnderline'>Splits</a></td><td><select class='programprivacy programprivacyselect'><option value='0'>Public</option><option value='1'>Trackers only</option><option value='2'>Private</option></select></td><td><a class='programview small box noUnderline' href='/program.php?program="+r+"'>View</a></td><td><a class =\"programedit programprivacy\" href='/programedit.php?program="+r+"'>Edit</a></td><td><div class='deletered sp programprivacy deleteprogram'></div></td><td class='tablesure'></td></tr><tr><td colspan='7'><div class='hidden' hidden></div></td></tr>");            
                  $("#addprogramcancel").click();
                 $("#add-program").removeAttr("disabled");
                	},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
        } else {
        	$("#addprogramcancel").click();
        }
        return false; // prevent default form submission
 	})
             $("input[name='mainprogram']").filter("[value="+<?php echo $lists->loadMainProgramByUser($UID)?> +"]").attr("checked",true);
             $(".programprivacyselect").each(function(){
             	var thiscache=$(this);
             	$.ajax({
    			type: "POST",
    			url: "db-interaction/lists.php",
    			data: "action=getprogramprivacy"
    				+ "&pid=" + $(this).parent().parent().attr('id'),
    			success: function(r){
				thiscache.val(r);
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
         	});
             $("#updateprogram").live("click",function(){
             $.ajax({
             		type: "POST",
             		url: "db-interaction/users.php",
             		data: "action=newsupdate&content="+$("input[name='mainprogram']:checked").val()+
             		"&newstype=5",
             		success: function(){
             		},
             		error: function(){
             		}
         	});
             	$.ajax({
    			type: "POST",
    			url: "db-interaction/lists.php",
    			data: "action=updatemainprogram"
    				+ "&val=" + $("input[name='mainprogram']:checked").val(),
    			success: function(){
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});

             	$(".programprivacyselect").each(function(){
            $.ajax({
    			type: "POST",
    			url: "/db-interaction/lists.php",
    			data: "action=updateprogramprivacy"
    				+ "&val=" + $(this).val()
    				+ "&pid=" + $(this).parent().parent().attr('id'),
    			success: function(){
    				$("#updateconfirmation").text("Updated!!").show();
            		t = setTimeout(function(){$("#updateconfirmation").fadeOut(1000)},3000);
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
             	});
             })
             $(".toggle").live("click",function(){
             	$(this).parent().next().find(".hidden").slideToggle();
             	$thiscache = $(this).find("a");
             	if($thiscache.text()=="Splits"){
             		$thiscache.text("Hide");
         		}else{
         			$thiscache.text("Splits");
             	}
         	});
         	function deleteprogram(thiscache){
             	list = thiscache.parent().parent().attr('id');
             	pos = thiscache.parent().parent().attr('rel');
             	$.ajax({
    			type: "POST",
    			url: "db-interaction/lists.php",
    			data: {
    					"list":list,
    					"pos":pos,
    					"action":"deleteprogram"
    				},
    			success: function(r){
    						var position = 0;
    					thiscache.parent().parent().next().remove();
    	            	thiscache
    	            		.parent().parent()
    	            			.hide("explode", 400, function(){$(this).remove()});
    	            	thiscache.parent().parent().parent()
            				.children('.exerciseEdit')
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
          $(".deleteprogram").live("click", function(){
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
			deleteprogram(thiscache);
		});
        	thiscache.parent().next().append("<p class='readydelete' hidden>are you sure?</p>");
        	$(".readydelete").slideToggle("fast");
        	thiscache.data("readyToDelete", "go for it");
    	}
    	});   
            </script>
            <script src="js/grid.locale-en.js" type="text/javascript"></script>
			<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>
</div>
<?php
    else:
        echo"You do not have permission to view this page. Unpopular much?";
    endif;
?>
<?php
    include_once "common/footer.php";
?>