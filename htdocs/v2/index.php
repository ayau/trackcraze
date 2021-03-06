<?php 
	require 'common/base.php';
	require 'model/class.user.php';
	$user = new User();
	
	require 'utils/facebook/facebook.php';

	if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['uid']) && $_SESSION['uid'] != 0){
		header('Location: program.php');
	}else{
		$facebook = new Facebook(array(
	  		'appId'  => FB_APID,
	  		'secret' => FB_SECRET,
		));
		// See if there is a user from a cookie
		$fb_user = $facebook->getUser();

		if (isset($fb_user)) {
		    try {
			    // Proceed knowing you have a logged in user who's authenticated.
			    $fb_user_profile = $facebook->api('/me');
			    echo "<meta http-equiv='Refresh' content='0;URL=program.php'>";
		  	} catch (FacebookApiException $e) {
		    	$fb_user = null;
		  	}
		}

	}
?>

<!DOCTYPE html>
<html style='overflow:hidden; width:100%; height:100%' xmlns:fb="http://www.facebook.com/2008/fbml">

<head>
	<title>trackCraze</title>
	<meta name='KEYWORDS' content='trackcraze,fitness,tracking,gym,workout,log,exercise,record,share'/>
	<meta name='Description' content="Trackcraze lets you follow other people's workout, record your own, and share it with others. Track your friends, your idol, and most importantly, yourself. Make fitness simple, customizable and motivating."/>

	<meta charset="UTF-8">
	<link rel="stylesheet" href="style.css" type="text/css" />
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js?ver=1.3.2'></script>
	<script type='text/javascript' src='js/jquery.min.js'></script>
</head>

<body style='margin:0px; width:100%; height:100%'>
<div id='index_bg'></div>

<div style='position:absolute; top:15px; right:45px'>
	<div id='signup' style='float:right; margin:5px' class='btn'>SignUp</div>
	<div id='login' style='float:right; margin:5px' class='btn'>Login</div>
</div>

<center id='index_logo' ><img style='margin-right:30px' src='images/trackcraze_logo.png'/></center>
<div id='index_strip'>
	<div style='position:absolute; top:50%; width:100%'>
	<div id='index_top_strip'></div>
	</div>
	<div style='position:absolute; top:50%; margin-top:80px; width:100%;'>
		<div id='index_divider' style='position:relative; background-color:#004F75; height:1px; border-top: 9px solid #005E8D; border-bottom: 1px solid #005E8D; '></div>
		<div style='background-color:#006699; width:100%; height:60px; border-bottom: 3px solid #004F75;'></div>
		<div style='background-color:rgba(0,0,0,0.30); width:100%; height:5px;'></div>
	</div>
