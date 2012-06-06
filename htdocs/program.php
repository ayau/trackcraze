<?php
	include_once "common/base.php"; 
    $pageTitle = "Program";//Change to the name of the person?
	include_once "common/header.php"; 
	echo "<div id='container'>";
	include_once 'inc/class.lists.inc.php';
	$program = new GymScheduleItems($db);
	include_once 'inc/class.news.inc.php';
	$news = new GSNews($db);
	if(isset($_GET['program'])):
	$UID = $program->loadUserID($_GET['program']);
	$PID = $_GET['program'];
	elseif (isset($_GET['user'])):
	$UID = $_GET['user'];
	$PID = $program->loadMainProgramByUser($UID);
	else:
	$UID = $_SESSION['UserID'];	
	$PID = $program->loadMainProgramByUser($UID);
	endif;
	if($UID==NULL):
	else:
	include_once "common/sidebar.php";
	include_once "common/rsidebar.php";
	endif;
	$OSID = $program->getDefaultProgram($UID);
	?>
<div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
            <script type="text/javascript" src="js/lists.js"></script>
<?php 
if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])&&($UID!=NULL)&& isset($PID))://PROBLEM HERE IS THAT USERID COULD BE NULL(VISITOR)AND ABLE TO VIEW PAGES THAT DON"T EXIST" MAYBE CHECK ISSET PROGRAM?
	if ($PID==0):
		if($_SESSION['UserID']==$UID ):
			echo "Create your first program!";
			?>
			<form action="db-interaction/lists.php" id="add-program" method="post"> 
				<input type='text' id='addprogramtextbox' style='width:300px' autocomplete='off'/>
				<div id='addprogrambuttons'>
					<input type='submit' id='addprogramsubmit' value='Add' />
				</div>
			<input type="hidden" id="new-list-item-position" name="new-list-item-position" value="<?php echo ++$order; ?>" />
			</form>
			<script>
			
		$('#add-program').submit(function(){
 		// HTML tag whitelist. All other tags are stripped.
    	var $whitelist = '<b><i><strong><em><a>',
    		forList = '<?php echo $cuser?>',//CHANGE ONCE PUT INTO JS OR WON"T WORK!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
    		newListItemText = strip_tags(cleanHREF($("#addprogramtextbox").val()), $whitelist),
    		URLtext = escape(newListItemText);
    		//INSERT REMOVE ATTR DISABLE BUTTON BEFORE SAVE IS DONE
			 if (newListItemText.length > 0) {
        
            // prevent multiple submissions by disabling button until save is successful
           $("#add-program").attr("disabled", true); //CHANGE THESE STUFF
        	
            $.ajax({//NEEEEEEEEDDD SPLIT ID. LOOK BELOW
    			type: "POST",
    			url: "db-interaction/lists.php",
    			data: "action=addProgram&list=" + forList
    				+ "&text=" + URLtext,
    			success: function(r){
    				$.ajax({//NEEEEEEEEDDD SPLIT ID. LOOK BELOW
    					type: "POST",
    					url: "db-interaction/lists.php",
    					data: "action=updatemainprogram&val=" + r,
    					success: function(){
    						window.location = "programedit.php?program="+r;
                			},
    					error: function(){
    			    // should be some error functionality here
    					}
    			});
                	},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
        } else {
        	$("#addprogramtextbox").val("");
        }
        return false; // prevent default form submission
 	})
           
         	</script>
			<?php
		else:
			echo "This person hasn't created any programs yet! Recommend some programs to him/her";
		endif;
	elseif($PID==-1):
		if($_SESSION['UserID']==$UID):
		echo "<a id='moreprograms' href=\"programlists.php?user=".$UID."\" class='fitwidth box noUnderline'>View More Programs</a>";
			echo "<br /><br /><br /><br /><br /><h3>Please choose a program as your main program in 'View More Programs'</div>";
		else:
		echo "<a id='moreprograms' href=\"programlists.php?user=".$UID."\" class='fitwidth box noUnderline'>View More Programs</a>";
			echo "<br /><br /><br /><br /><br /><h3>This person did not set a program as his main program<h3>";//just forward to programlist
		endif;
	else:
	$users = new GymScheduleUsers($db);
	$relationship = $users->trackingCheck($_SESSION['UserID'],$UID);
	$privacy = $program->getprogramprivacy($PID);
	echo "<a id='moreprograms' href=\"programlists.php?user=".$UID."\" class='fitwidth box noUnderline'>View More Programs</a>";
	$bool=false;
	if ($_SESSION['UserID']==$UID && ($OSID!=$PID)):	//Prevent editing Default program
		echo "<a id='editview' href=\"programedit.php?program=".$PID."\" class='orange fitwidth box noUnderline'>Edit Program</a>";
		$bool=true;
	else:
		echo "<p style='color:#666'>The Default program contains all the random, uncategorized exercises that you have done.</p><p style='color:#666'>You cannot edit this. Create a new program instead!</p>";
	endif;
if ($_SESSION['UserID']==$UID||$privacy==0||($privacy==1&&$relationship==2)):
list($LID, $URL) = $program->loadProgramByProgramID($PID);
echo "<div id='programcomment'><div id=".$PID." class='postcontent'>";
echo $news->checkkudos($PID,1,$UID);
echo "<div class='kudos'>";
$news->getkudos($PID,1);
echo "</div>";
echo "<div class='commentbox' >Comment<textarea class='addpostcomment' placeholder='Remember, be nice!' cols='80' rows='1' autocomplete='off' ></textarea><input id='commentsubmit' type='button' style='margin-top:2px' class='small box' value='Post it!'/> <input id='commentcancel' type='button' style='margin-top:2px' class='grey small box' value='Cancel'/></div><div class='comments'>";
echo $news->loadprogramcomments($PID,1,$bool);
echo "</div></div></div>";    
else:
echo "You do not have permission to view this page. Unpopular much?";
endif;
              
?>			
			
			
<br /><br />
 
            <div id="share-area">
                <p>Share your workout program with your friends! <a href="http://www.trackcraze.com<?php echo $_SERVER['REQUEST_URI']?>">www.trackcraze.com<?php echo $_SERVER['REQUEST_URI']?></a></p>
            </div>
 
  <span></span>
            <script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
            <script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
            <script type="text/javascript" src="js/autogrow.js"></script>
            <script type="text/javascript" src="js/charcounter.js"></script>
            <script type="text/javascript">
       			initializeProgram();
            </script>
<?php
/*elseif(isset($_GET['list'])):                 
    echo "ttt<ul id='list'>n";
 
    include_once '/inc/class.lists.inc.php';
    $lists = new GymScheduleItems($db);
    list($LID, $URL) = $lists->loadListItemsByProgramID();
 
    echo "\t\t\t</ul>";
    !!!!!!!!!!!!!!!!!!!!!!!!!IF USER IS NULL MAKE IT GO TO THEIR MAIN PROGRAM!!!!!!!!!!!*/
endif;
elseif($UID==NULL||$PID==NULL):                 
    echo "This program does not exist!";
else:
?>
                     This error should not come up.. wtf did you do?
            <!--<img src="/assets/images/newlist.jpg" alt="Your new list here!" />-->
                   
<?php endif; ?>
                                       
        </div>
		

<?php 
include_once "common/footer.php"; ?>