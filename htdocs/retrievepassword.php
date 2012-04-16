<?php
	include_once "common/base.php";    
	$pageTitle = "Retrieve Password";
	include_once "common/header.php";
 
    if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['UserID'])):
 		echo "<meta HTTP-EQUIV='REFRESH' content='0; /board.php'>";
    else:
        include_once 'inc/class.users.inc.php';
        $users = new GymScheduleUsers($db);
        if (isset($_GET['v']) && isset($_GET['e'])):
        if ($users->checkEmailExists($_GET['e'],$_GET['v'])==1):
        ?>
<br />
STOP TRYING TO FUCK AROUND WITH THE LINKS!!!!! AINT GONNA WORK!!!
<script>
setTimeout("window.location = '/board.php'",3000);
</script>
<?php else:?>
<span>
<br />
Enter a new password that you would like to use:
<br />
<br />
<form method="post"  id="forgetpassword">
<label class="toplabel" for="password">New Password:</label>
Passwords should be 6-18 characters long.
<input type="password" id="password" class='inputfields' placeholder='Password'/> <!-- PASSWORD SHOULD ONLY BE 6 TO 18 CHARACTERS -->
<br />
<label class="toplabel" for="password">Retype password:</label>
<input  type="password" id="passwordre" class='inputfields' placeholder='Retype Password'/>
<div id ='errorpass' class='errorsignup' hidden></div> 
<br />
<input type="button" name="passwordforget" id="passwordforget" class="wide box lightgreen" value="Reset Password" />
</form>
</span>
<script>
function checkPassword(text,text1){
      	if (text!=text1){
      		 $("#errorpass").text("Please retype your passwords (they don't match)").show();
          return false;
      	}else{
      		var exp = /^[a-zA-Z0-9_-]{6,18}$/;
      		if(exp.test(text)==false){
         $("#errorpass").text('Password should be 6-18 characters. Letters, numbers, underscores and hyphens allowed.').show();
          return false;
         }else{
         	resetpassword();
         }
      	}       
      }
$("#passwordforget").live("click",function(){
	checkPassword($("#password").val(),$("#passwordre").val());
});
function resetpassword(){
	$.ajax({
		type: "POST",
		url: "db-interaction/users.php",
		data: "action=resetpassword&shamail="+"<?php echo $_GET['e'] ?>"+
		"&vercode="+"<?php echo $_GET['v'] ?>"+
		"&password="+$("#password").val(),
	success: function(){
		//alert(r);
		$("form").remove();
		$("span").text("Your password has been reset, you can now login with your new password");
		setTimeout("window.location = '/login.php'",3000);
	},
	error:function(){}  
	});
}
</script>
<?php
		endif;
		else:
		echo "<meta HTTP-EQUIV='REFRESH' content='0; /board.php'>";
		endif;
    endif;
    include_once 'common/footer.php';
?>