</div>
<div style='position:absolute; top:50%; width:100%'> <!--for firefox (doesn't support negative margins -->
<div id='index_container'>
		<div id='title_container'><p id='title'>trackCraze</p><p style='display:none' id='mini_title'>Log in</p></div>
	<center>
		<div style='z-index:2; width:320px;'>
			<input id='name_enter' class='input' type='text' placeholder='Your Name' autocomplete='off' style='margin-bottom:12px; display:none' />
			<input id='email_enter' class='input' type='text' placeholder='Email' autocomplete='off'/>
			<div id='email_button'></div>
			<input hidden id='password_enter' style='position:relative; top:-20px; margin-bottom:10px' class='input' type='text' placeholder='Password' autocomplete='off'/>
			<div id='index_button' class='btn'>Log in</div>
			<div id='facebook_login' hidden style='position:relative; left:-150px; top:-2px; z-index:2'>
				<fb:login-button size="large"></fb:login-button><div id="fb-root"></div>
			</div>
		</div>
	</center>
</div>
</div>
<div style='position:absolute; top:50%; margin-top:100px; width:100%'>
	<div style='position:relative; left:50%'>
		<div id='tag_line'><h6>a simple workout tracking application</h6></div>
	</div>
	<center>
		<p id='description' style='position:relative; top:50px;'></p>
	</center>
</div>


<footer style='position:absolute; bottom:10px; width:100%'>
	<center>
      	<nav id="footer_items">
			<ul style='float:left'>
	        	<li><div class='nav_button_disabled'>Meet the Team</div></li>
	        	<li><a href="http://behindtheworkout.com" class="noUnderline"><div class='nav_button'>Our Blog</div></a></li>
	        	<li><a href="/feedback.php" class='noUnderline'><div class='nav_button'>Feedback</div></a></li>
	        	<li><a href="/terms.php" class='noUnderline'><div class='nav_button'>Terms of use</div></a></li>
	        	<li><a href="/privacypolicy.php" class='noUnderline'><div class='nav_button'>Privacy policy</div></a></li>
	        	<li><a href='/contact.php' class='noUnderline'><div class='nav_button'>Contact us</div></a></li>
	      	</ul>
      	</nav>
      </center>
</footer>

<script type='text/javascript'>
$(function () {
	var page = 0;	//0 -> default, 1 -> login, 2 -> signup
	var expanded = false;
	var transition_done = true;
	var index_bg = $("#index_bg");
	
	var descriptions = ["Create custom workout programs", "Record your workouts", "Track your progress",  "Monitor your goals", "Post snapshots at the gym", "Connect with your friends"];
	var desc_counter = 0;
	$("#description").text(descriptions[0]);
	
	adjust_screen();
	
	$(window).resize(function() {
		adjust_screen();
	});
	
	function adjust_screen(){
		if($("footer").position().top < $("#description").position().top + 310){
			$("footer").css("visibility","hidden");
			$("#index_logo").css("visibility", "hidden");
		}else{
			$("footer").css("visibility","visible");
			$("#index_logo").css("visibility", "visible");
		}
	}
	
	window.setInterval(function() {
    	$("#description").fadeOut(function(){
    		if(desc_counter < descriptions.length-1)
  				desc_counter ++;
  			else
  				desc_counter = 0;
  			$(this).text(descriptions[desc_counter]);
		}).fadeIn();
	}, 2500);
	
	$("#login").live("click",function(){
		
			if(page != 1){
				if(transition_done){
				transition_done = false;
				page = 1;
			
			$("#index_button").hide();
			
				if(!expanded){
					$("#index_logo").fadeOut();
					$("#mini_title").hide();
					$("#index_top_strip").animate({
							top: '-120px',
							height: '230px'
						}, {
			    		"duration": 300,
			    		specialEasing: {
			    			top: 'easeOutBounce',
			      			height: 'easeOutBounce'
			    		},complete: function() {
					  		populateLoginInput();
			    		}
					})
					$("#index_container").animate({
							top: '-125px'
						}, {
			    		"duration": 400,
			    		specialEasing: {
			      		top: 'easeOutBounce'
			    		}
					})
					$("#email_button").css("visibility","hidden");
					expanded = true;
				}else{
					populateLoginInput();
				}
			}
		}
	})
	
	function populateLoginInput(){
		$("#password_enter").fadeIn();
	    	$("#title").animate({
				left:'-240px'
			}, {
				"duration": 400,
				specialEasing: {
  					right: 'easeOutBounce'
	 				}
			});
			$("#name_enter").slideUp(function(){
				$("#mini_title").text("Log in");
	  			$("#mini_title").fadeIn();
	  			$("#tag_line h6").css({"color":"white"});
	  			$("#tag_line h6").text("Forgot password?");
	  			$("#tag_line").animate({
	  				left:'40px'
	  			})
	  			$("#tag_line").addClass('forgot_password');
	    		$("#tag_line").css({"visibility": "visible"});
	    	});
	    			
	    	$("#index_divider").animate({
			  	top: '0px'
			  }, {
			    "duration": 400,
			    specialEasing: {
			    	top: 'easeOutBounce'
			  	}, complete:function(){	
			  		$("#index_button").text("Log in");
			  		$("#index_button").fadeIn(function(){
			  			transition_done = true;
			  		});
			  		$("#facebook_login").fadeIn();
			  		
			  	}
			 })
	}
		
	$("#signup").live("click",function(){
			if(page != 2){
				if(transition_done){
				transition_done = false;
				page = 2;
			
			$("#index_button").hide(); 		
			$("#facebook_login").hide();
				if(!expanded){
					$("#index_logo").fadeOut();
					$("#mini_title").hide();
					$("#index_divider").animate({
			  			top: '60px'
			  		}, {
			    		"duration": 300,
			    		specialEasing: {
			    			top: 'easeOutBounce',
			      			height: 'easeOutBounce'
			  			}
			  		})
					$("#index_top_strip").animate({
							top: '-120px',
							height: '230px'
						}, {
			    		"duration": 300,
			    		specialEasing: {
			    			top: 'easeOutBounce',
			      			height: 'easeOutBounce'
			    		},complete: function() {
			    			populateSignUpInput();
			    		}
					})
					$("#index_container").animate({
							top: '-125px'
						}, {
			    		"duration": 400,
			    		specialEasing: {
			      		top: 'easeOutBounce'
			    		}
					})
					$("#email_button").css("visibility","hidden");
					expanded = true;
				}else{
					$("#index_divider").animate({
			  			top: '60px'
			  		}, {
			    		"duration": 400,
			    		specialEasing: {
			    			top: 'easeOutBounce',
			      			height: 'easeOutBounce'
			  			},complete:function(){		
							populateSignUpInput();
			  			}
			  		})
				}
				$("#tag_line").css("visibility", "hidden");
			}
		}
	})
	
	function populateSignUpInput(){
		$("#password_enter").hide().fadeIn();
	    	$("#title").animate({
	    		left:'-240px'
			}, {
	    		"duration": 400,
	    		specialEasing: {
	      			right: 'easeOutBounce'
	  			},complete:function(){
	  				transition_done = true;
	  			}
	  		});	  				
	  				
	  		$("#mini_title").text("Sign up");
	    	$("#mini_title").fadeIn();
	    	$("#name_enter").show();
	    	$("#index_button").text("Sign up");
			$("#index_button").fadeIn();
	}
	
	$(".forgot_password").live("click",function(){
		if(transition_done){
			transition_done = false;
			$("#facebook_login").hide();
			$("#index_button").hide();
			$("#email_button").css("visibility","visible");
			$("#mini_title").hide();
			$("#mini_title").text("Forgot Password");
		  	$("#mini_title").fadeIn();
		  	
		  	$("#tag_line").animate({
		  		left:'-285px'
		  	})
	
		  	$("#tag_line").fadeIn();
		  	$("#tag_line h6").text("Please enter the email address you used to sign up with trackCraze");
		  	$("#tag_line").removeClass('forgot_password');
		  	$("#tag_line").css("visibility","visible");
		  	$("#tag_line h6").css({"color":"#EEE"});
	
	
		  	//$("#tag_line").css("visibility","hidden");
		  	$("#password_enter").fadeOut(function(){
		  	$("#index_top_strip").animate({
							top: '-70px',
							height: '200px'
						}, {
			    		"duration": 300,
			    		specialEasing: {
			    			top: 'easeOutBounce',
			      			height: 'easeOutBounce'
			    		},complete: function() {
			    			transition_done = true;
			    		}
					})
					$("#index_container").animate({
						top: '-70px',
						}, {
			    		"duration": 300,
			    		specialEasing: {
			      		top: 'easeOutBounce'
			    		}
					})
				});
			expanded = false;
			page = 3;
		}
	})
	
	$("#index_button").live("click",function(){
		window.location ='program.php';
	})
	
});
</script>

<script type='text/javascript'>               
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>', 
          cookie: true, 
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
        	FB.api('/me', function(response) {
  				
        		$.ajax({
	    			type: "POST",
	    			url: "db-interaction/user.php",
	    			data: {
	    				"action":"facebook_login",
	    				"data":  JSON.stringify(response)
					},
	    			beforeSend: function(x) {
            			if (x && x.overrideMimeType) {
              				x.overrideMimeType("application/j-son;charset=UTF-8");
            			}
          			},	
	    			success: function(r){
	    				if(r)
	    					window.location = "program.php";
	    				else
	    					alert("Something went wrong. Try again?");
	    			},
	    			error: function(){
	    			    // should be some error functionality here
	    			}
	    		});    
  				//alert('Your name is ' + response.name);
			});	
        });
        FB.Event.subscribe('auth.logout', function(response) {
          //window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
      
</script>