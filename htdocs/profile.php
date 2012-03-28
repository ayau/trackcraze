<?php
	include_once "common/base.php";
    //if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
      //  && $_SESSION['LoggedIn']==1):
    include_once 'inc/class.users.inc.php';
    	include_once "common/header.php";
    	$Username=$_SESSION['Username'];
	echo "<div id='container'>";
	include_once "common/sidebar.php";	
	include_once "common/rsidebar.php";
    $users = new GymScheduleUsers($db);
	//if ($_GET['user']==NULL)://THIS LINE IS PROBLEMATIC!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	list($Surname, $Forename, $Gender, $DOB, $Weight, $lbkg, $Height, $Heighti, $Phone, $Email, $Location, $Privacy, $TrackerO, $status, $Bmeasurements, $Unit, $Setting) = $users->loadProfileByUser($cuser);
	$Measurements = unserialize($Bmeasurements);
	if (isset($DOB)){
	$birthday = date_create($DOB);
	$DOfB = date_format($birthday,'jS M Y');}
	else{
		$DOfB = "";
	}
	$pageTitle = "$Forename $Surname";
?>
<title><?php echo $pageTitle ?></title>
<div id="main">
	<noscript>This site just doesn't work, without JavaScript, period. (not so period: and also cause we're shitty developers and javascript is easier to use)</noscript>
	<div id="profile">
		<div><h1><?php echo ($Forename." ".$Surname) ?><a href="editprofile.php" class="fitwidth box" id="editbutton">Edit your Profile</a></h1></div>
				<h2><span class="hoverheading hovered">User Information</span>|<span class="hoverheading">Goals and Body Measurements</span></h2>
			<span id="userinformation"><table border="0">	
				<tr>
					<td class='tal'>Gender</td>
					<td><?php echo ($users->getGender($Gender))?></td>
				</tr>
				<tr id="privacya">
					<td class='tal'>Age</td>
					<td><?php echo ($users->getAge($DOB))?></td>
				</tr>
				<tr id="privacyb">
					<td class='tal'>Birthday</td>
					<td><span id="DOfB"><?php echo $DOfB ?></span></td>
				</tr>
				<tr id="privacy1">
     				<td class='tal'>Weight</td>
     				<td class="weightrow"></td>
    </tr>
				<tr id="privacy2">
					<td class='tal'>Height</td>
					<td class="heightrow"><?php echo $Height ?> cm / </td>
				</tr>
			</table>
			<br /><br />
		<span><h2>Sports</h2>
			<br />
			<ul class="tags">
			
		<?php $users->loadSports($cuser,1) ?>
		</ul> 
		<br /><br /><br /></span>
		<h2>Contact Information</h2>
			<table border="0">
				<tr id="privacy3">
					<td class='tal'>Phone</td>
					<td><?php echo $Phone ?></td>
				</tr>
				<tr id="privacy4">
					<td class='tal'>Email</td>
					<td><?php echo $Email ?></td>
				</tr>
				<tr id="privacy5">
					<td class='tal'>Location</td>
					<td><?php echo $Location ?></td>
				</tr>
			</table></span>
		<span id="goalsbmi" style="display:none">
			<br />
			<table border='0' id="goals">
				<tr>
					<td class='tat'>Goal</td>
					<td class='tat'></td>
					<td class='tat'>Weight from target</td>
				</tr>
				<?php echo $users->loadGoalsByUserID($cuser,1) ?>
			</table>
			<h2>Body Measurements</h2>
			<table border="0" id="bodymeasurements">
				<tr>
					<td class='tal'>Chest Size</td>
					<td id='m0'></td>
					<td class='tal'>Forearm Size</td>
					<td id="m1"></td>
				</tr>
				<tr>
					<td class='tal'>Waist Size</td>
					<td id="m2"></td>
					<td class='tal'>Thigh Size</td>
					<td id="m3"></td>
				</tr>
				<tr>
					<td class='tal'>Hip Size</td>
					<td id="m4"></td>
					<td class='tal'>Calve Size</td>
					<td id="m5"></td>
				</tr>
				<tr>
					<td class='tal'>Bicep Size</td>
					<td id="m6"></td>
					<td class='tal'>Neck Size</td>
					<td id="m7"></td>
				</tr>
				<tr>
					<td class='tal'>Shoulders</td>
					<td id="m8"></td>
				</tr>
			</table>
			<span id="BMIcalc"><h2>BMI Calculator</h2>
			<table border="0">
				<tr>
					<td class='tal'>Gender</td>
					<td><?php echo ($users->getGender($Gender))?></td>
					<td><input class="inputnumber" type='text' id='weightbmi'> <select id="bmilbkg"><option value="0">lbs</option><option value="1">kg</option></select> <input class="inputnumber" type='text' id='heightbmi'><button type="button" id='calc'>Calculate BMI</button> <button type="button" id='loadstats'>Load Me</button></td>
				</tr>
				<tr>
					<td class='tal'>Age</td>
					<td id="age"><?php echo ($users->getAge($DOB))?></td>
					<td>Your BMI is <input class="inputnumber" type='text' id='bmibox'></td>
				</tr>
				<tr>
					<td class='tal'>Weight</td>
					<td class="weightrow"></td>
					<td><select id="gender"><option value="0">Male</option><option value="1">Female</option><option value="2">Undisclosed</option></select> <input class="inputnumber" type='text' id='agebmi'> <button type="button" id='percentile'>Find Percentile</button> <button type="button" id='loadage'>Load Age</button></td>
				</tr>
				<tr>
					<td class='tal'>Height</td>
					<td class="heightrow"><?php echo $Height ?> cm / </td>
					<td><span id="percentileresult"></span></td>
				</tr>
			</table></span>
		</span>		

	</div>
