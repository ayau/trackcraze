<?php
	include_once "common/base.php"; 
    $pageTitle = "trackCraze";//Change to the name of the person?
	include_once "common/header.php";
	include_once 'inc/class.news.inc.php';
	$news = new GSNews($db);
	include_once "inc/class.users.inc.php";
	$users = new GymScheduleUsers($db);
	include_once "common/rsidebar.php";

	if (isset($_GET['user'])):
		$cuser = $_GET['user'];
		$getuser ="?user=".$cuser;
	else:
		$cuser = $_SESSION['UserID'];
		$getuser="";
	endif;
	list($Surname1, $Forename1, $Gender1, $DOB1, $Weight1, $lbkg1, $Height1, $Heighti1, $Phone1, $Email1, $Location1, $Privacy1,$TO, $status, $Bmeasurements1, $Unit1, $Setting1) = $users->loadProfileByUser($cuser);
	if ($lbkg1 ==NULL):// LOLOLOLOLOLOLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL
	echo "This user does not exist.";
	die();
	endif;
	if ($users->verificationCheck($_SESSION['UserID'])==TRUE):
	echo "<br />Please check your email to verify your Trackcraze account before proceeding";
	die();
	endif;
	if($Gender1 == 0)
		$him = "him";
	else if($Gender1 == 1)
		$him = "her";
	else
		$him = "him/her";
	if(strlen($Forename1)>18)
		$Forename = substr($Forename1, 0, 18)."..";
	else
		$Forename = $Forename1;
	echo "<div id='container'>";
	echo "<div id='wrap'>";
	echo "<div id='profileCon' class='container'><a class='TitleConlink' href='profile.php".$getuser."'><div class='TitleCon hover h3'>";
	$name = $Forename1." ".$Surname1;
	if(strlen($name)>20){
		$name = substr($name, 0, 20)."..";
	}
	echo $name."</div></a>";
	echo "<div class='photo'>".$news->getProfilePic($cuser)."</div>";
    echo "<div id='profileinfo'>";
    if (isset($DOB1)){
	$birthday = "<tr><td class='tal'>Birthday:</td><td id='privacyb' style='display:none'>".date_format(date_create($DOB1),'jS M Y')."</td></tr>";//SOLUTION TO MAKING STUFF NOT GO OUTSIDE THE BOX IF THE USER IS NOT TRACKING
}
	else{
		$birthday = "";
	}
    $statushover='';
    if($_SESSION['UserID']==$cuser):
    $statushover = ' statushover';
	elseif($users->trackingCheck($_SESSION['UserID'],$cuser)==0)://TRACK AND REQUEST PENDING IS WAYY TOOO LONG(pushes other stuff out)
			echo "<div class='mid box lightgreen font14' id='trackthisperson'>Track this person</div>";
			$birthday = "";//THINK OF BETTER WAY
		elseif($users->trackingCheck($_SESSION['UserID'],$cuser)==1):
			echo "<div class='mid box grey font14' id='pendingtrack'>request pending</div>";
			$birthday = "";//THINK OF BETTER WAY
		else:
			echo "<div class='mid box font14' id='stoptrack'>Tracking</div>";
		endif;
		//if($_SESSION['UserID']!=$cuser && $users->trackingCheck($cuser,$_SESSION['UserID'])==2):
		//	echo "<a id='blocktracker'>Block Tracker</a>";
		//endif;
	echo "<p id='status' class='statusboard".$statushover."'>".$status."</p>"; 
	//echo $users->loadSports($cuser,2);
	echo "</div>";
	echo "<table><tr><td class='tal'>Gender:</td><td style='min-width:70px'>".$users->getGender($Gender1)."</td>";
	echo"<td class='tal'>Weight:</td><td id='privacy1' style='display:none'>".$Weight1."</td></tr><tr><td class='tal'>Age:</td><td id='privacya' style='display:none'>".$users->getAge($DOB1)."</td><td class='tal'>Height:</td><td id='privacy2' style='display:none'>".$Height1." cm</td></tr>".$birthday."</table>";
	if ($_SESSION['UserID']==$cuser):
	echo "<a href='editprofile.php'><div class='editprofile sp'></div></a>";
	endif;
	echo "</div>";
	echo "<div id='programsCon' class='container'><a class='TitleConlink' href='program.php".$getuser."'><div class='TitleCon hover h3'>Programs</div></a>";
	echo "\t\t\t<table id=\"list\">\n";
        include_once 'inc/class.lists.inc.php';
   		$lists = new GymScheduleItems($db);
   		echo $lists->loadProgramListForBoard($cuser);
   		echo "\t\t\t</table></div>";
   	?><script>
   	if($("#programsCon").find("tr").length==1){
   		if(<?php echo $_SESSION['UserID']?> == <?php echo $cuser?>){
   			$("#programsCon").append("<p>You can create your own custom workout program by clicking 'Programs'</p>");
	   	}else{
		   	$("#programsCon").append("<p><?php echo $Forename?> doesn't seem to have any workout programs yet. Recommend some to <?php echo $him?>!</p>");
		}
	   	   	
   	}
   	
   	if("<?php echo $Height1 ?>"==0||""){
   		$("#privacy2").prev().remove();
   		$("#privacy2").remove();   		
   	}
   	if("<?php echo $Weight1 ?>"=="0.0"||""){
   		$("#privacy1").prev().remove();
   		$("#privacy1").remove();
   	}
   	else if (<?php echo $lbkg1 ?>==0){
   		$("#privacy1").append("lbs");}
   	else{
   		$("#privacy1").append("kg");}</script><?php
	echo "<div id='progressCon' class='container'><a class='TitleConlink' href='progress.php".$getuser."'><div class='TitleCon  h3 hover'>Progress</div></a><table>";
	$goals = $users->loadGoalsByUserID($cuser,2);
	$lastworkouts = $news->loadLastWorkouts($cuser);
	echo $lastworkouts;
	if($lastworkouts == ""){
		if($cuser == $_SESSION['UserID'])
			echo "<p>Your recent workouts will show up here after you've worked out.</p><p>Click into 'Progress' and start recording what you've done or create a program in 'Programs' if you haven't done so already.</p>";
		else
			echo "<p>This user hasn't worked out yet. What a lazy bum. Go motivate ".$him."!</p>";
	}
	if($goals==true)
	echo "<br /><h3>One of ".$Forename1."'s goals</h3>";
	echo "</table></div>";
	if ($_SESSION['UserID']==$cuser):
	echo "<div id='newsCon'><div class='TitleCon hover h3' onclick='window.location=\"/news.php".$getuser."\"'?>News & Updates</div><div id='newscontent'>";
	$news->getMiniNews();
	echo "</div></div>";
	endif;

	echo "<div id='boardCon'>";
	?>
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
            				
	<form action="db-interaction/lists.php" id="add-post" method="post"> 
				<?php echo "<div class='miniphoto'>".$news->getProfilePic($cuser)."</div>" ?>
				<textarea id='addposttextbox' placeholder="Remember, be nice!" cols='80' rows="1" autocomplete='off' ></textarea>
				
				<div id='addpostbuttons' >
					<input type='submit' id='addpostsubmit' class="font14 mid box" value='Post it' />
					<input type='button' id='addpostcancel' class="font14 mid grey box" value='Cancel' />
				</div>
			</form>
						
			<div id='posts'>				
					
			</div>
			<div id='overlay'>
				<img src="images/loader.gif" />
			</div>
			<?php echo "</div>";
		//echo "<div id='toptracks'><a class='TitleConlink' href='toptracks.php".$getuser."'><div class='TitleCon  h3 hover'>Top Tracks</div></a>";
		echo "<div id='toptracks'><div class='TitleCon  h3'>Top Tracks</div>";
		$toptracks = $users->getrandTracking($cuser,1);
		echo $toptracks;
		if($toptracks == ""){
			if($cuser == $_SESSION['UserID'])
				echo "<p>You can choose your top tracks in 'Tracking' for easy access</p>";
			else    	
				echo "<p style='padding-top:0px'>".$Forename." hasn't picked any top tracks yet. You should talk to ".$him." and trick ".$him." into picking you</p>";
		}
		echo "</div>";
		echo "<div id='trackerCon'><div class='halftrack'>";
		echo "<a class='TitleConlink' href='tracking.php".$getuser."'><div class='TitleCon  h3 hover'>Tracking: (".$users->countTrack($cuser,1).")</div></a>";
    	$tracking = $users->getrandTracking($cuser,0);
    	echo $tracking;
    	if($tracking == ""){
    		if($cuser == $_SESSION['UserID'])
    			echo "<p>You can track your friends' or other people's progress by clicking 'Track' on their page.</p><p style='padding-top:0px'>You can also search for users in the search bar at the top of this page</p>";
			else
				echo "<p>".$Forename." doesn't feel the need to track anyone. Let ".$him." know that being self centered won't get you far in life";
		}		
    	echo "</div>";
    	echo "<div class='break'></div>";
		echo "<div class='halftrack'>";
    	echo "<a class='TitleConlink' href='trackers.php".$getuser."'><div class='TitleCon  h3 hover'>Trackers: (".$users->countTrack($cuser,0).")</div></a>";
		$tracker =  $users->getrandTrackers($cuser);
		echo $tracker;
    	if($tracker == "")
    		if($cuser == $_SESSION['UserID'])
    			echo "<p>Invite your friends to track your progress and give you motivation and advices on your workouts</p>";
    		else
				echo "<p>Poor ".$Forename.", has no one tracking ".$him.". Track ".$him." to give ".$him." some support!";
    	echo "</div></div>";
    	?>
    	</div>
