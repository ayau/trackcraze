<?php
 include_once "common/base.php";
    if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1):
        $Username=$_SESSION['Username'];
    include_once 'inc/class.users.inc.php';
    $users = new GymScheduleUsers($db);
 list($Surname, $Forename, $Gender, $DOB, $Weight, $lbkg, $Height, $Heighti, $Phone, $Email, $Location, $Privacy, $TrackerO, $status, $Bmeasurements, $Unit, $Setting) = $users->loadProfileByUser($_SESSION['UserID']);//MAKE NEW FUNCTION
 if (isset($DOB)){
 list($YOfB,$MOfB,$DDOfB) = explode("-",$DOB);}
 else{
 	$today = getdate();
 	list($YOfB,$MOfB,$DDOfB) = array($today['year'],$today['mon'],$today['mday']);
 }
 $Measurements = unserialize($Bmeasurements);
 $pageTitle = "$Forename $Surname";
 include_once "common/header.php";
 echo "<div id='container'>";
 include_once "common/sidebar.php";
 include_once "txt/sports.php";
?>
<div id="main">
 <noscript>This site just doesn't work, without JavaScript, period. (not so period: and also cause we're shitty developers and javascript is easier to use)</noscript>
 <div id="profile">
  <h1><?php echo ($Forename." ".$Surname) ?></h1>
    <h2><span id="eprofile">Edit Profile</span>|<span id="goaloptions">Goals</span>|<span id="privacyoptions">Privacy Options</span>|<span id="trackeroptions">Update Options</span>|<span class='hoverheading'>Body Measurements</span></h2>
   <table id="edittable" border="0">
   	 <tr>
     <td class='tar tat'>Pictures</td>
     <td>
      <?php echo "<div class='photo'>".strip_tags($profilepic, '<img>')."</div>" ?>
    	<form enctype="multipart/form-data" action="uploader.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="700000" />
Choose a file to upload: <input name="file" type="file" />Maximum size-700kB<br />
<input type="submit" value="Upload File" />
</form>
	Or Choose from existing photos:	<input type='button' id='chooseprofilepic' alt="photochooser.php?height=400&width=570" title="Select a photo:" class="thickbox" value='Choose file'/>
	<div id='profilepicselect' hidden></div>
  </td>
    </tr>
    <tr>
     <td class='tar tat'>I Am</td>
     <td>
      <select id="gender">
       <option value="0">Male</option>
       <option value="1">Female</option>
       <option value="2">Undisclosed</option>
      </select>
      </td>
    </tr>
    <tr>
     <td class='tar tat'>Your Birthday</td>
     <td>
     <input id='weightdate' maxlength='10' size='7'/>
	</td>
 	<tr>
     <td class='tar tat'>Your Weight</td>
     <td id="weightrow">
      <input class="inputnumber" type='text' id='pweight' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo($Weight) ?>">
      <select id="plbkg">
       <option value="0">lbs</option>
       <option value="1">kg</option>
  </select> <select id="setting0"><option value="1">Automatically update from progress</option><option value="0">Don't automatically update</option></select>	
     </td>
