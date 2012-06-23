<?php
    include_once "common/base.php";
    $pageTitle = "Feedback";
    include_once "common/header.php";
 
   
        include_once 'inc/class.users.inc.php';
        $users = new GymScheduleUsers($db); 
        
        if(isset($_SESSION['UserID']))
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
       echo '<div style="float:left">';
       // <!--<form method="post" name="feedbackform" id="feedbackform">
      //      <div>-->
            	echo '<label class="toplabel" for="Name">Your Name</label>';
            	if(isset($Forename)&&isset($Surname)){
            	echo "<input type='text' name='name' id='name' class='inputfields' placeholder='Name' value=\"".$Forename." ".$Surname."\"/>";
        		}else{
        			echo "<input type='text' name='name' id='name' class='inputfields' placeholder='Name' />";
        		}
                echo "<br /><br />";
                echo '<label class="toplabel" for="username">Your Email</label>';
                if(isset($_SESSION['Username'])){
                echo "<input type='text' name='username' id='username' class='inputfields' placeholder='Email' value=\"".$_SESSION['Username']."\"/>";
            	}else{
            		echo "<input type='text' name='username' id='username' class='inputfields' placeholder='Email' />";
            	}
?>               
                 <br /><br />
                <label class="toplabel" for='feedback'>Your comments:</label>
				<textarea class='inputtextarea' id="feedback" rows="10" cols="55" placeholder="Be honest! We won't hate you."></textarea>
				<br />
                <input type="submit" name="Submit" id="submitfeedback" value="Submit" class="lightgreen wide box" />
                 <p id='feedbackthanks'></p>
                 </div>
                 <img style='float:left; margin-left:50px' src='images/mail.png'/>
           <!-- </div>
    </form> -->
<?php
    //endif;
?>
 
      <script>//FIX THIS WHEN LAUNCH!!!!!!!!!!!!!!!!!!!!!!!!!
      $('#submitfeedback').live("click",function(){
      	if ($("#feedback").val()){
      	$.ajax({
       						type: "POST",
       						url: "db-interaction/users.php",
       						data: "action=sendfeedback&email="+$("#username").val()+"&name="+$("#name").val()+"&comment="+$("#feedback").val(),
       						success: function(){
       							$("#feedbackthanks").text('Thank you for your feedback').show();
       						},
      						error: function(){
      							$("#feedbackthanks").text("There's an error sending your feedback").show();
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