<br /><br />
            <div class="clear"></div>
            
            <script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
            <script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
            <script type="text/javascript" src="js/autogrow.js"></script>
            <script type="text/javascript" src="js/charcounter.js"></script>
            <script type="text/javascript" src="js/users.js"></script>
            <script type="text/javascript" src="js/news.js"></script>
            <script type="text/javascript">
            if ("<?php echo $birthday?>"==""){
            	$(".tal:contains('Age:')").next().remove();
            	$(".tal:contains('Age:')").remove();
            }
            initializeNews("<?php echo $cuser?>");//INSPECT ELEMENT HACK?
            $("#trackthisperson").bind("click",function(){
			$(this).unbind();
			getTrackerO(<?php echo $cuser?>);
			})
            $("#addposttextbox").autogrow(); 
            $("#addposttextbox").charCounter(250);
            </script>
            <?php if($cuser==$_SESSION['UserID']):
            	?><script>
            	$.editable.addInputType('textarea1', {
                element : function(settings, original) {
                	var textarea = $('<textarea id="status1" class="statusboard" maxlength = "50" >');
					$(this).append(textarea);
                    return(this);
                },	
                submit: function (settings, original) {

                },
                content : function(string, settings, original) {
        				$("#status1", this).val(string);
    				}
     
            });
    // CLICK-TO-EDIT on list items
    $("#status").editable("db-interaction/users.php", {
        id        : 'UserID',
        indicator : 'Saving...',
        type      : 'textarea1',
        tooltip   : 'Double-click to edit.',
        event: 'dblclick',
    	select : false,
    	placeholder: "Double click to add a short description of yourself",
    	cancel:"<button class='grey small box'>cancel</button>",
    	submit:"<input type='submit' class='lightgreen small box' value='save'/>",
        submitdata: function(){
        	var status = $("#status1").val();
                    	var hash = {};
                    	hash["status"] = status;
                    	hash["action"] = "updatestatus";
                    	return hash;
	}
    });
    </script>
    <?php endif;?>
            <script src="js/grid.locale-en.js" type="text/javascript"></script>
			<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>
