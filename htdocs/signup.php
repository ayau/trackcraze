<?php
    include_once "common/base.php";
    $pageTitle = "Register";
    include_once "common/header.php";
    if(!empty($_POST['username'])&& false):	//Check if logged in. Remove false.
        include_once "inc/class.users.inc.php";
        $users = new GymScheduleUsers($db);
        echo $users->createAccount();
        
    elseif(isset($_GET['status']) && $_GET['status']=="completed"):
    ?>
    <br/><br/><br/><br/>
    <h3>Thank you for signing up. You will receive an email from us shortly.</h3>
    
    
    <?php
    else:
?>
 
        <div id="loginheading"><h2>Sign up</h2></div>
        <form method="post"  id="registerform">
            <div>
                <label class="toplabel" for="username">Email:</label>
                <input type="text" name="username" id="username" class='inputfields' placeholder='Email'/>
                <div id ='errormail' class='errorsignup' hidden></div>
                <br />
                
                <label class="toplabel" for="password">Password:</label>
                Passwords should be 6-18 characters long.
                <input type="password" id="password" class='inputfields' placeholder='Password'/> <!-- PASSWORD SHOULD ONLY BE 6 TO 18 CHARACTERS -->
                <div id ='errorpass' class='errorsignup' hidden></div><br />
                <label class="toplabel" for="password">Retype password:</label>
                <input  type="password" id="passwordre" class='inputfields' placeholder='Retype Password'/><br />
                <label class="toplabel" for="firstname">First Name:</label>
                <input type="text" id="firstname" class='inputfields' placeholder='First Name'/>
                <div id ='errorname' class='errorsignup' hidden></div><br />
                <label class="toplabel" for="surname">Last Name</label>
                <input type="text" id="surname" class='inputfields' placeholder='Last Name'/><br />
                I am <Input type='radio' name='gender' id='male' value='0'/><label for="male" class="normal">Male</label><Input type='radio' name='gender' id='female' value='1'/><label for="female" class="normal">Female</label><Input type='radio' name='gender' id='weird' value='2'/><label for="weird" class="normal">Undisclosed</label>
                <div id ='errorgender' class='errorsignup' hidden></div>
                <br /><br />
                 <label class="toplabel" for="surname">Alpha testing verification code</label>
                <input type="password" id="alpha" class='inputfields' placeholder='Enter Code here'/>
                <div id ='errorcode' class='errorsignup' hidden></div><br />
                <input type="button" name="register" id="register" class="wide box lightgreen" value="Sign up" />
                <br />
                <br />
            </div>
        </form>
 		<script>
 		function checkEmail(text)
      {
         var exp = /^[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[a-z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)\b$/;
         if(exp.test(text)==false){
          $("#errormail").text("Please enter a valid email").show();
          return false;
         }else{
         	return true;
         }
      }
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
         	return true;
         }
      	}       
      }
      function checkName(text,text1){
       var exp = /^([a-zA-Z0-9_-][ ]{0,1}){1,20}$/;
       if (text.length>20||text1.length>20){
       	$("#errorname").text("Maximum 20 characters please. If your name is Taumatawhakatangihangakoauauotamateaurehaeaturipukakapikimaungahoronukupokaiwhenuakitanatahu*, then we're sorry. (*actually means 'The hill of the flute playing by Tamatea — who was blown hither from afar, had a slit penis, grazed his knees climbing mountains, fell on the earth, and encircled the land — to his beloved', sorry about your penis I guess)").show();
       	return false;
       }else{
       if(exp.test(text)==false||exp.test(text1)==false){
          $("#errorname").text('1-20 characters only for name. Letters, numbers, underscores hyphens and space(no consecutive spaces) allowed.').show();
          return false;
         }else{
         	return true;
         }
     }
      }
      function checkGender(text){
      	if ($("input[name='gender']:checked").val()){
      		return true;
      	}else{
      		$("#errorgender").text("Please select a gender.").show();
      	}
      }
      function checkCode(text){
      		$.ajax({
      			type: "POST",
       			url: "db-interaction/users.php",
       			data: "action=alpha",
       			success: function(r){
       			if (text==r){
      				register();
      			}else{
      				$("#errorcode").text("This is not a valid code. Please email support@trackcraze.com if you would like to participate in alpha testing").show();
      			}
   			},
     		error:function(){}  
 			});
      }
    $("#register").live("click",function(){
    	$(".errorsignup").hide();
    	checkEmail($("#username").val());
       checkPassword($("#password").val(),$("#passwordre").val());
       checkName($("#firstname").val(),$("#surname").val());
       checkGender($("#gender").val());
       if (checkEmail($("#username").val()) && checkPassword($("#password").val(),$("#passwordre").val()) && $("#gender").val()!=3 && checkName($("#firstname").val(),$("#surname").val())){
       	checkCode($("#alpha").val());
       }
  });
  function register(){
  	$.ajax({
       type: "POST",
       url: "db-interaction/users.php",
       data: "action=createaccount&email="+$("#username").val()+
       "&password="+$("#password").val()+
       "&firstname="+$("#firstname").val()+
       "&surname="+$("#surname").val()+
       "&sex="+$("input[name='gender']:checked").val(),
       success: function(r){
       	if(r==1)
       		 $("#errormail").text("Sorry, that email is already in use.").show();
       	else if(r==0)
       		window.location = "/signup.php?status=completed"; 
       	
   },
     error:function(){}  
  });
  }
    </script>
<?php
    endif;
    include_once 'common/footer.php';
?>