</tr>
    <tr>
     <td class='tar tat'>Your Height</td>
     <td>
      <input class="inputnumber" type="text" id="heightf" onkeypress="return func_obj.numbersOnly(event)" onchange="func_obj.convertFtIToM()" value="<?php echo (($Heighti-fmod($Heighti,12))/12)?>"> ft <input class="inputnumber" type="text" id="heighti" onkeypress="return func_obj.numbersOnly(event)" onchange="func_obj.convertFtIToM()" value="<?php echo (fmod($Heighti,12))?>"> in </br> or </br><input class="inputnumber" type="text" id="height" value="<?php echo($Height) ?>" onkeypress="return func_obj.numbersOnly(event)" onchange="func_obj.convertMetersToFI()"> cm
     </td>
    </tr>
    <tr>
     <td class='tar tat'>Hobbies</td>
     <td><textarea id='sports' placeholder='What Hobbies do you enjoy?'></textarea></td><!-- limit character number-->
    </tr>
    <tr>
     <td class='tar tat'>Sports You Play</td>
     <td>
     	<span id="existingsports"><?php $users->loadSports($_SESSION['UserID'],0) ?></span>
     	<li><input type='text' id='sportsinput' value="" autocomplete='off' placeholder='What Sports do you play?'/><button type="button" id='addsportsbutton'>Add</button></li>
     </td><!-- limit character number-->
    </tr>
     <td class='tar tat'>Phone</td>
     <td><input type='text' id='phone' value="<?php echo($Phone) ?>" onkeypress="return func_obj.numbersOnly(event)">
	</td>
    </tr>
    <tr>
     <td class='tar tat'>Email</td>
     <td><input type='text' id='email' value="<?php echo($Email) ?>">
     	   <br /><br />
     </td>
    </tr>
    <tr>
     <td class='tar tat'>Location</td>
     <td><input type='text' id='location' value="<?php echo($Location) ?>">
     </td>
    </tr>
   </table>
   <span id='privacytable' style"display:none"><table border='0'><tr><td class='tar'>Birthday</td><td><select id='privacy0'><option value='0'>Show my Full Birthday in my profile</option><option value='1'>Show Day and Month only in my profile</option><option value='2'>Don't show my Birthday in my profile</option></select></td></tr><tr><td class='tar'>Weight</td><td><select id='privacy1'><option value='0'>Show my weight to everyone</option><option value='1'>Show my weight to trackers only</option><option value='2'>Don't show my weight in my profile</option></select></td></tr><tr><td class='tar'>Height</td><td><select id='privacy2'><option value='0'>Show my height to everyone</option><option value='1'>Show my height to trackers only</option><option value='2'>Don't show my height in my profile</option></select></td></tr><tr><td class='tar'>Phone</td><td><select id='privacy3'><option value='0'>Show my phone number to everyone</option><option value='1'>Show my phone number to trackers only</option><option value='2'>Don't show my phone number in my profile</option></select></td></tr><tr><td class='tar'>Email</td><td><select id='privacy4'><option value='0'>Show my email to everyone</option><option value='1'>Show my email to trackers only</option><option value='2'>Don't show my email in my profile</option></select></td></tr><tr><td class='tar'>Location</td><td><select id='privacy5'><option value='0'>Show my location to everyone</option><option value='1'>Show my location to trackers only</option><option value='2'>Don't show my location in my profile</option></select></td></tr></table>
   	
   </span>
   <span id="trackertable" style="display:none">
   	<table border='0'>
   		<tr>
   			<td class='tar'>Track requests</td>
   			<td>
   				<select id='tprivacy0'>
   					<option value='1'>Automatically accept trackers</option>
   					<option value='0'>Let me accept trackers</option>
   				</select>
   			</td>
   		</tr>
   		<tr>
   			<td class='tar'>Weight Update</td>
   			<td>
   				<select id='tprivacy1'>
   					<option value='0'>Notify trackers when I change my weight</option>
   					<option value='1'>Don't notify trackers</option>
   				</select>
   			</td>
   		</tr>
   		<tr>
   			<td class='tar'>Height Update</td>
   			<td>
   				<select id='tprivacy2'>
   					<option value='0'>Notify trackers when I change my height</option>
   					<option value='1'>Don't notify trackers</option>
   				</select>
   			</td>
   		</tr>
   		<tr>
   			<td class='tar'>Program Update</td>
   			<td>
   				<select id='tprivacy3'>
   					<option value='0'>Notify trackers when I update a program</option>
   					<option value='1'>Don't notify trackers</option>
   				</select>
   			</td>
   		</tr>
   		<tr>
   			<td class='tar'>New Programs</td>
   			<td>
   				<select id='tprivacy4'>
   					<option value='0'>Notify trackers when I add a new program</option>
   					<option value='1'>Don't notify trackers</option>
   				</select>
   			</td>
   		</tr>
   		<tr>
   			<td class='tar'>Contact Information</td>
   			<td>
   				<select id='tprivacy5'>
   					<option value='0'>Notify trackers when I update my contact information</option>
   					<option value='1'>Don't notify trackers</option>
   				</select>
   			</td>
   		</tr>
   		<tr>
   			<td class='tar'>Progress Update</td>
   			<td>
   				<select id='tprivacy6'>
   					<option value='0'>Notify trackers when I update my progress</option>
   					<option value='1'>Don't notify trackers</option>
   				</select>
   			</td>
   		</tr>
   	</table>
   	<h2>Progress Options</h2>
		<table border='0'>
   			<tr>
   				<td class='tal'>Weight</td>
   				<td><select id="refersetting0"><option value="1">Automatically update from progress</option><option value="0">Don't automatically update</option></select></td>
   			</tr>
   		</table>
   	</span>   	
