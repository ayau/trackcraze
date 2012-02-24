<?php
	include_once "common/base.php";
	include_once "inc/class.users.inc.php";
	include_once 'inc/class.news.inc.php';
	$news = new GSNews($db);
	$users = new GymScheduleUsers($db);
	if (isset($_GET['user'])):
		$cuser = $_GET['user'];
		$getuser ="?user=".$cuser;
	elseif(isset($_GET['program'])&&substr($_SERVER['REQUEST_URI'],0,12)=="/program.php"):
		$cuser = $program->loadUserID($_GET['program']);
		$getuser ="?user=".$cuser;
	else:
		$cuser = $_SESSION['UserID'];
		$getuser="";
	endif;
	list($Surname1, $Forename1, $Gender1, $DOB1, $Weight1, $lbkg1, $Height1, $Heighti1, $Phone1, $Email1, $Location1, $Privacy1, $TO, $status, $Bmeasurements1, $Unit1, $Setting1) = $users->loadProfileByUser($cuser);
	$profilepic = $news->getProfilePic($cuser);
    if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1):
        $Username=$_SESSION['Username'];
    include_once 'inc/class.users.inc.php';
	if ($cuser!=$_SESSION['UserID']&&substr($_SERVER['REQUEST_URI'],0,9)=="/news.php"):
	echo "Stop trying to read other people's stuff!";
	die();
	endif;
	if ($lbkg1 ==NULL):// LOLOLOLOLOLOLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLLL
	echo "This user does not exist.";
	die();
	endif;
	
?>

<div id="lsbar">
		<?php echo "<div class='photo'>".$profilepic."</div>" ?>
		<br />
    	<a href="/board.php<?php echo $getuser ?>"+$UID>Board</a>
    	<a href="/program.php<?php echo $getuser ?>">Programs</a>
    	<a href="/progress.php<?php echo $getuser ?>">Progress</a>
    	<a href="/photo.php<?php echo $getuser ?>">Photos</a>
    	<a href="/profile.php<?php echo $getuser ?>">Profile</a>
    	<div class='break'></div>
    	<?php    	
    	echo "<a href=\"/tracking.php".$getuser."\">Tracking (".$users->countTrack($cuser,1).")</a>";
    	echo "<a href=\"/trackers.php".$getuser."\">Trackers (".$users->countTrack($cuser,0).")</a>";
    	?>
    	<div class='break'></div>
    	<h3>Top Tracks</h3>
    	<?php
    	echo $users->getrandTracking($cuser,2)?>
</div>
<div id="topbar">
	<h2><?php echo $Forename1." ".$Surname1?></h2>
	<?php 		$statushover='';
	if($_SESSION['UserID']==$cuser):
			$statushover =' statushover';
	elseif($users->trackingCheck($_SESSION['UserID'],$cuser)==0):
			echo "<a id='trackthisperson'>Track this person</a>";
		elseif($users->trackingCheck($_SESSION['UserID'],$cuser)==1):
			echo "<a id='pendingtrack'>Track request pending</a>";
		endif; 
	echo "<p id='status' class=\"statustop".$statushover."\">".$status."</p>"?>
	<div class='progressbar'></div>
	
</div>
	<script type="text/javascript" src="/js/users.js"></script>
	<script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
            <script type="text/javascript" src="js/autogrow.js"></script>
            <script type="text/javascript" src="js/charcounter.js"></script>
	<script>
		$("#trackthisperson").bind("click",function(){
			$(this).unbind();
			getTrackerO(<?php echo $cuser.','.$TO?>);
		})
		</script>
		<?php if($cuser==$_SESSION['UserID']):
            	?><script>
            	$.editable.addInputType('textarea1', {
                element : function(settings, original) {
                	var textarea = $('<textarea id="status1" class="statustop" maxlength = "50" >');
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
    	cancel:"cancel",
    	submit:"save",
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
<?php endif; ?>