<?php
	include_once "common/base.php";    
	$pageTitle = "Retrieve Password";
	include_once "common/header.php";
 
    if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['UserID'])):
 		echo "<meta HTTP-EQUIV='REFRESH' content='0; /board.php'>";
    else:
        include_once 'inc/class.users.inc.php';
        $users = new GymScheduleUsers($db);
?>
<br />
Clearly you're not working out hard enough if you've forgotten your password, you gotta login more frequently!!!
<br />
<br />
Please enter the email address you used to sign up with trackCraze:
<br />
<br />
<form method="post"  id="passwordforget">
<input type="text" name="username" id="username" class='inputfields' placeholder='Email'/>
<div id ='errormail' class='errorsignup' hidden></div> 
<br />
<input type="button" name="passwordforget" id="passwordforget" class="wide box lightgreen" value="Retrieve Password" />
</form>
<script>
$("#passwordforget").live("click",function(){
	$.ajax({
		type: "POST",
		url: "db-interaction/users.php",
		data: "action=retrievepassword&email="+$("#username").val(),
	success: function(r){
		if(r==1)
			$("#errormail").text("Sorry, but we do not have a record of that email").show();
		else if(r==0)
	window.location = "/passwordretrieve.php"; 
	},
	error:function(){}  
	});
});
</script>
<?php
    endif;
    include_once 'common/footer.php';
?>