<!--Goals -->   	
   	<span id="goaltable" style="display:none">
   		<span id="addnewgoal">
   		<table border='0'>
   		<tr>
   			<td class='tar'>Aims</td>
   			<td>I workout because:</td>
   			<td><input type="checkbox" value='0' id='aim1'> I want to keep Fit</td>
   			<td><input type="checkbox" > I want to get massive</td>
   			<td><input type="checkbox" > I want to lose weight</td>
   		</tr>
   		<tr>
   			<td></td>
   			<td></td>
   			<td><input type="checkbox" > I compete</td>
   		</tr>
   		 <tr>
   		 	<td></td>
    		<td colspan="4"><div class='break'></div></td>
  		</tr>
  		</table>
  		<table border='0'>
   		<tr>
   			<td class='tar'>Specify new goal</td>
   			<td>I want to:</td>
   			<td><input type="checkbox" name="goal massive" id="massive"><label for="massive"> Get more massive</label></td>
   			<td><input type="checkbox" name="goal less" id="less"><label for="less"> Lose Weight</label></td>
   			<td><input type="checkbox" name="goal iron" id="iron"> <label for="iron"> Push more iron</label></td>
   		</tr>
   	</table>
   	<table border='0'>
   		<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
   			<td><input type="input" id="othergoals" placeholder='Specify your own goals'></td>
   		</tr>
   	</table>
   	<table id='gainweight' border='0' style='display:none'>
   		<tr>
   			<td id="gainweightrow">I will be <input type="text" id="goalweight0" maxlength="4" class="inputnumber" onkeypress="return func_obj.numbersOnly(event)"> <select  class="goallbkg" id="goallbkg0"><option value="0">lbs</option><option value="1">kg</option></select> massive by <input type="text" id="goaldate0"> <span class="errormessage"></span></td>
   		</tr>
   	</table>
   	<table id="loseweight" border='0' style='display:none'>
   		<tr>
   			<td id='loseweightrow'>I will be <input type="text" id="goalweight1" maxlength="4" class="inputnumber" onkeypress="return func_obj.numbersOnly(event)"> <select class="goallbkg" id="goallbkg1"><option value="0">lbs</option><option value="1">kg</option></select> slim by <input type="text" id="goaldate1"> <span class="errormessage"></span></td>
   		</tr>
   	</table>
   	 <table id='pumpiron' border='0' style='display:none'>
     <tr>
      <td id="iron0"><input type="checkbox" id="subtype0"><label for="subtype0"> I will Bench Press </label><input type="text" maxlength="4" class="inputnumber goalweight" onkeypress="return func_obj.numbersOnly(event)"> <select class="goallbkg"><option value="0">lbs</option><option value="1">kg</option></select> for <input type="text" maxlength="3" class="inputnumber goalreps" onkeypress="return func_obj.numbersOnly(event)"> reps by <input class="goaldate" type="text"> <span class="errormessage"></span></td>
     </tr>
     <tr>
      <td  id="iron1"><input type="checkbox" id="subtype1"><label for="subtype1"> I will Dead Lift </label><input type="text" maxlength="4" class="inputnumber goalweight" onkeypress="return func_obj.numbersOnly(event)"> <select class="goallbkg"><option value="0">lbs</option><option value="1">kg</option></select> for <input type="text" maxlength="3" class="inputnumber goalreps" onkeypress="return func_obj.numbersOnly(event)"> reps by <input class="goaldate" type="text"> <span class="errormessage"></span></td>
     </tr>
     <tr>
      <td  id="iron2"><input type="checkbox" id="subtype2"><label for="subtype2"> I will Squat </label><input type="text" maxlength="4" class="inputnumber goalweight" onkeypress="return func_obj.numbersOnly(event)"> <select class="goallbkg"><option value="0">lbs</option><option value="1">kg</option></select> for <input type="text" maxlength="3" class="inputnumber goalreps" onkeypress="return func_obj.numbersOnly(event)"> reps by <input class="goaldate" type="text"> <span class="errormessage"></span></td> 
     </tr>
 </table>
 <button type="button" id='addnewgoalbutt'>Add goal</button>
 <br />
   <span id="errorline"></span><br /></span>
   		<br /><span id='existinggoals'><h2>Existing goals</h2>
   		<table border='0'>
   			<tr>
   				<td class='tat'></td>
   				<td class='tat'>Goal</td>
   				<td class='tat'></td>
   				<td class='tat'>Weight from Target</td>
   				<td></td>
   			</tr>
   			<?php echo $users->loadGoalsByUserID($_SESSION['UserID'],0) ?>
   		</table></span>
   	</span>
	<span id="measurements" style="display:none">
		<table border='0'>
			<tr>
				<tr>
					<td class='tal'>Chest Size</td>
					<td><input class="inputnumber" type='text' id='m0' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo $Measurements[0] ?>"> <select id="inchorcm0"><option value="0">in</option><option value="1">cm</option></select></td>
					<td class='tal'>Forearm Size</td>
					<td><input class="inputnumber" type='text' id='m1' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo $Measurements[1] ?>"> <select id="inchorcm1"><option value="0">in</option><option value="1">cm</option></select></td>
				</tr>
				<tr>
					<td class='tal'>Waist Size</td>
					<td><input class="inputnumber" type='text' id='m2' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo $Measurements[2] ?>"> <select id="inchorcm2"><option value="0">in</option><option value="1">cm</option></select></td>
					<td class='tal'>Thigh Size</td>
					<td><input class="inputnumber" type='text' id='m3' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo $Measurements[3] ?>"> <select id="inchorcm3"><option value="0">in</option><option value="1">cm</option></select></td>
				</tr>
				<tr>
					<td class='tal'>Hip Size</td>
					<td><input class="inputnumber" type='text' id='m4' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo $Measurements[4] ?>"> <select id="inchorcm4"><option value="0">in</option><option value="1">cm</option></select></td>
					<td class='tal'>Calve Size</td>
					<td><input class="inputnumber" type='text' id='m5' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo $Measurements[5] ?>"> <select id="inchorcm5"><option value="0">in</option><option value="1">cm</option></select></td>
				</tr>
				<tr>
					<td class='tal'>Bicep Size</td>
					<td><input class="inputnumber" type='text' id='m6' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo $Measurements[6] ?>"> <select id="inchorcm6"><option value="0">in</option><option value="1">cm</option></select></td>
					<td class='tal'>Neck Size</td>
					<td><input class="inputnumber" type='text' id='m7' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo $Measurements[7] ?>"> <select id="inchorcm7"><option value="0">in</option><option value="1">cm</option></select></td>
				</tr>
				<tr>
					<td class='tal'>Shoulders</td>
					<td><input class="inputnumber" type='text' id='m8' onkeypress="return func_obj.numbersOneDecimalPointWeight(event)" value="<?php echo $Measurements[8] ?>"> <select id="inchorcm8"><option value="0">in</option><option value="1">cm</option></select></td>
				</tr>
			</table>
			<h2>Body Measurements Privacy</h2>
   	<table border='0'>
   		<tr>
   			<td class='tar'>Chest Size</td>
			<td><select id='privacy6'><option value='0'>Show to everyone</option><option value='1'>Show to trackers only</option><option value='2'>Don't show in my profile</option></select></td>
			<td class='tar'>Forearm Size</td>
			<td><select id='privacy7'><option value='0'>Show to everyone</option><option value='1'>Show to trackers only</option><option value='2'>Don't show in my profile</option></select></td>
		</tr>
		<tr>
			<td class='tar'>Waist Size</td>
			<td><select id='privacy8'><option value='0'>Show to everyone</option><option value='1'>Show to trackers only</option><option value='2'>Don't show in my profile</option></select></td>
			<td class='tar'>Thigh Size</td>
			<td><select id='privacy9'><option value='0'>Show to everyone</option><option value='1'>Show to trackers only</option><option value='2'>Don't show in my profile</option></select></td>
		</tr>
		<tr>
			<td class='tar'>Hip Size</td>
			<td><select id='privacy10'><option value='0'>Show to everyone</option><option value='1'>Show to trackers only</option><option value='2'>Don't show in my profile</option></select></td>
			<td class='tar'>Calve Size</td>
			<td><select id='privacy11'><option value='0'>Show to everyone</option><option value='1'>Show to trackers only</option><option value='2'>Don't show in my profile</option></select></td>
		</tr>
		<tr>
			<td class='tar'>Bicep Size</td>
			<td><select id='privacy12'><option value='0'>Show to everyone</option><option value='1'>Show to trackers only</option><option value='2'>Don't show in my profile</option></select></td>
			<td class='tar'>Neck Size</td>
			<td><select id='privacy13'><option value='0'>Show to everyone</option><option value='1'>Show to trackers only</option><option value='2'>Don't show in my profile</option></select></td>
		</tr>
		<tr>
			<td class='tar'>Shoulders</td>
			<td><select id='privacy14'><option value='0'>Show to everyone</option><option value='1'>Show to trackers only</option><option value='2'>Don't show in my profile</option></select></td>
		</tr>
	</table>
	</span>
   	<button type="button" id='editpsave'>Save Changes</button>
   </div>
   <script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
   <script type="text/javascript" src="js/users.js"></script>
   <script type="text/javascript" src="js/autocomplete/jquery.autocomplete.js"></script>
   <script type="text/javascript" src="js/thickbox/thickbox.min.js"></script>
   <script language="javascript" src="js/datepicker/js/datepicker.js" type="text/javascript"></script>
   <script language="javascript" type="text/javascript" src="js/jquery.maskedinput.min.js"></script>
   <script type="text/javascript">
            var func_obj = new profileEdit(<?php echo $MOfB?>,<?php echo $YOfB?>,<?php echo $DDOfB?>,<?php echo($Gender)?>,<?php echo($lbkg)?>,<?php echo $Privacy ?>,<?php echo $TrackerO ?>,"<?php echo $Location ?>","<?php echo $Email ?>","<?php echo $Phone ?>","<?php echo $Height ?>","<?php echo $Weight ?>",<?php echo $Unit ?>, <?php echo $Setting ?>, <?php echo json_encode($sportlist)?>);
    </script>	   
    <link rel="stylesheet" href="js/thickbox/thickbox.css" type="text/css" media="screen" />
    <link rel="stylesheet" type="text/css" media="screen" href="js/datepicker/css/datepicker.css" />
<?php
 include_once "common/footer.php"; 
?>
</div>
<?php else: ?><!-- DO I NEED THE META IF OUR HEADER DISABLE THING?-->
<meta http-equiv="REFRESH" content="0;url=login.php">
<?php endif; ?>