</div>
	<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
	<script type="text/javascript" src="js/users.js"></script>
	<script type="text/javascript">
	if("<?php echo $Phone ?>"==""||"<?php echo $Phone ?>"==0){
		$("#privacy3").children("[class!=tal]").html("<a href='editprofile.php' class='toEditProfile'>Edit your profile to add a phone number</a>");
	};
	if("<?php echo $Email ?>"==""){
		$("#privacy4").children("[class!=tal]").html("<a href='editprofile.php' class='toEditProfile'>Edit your profile to add an email</a>");
	};
	if("<?php echo $Location ?>"==""){
		$("#privacy5").children("[class!=tal]").html("<a href='editprofile.php' class='toEditProfile'>Edit your profile to add your location</a>");
	};
		profile("<?php echo $Measurements[0] ?>","<?php echo $Measurements[1] ?>","<?php echo $Measurements[2] ?>","<?php echo $Measurements[3] ?>","<?php echo $Measurements[4] ?>","<?php echo $Measurements[5] ?>","<?php echo $Measurements[6] ?>","<?php echo $Measurements[7] ?>","<?php echo $Measurements[8] ?>","<?php echo $Unit ?>","<?php echo $Heighti ?>","<?php echo $Weight ?>","<?php echo $lbkg ?>","<?php echo $Height ?>","<?php echo $Gender ?>","<?php echo $DOB ?>");
		$("#topbar").hide();//do it in the bar
alert("lol");
	</script>
<?php
    		if ($cuser!=$_SESSION['UserID']):
    	?>
    	<script type="text/javascript">
    	privacy = baseTenConvert(<?php echo $Privacy ?>,15);
    	var splitDOB = "<?php echo $DOB ?>".split("-")
    	today = new Date();
    	$(".toEditProfile").each(function(){
    		$(this).parent().remove();
    	});
    	if("<?php echo $Phone ?>"==""){
		$("#privacy3").remove();
		};
		if("<?php echo $Email ?>"==""){
			$("#privacy4").remove();
		};
		if("<?php echo $Location ?>"==""){
			$("#privacy5").remove();
		};
		if("<?php echo $Weight ?>"==""||"<?php echo $Weight ?>"==0){
			$("#privacy1").remove();
		};
		if("<?php echo $Heighti ?>"==""){
			$("#privacy2").remove();
		};
    	$("#BMIcalc").remove();
    	$("#editbutton").hide();
    	var relationship = <?php echo $users->trackingCheck($_SESSION['UserID'],$_GET['user']) ?>;
    	for (var i=1; i<6; i++)
    	{
    		if (privacy[i]==2)
    		{
    			$("#privacy"+i).remove();
    			
    		}
    		else if (privacy[i]==1 && (relationship==0||relationship==1))
    		{
    			$("#privacy"+i).remove();
    		}
    	}
    	for(var i=0; i<9; i++){
    		if (privacy[i+6]==2)
    		{
    			$("#m"+i).prev().remove();
    			$("#m"+i).remove();
    		}
    		else if (privacy[i+6]==1 && (relationship==0||relationship==1)){
    			$("#m"+i).prev().remove();
    			$("#m"+i).remove();
    		}
    	}
    	if (privacy[1]==2){
    		$(".targettext:contains('go!!')").remove();
    	}
    	else if (privacy[2]==1 && (relationship==0||relationship==1)){
    		$(".targettext:contains('go!!')").remove();
    	}
    	if (privacy[0]==2)
    	{
    		$("#privacya").remove();
    		$("#privacyb").remove();
    	}
    	else if (privacy[0]==1)
    	{
    		$("#privacya").remove();
    		var DOfB = "<?php echo $DOfB ?>";
    		splitDOfB = DOfB.split(" ");
    		$("#DOfB").replaceWith(splitDOfB[0]+" "+splitDOfB[1]);
    	}
    	</script>
    	<?php
    		//else:
    	//	endif;
    	?>
<?php
endif;
	include_once "common/footer.php"; 

?>

