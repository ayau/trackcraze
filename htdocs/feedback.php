<?php
    include_once "common/base.php";
    $pageTitle = "Home";
    include_once "common/header.php";
 
   
        include_once 'inc/class.users.inc.php';
        $users = new GymScheduleUsers($db); 
        list($Surname, $Forename, $Gender, $DOB, $Weight, $lbkg, $Height, $Heighti, $Phone, $Email, $Location, $Privacy, $TrackerO, $status, $Bmeasurements, $Unit, $Setting) = $users->loadProfileByUser($_SESSION['UserID']);//user might not be logged in. Need to change this when public launch!!!!!
        //if($users->accountLogin()===TRUE):
        //    echo "<meta HTTP-EQUIV='REFRESH' content='0; /board.php'>";
        //	exit;
       // else:
    //<div id="loginheading">       
      //  <h2>Thanks for your feedback</h2>
    //</div>-->
        //endif;
    //else:

               
       echo '<div id="loginheading"><h2 >Tell us what you think</h2></div>';
       // <!--<form method="post" name="feedbackform" id="feedbackform">
      //      <div>-->
            	echo '<label for="Name">Your Name</label>';
            	echo "<input type='text' name='name' id='name' class='inputfields' placeholder='Name' value=\"".$Forename." ".$Surname."\"/>";
                echo "<br /><br />";
                echo '<label for="username">Your Email</label>';
                echo "<input type='text' name='username' id='username' class='inputfields' placeholder='Email' value=\"".$_SESSION['Username']."\"/>";
?>               
                 <br /><br />
                <label for='comments'>Your comments:</label>
				<textarea class='inputtextarea' id="comments" rows="3" cols="50"></textarea> 
                <input type="submit" name="Submit" id="submitfeedback" value="Submit" class="button" />
                 <p id='feedbackthanks'></p>
           <!-- </div>
    </form> -->
<?php
    //endif;
?>
 
      <script>//FIX THIS WHEN LAUNCH!!!!!!!!!!!!!!!!!!!!!!!!!
      $('#submitfeedback').live("click",function(){
      	if ($("#comments").val()){
      	$.ajax({
       						type: "POST",
       						url: "db-interaction/users.php",
       						data: "action=sendfeedback&email="+$("#username").val()+"&name="+$("#name").val()+"&comment="+$("#comments").val(),
       						success: function(){
       							$("#feedbackthanks").text('Thank you for your feedback').show();
    							t = setTimeout(function(){$("#emailerror").fadeOut(1000)},5000);
       						},
      						error: function(){
      							$("#feedbackthanks").text('Thank you for your feedback').show();
    							t = setTimeout(function(){$("#emailerror").fadeOut(1000)},5000);
       						}
      					});
  					}
  });
      	if(<?php echo !empty($_SESSION['LoggedIn'])?> && <?php echo !empty($_SESSION['UserID'])?>){
      		//$("#username").val(<?php echo $_SESSION['UserID']?>);
      	}
      	</script>
<?php
    include_once "common/footer.php";
?>