<?php
    		if ($cuser!=$_SESSION['UserID']):
?>
<script>
var relationship = <?php echo $users->trackingCheck($_SESSION['UserID'],$_GET['user']) ?>;
var privacy = baseTenConvert(<?php echo $Privacy1 ?>,16);
for (var i=1; i<3; i++)
    	{
    		if (privacy[i]==2)
    		{
    			$("#privacy"+i).text("Private");
    			$("#privacy"+i).show();
    			
    		}
    		else if (privacy[i]==1 && (relationship==0||relationship==1))
    		{
    			$("#privacy"+i).text("Private");
    			$("#privacy"+i).show();
    		}
    		else
    		{
    			$("#privacy"+i).show();
    		}
    	}
		if (privacy[0]==2)
    	{
    		$("#privacya").text("Private");
    		$("#privacya").show();
    		$("#privacyb").text("Private");
    		$("#privacyb").show();
    	}
    	else if (privacy[0]==1)
    	{
    		$("#privacya").text("Private");
    		$("#privacya").show();
	  		var DOfB = $("#privacyb").text();
    		splitDOfB = DOfB.split(" ");
    		$("#privacyb").text(splitDOfB[0]+" "+splitDOfB[1]);
    		$("#privacyb").show();
    	}
    	else
    	{
    		$("#privacya").show();
    		$("#privacyb").show();
    	}
</script>
<?php
else:
?>
<script>
for (var i=1; i<3; i++)
{
	$("#privacy"+i).show();
}
$("#privacya").show();
$("#privacyb").show();
</script>
<?php
endif;
	
/*elseif(isset($_GET['list'])):                 
    echo "ttt<ul id='list'>n";
 
    include_once '/inc/class.lists.inc.php';
    $lists = new GymScheduleItems($db);
    list($LID, $URL) = $lists->loadListItemsByProgramID();
 
    echo "\t\t\t</ul>";
    else:
    */

?>
                   
            <!--<img src="/assets/images/newlist.jpg" alt="Your new list here!" />-->
                   

                                       
        </div>
		
