<html style='overflow:hidden'>
<title>trackCraze</title>

<link rel="stylesheet" href="style.css" type="text/css" />
<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js?ver=1.3.2'></script>

<body style='margin:0px'>
<div id='index_bg'></div>

<div style='position:absolute; top:15px; right:45px'>
	<div id='signup' style='float:right; margin:5px' class='btn'>SignUp</div>
	<div id='login' style='float:right; margin:5px' class='btn'>Login</div>
</div>

<center id='index_logo' ><img style='margin-right:30px' src='images/trackcraze_logo.png'/></center>
<div id='index_strip'>
	<div id='index_top_strip'></div>
	<div style='position:absolute; top:403px; width:100%;'>
		<div id='index_divider' style='position:relative; background-color:#004F75; height:1px; border-top: 9px solid #005E8D; border-bottom: 1px solid #005E8D; '></div>
		<div style='background-color:#006699; width:100%; height:60px; border-bottom: 3px solid #004F75;'></div>
		<div style='background-color:rgba(0,0,0,0.30); width:100%; height:5px;'></div>
	</div>
</div>
<div id='index_container'>
	<center>
		<div style='margin:20px 0px 0px;'><p id='title'>trackCraze</p><p style='display:none' id='mini_title'>Log in</p></div>
		
		<div style='z-index:100; width:320px;'>
			<input id='name_enter' class='input' type='text' placeholder='Your Name' autocomplete='off' style='margin-bottom:12px; display:none' />
			<input id='email_enter' class='input' type='text' placeholder='Enter Email' autocomplete='off'/>
			<div id='email_button'></div>
			<input hidden id='password_enter' style='position:relative; top:-20px; margin-bottom:10px' class='input' type='text' placeholder='Password' autocomplete='off'/>
		</div></center>
</div>
<div style='position:absolute; top:420px; width:100%'>
	<center>
		<p id='tag_line' style='position:relative; margin-left:550px; top:-4px; white-space:nowrap;'>a simple workout tracking application</p>
		<p id='description' style='position:relative; top:50px;'></p>
	</center>
</div>


<div id="footer" style='position:absolute; bottom:10px; width:100%'>
	<center>
      	<div id="footer_items">
		<ul style='float:left'>
        	<li><div class='nav_button_disabled'>Meet the Team</div></li>
        	<li><a href="http://behindtheworkout.com" class="noUnderline"><div class='nav_button'>Our Blog</div></a></li>
        	<li><a href="/feedback.php" class='noUnderline'><div class='nav_button'>Feedback</div></a></li>
        	<li><a href="/terms.php" class='noUnderline'><div class='nav_button'>Terms of use</div></a></li>
        	<li><a href="/privacypolicy.php" class='noUnderline'><div class='nav_button'>Privacy policy</div></a></li>
        	<li><a href='/contact.php' class='noUnderline'><div class='nav_button'>Contact us</div></a></li>
      	</ul>
      	</div>
      </center>
</div>

<script type='text/javascript'>
	var page = 0;	//0 -> default, 1 -> login, 2 -> signup
	var expanded = false;
	
	var descriptions = ["Create custom workout programs", "Record your workouts", "Track your progress",  "Monitor your goals", "Post snapshots at the gym", "Connect with your friends"];
	var desc_counter = 0;
	$("#description").text(descriptions[0]);
	
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
			page = 1;
			
			if(!expanded){
				$("#index_logo").fadeOut();
				$("#mini_title").hide();
				$("#index_top_strip").animate({
						top: '200px',
						height: '230px'
					}, {
		    		"duration": 500,
		    		specialEasing: {
		    			top: 'easeOutBounce',
		      			height: 'easeOutBounce'
		    		},complete: function() {
				  		populateLoginInput();
		    		}
				})
				$("#index_container").animate({
						top: '200px'
					}, {
		    		"duration": 800,
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
	})
	
	function populateLoginInput(){
		$("#password_enter").fadeIn();
	    	$("#title").animate({
				right:'50px'
			}, {
				"duration": 700,
				specialEasing: {
  					right: 'easeOutBounce'
	 				}
			});
			$("#name_enter").slideUp(function(){
				$("#mini_title").text("Log in");
	 			m_left = $("#email_enter").position().left+280+'px';
				m_top = ($("#email_enter").position().top-85)+'px';
				$("#mini_title").css({left:m_left, top:m_top});
	  			$("#mini_title").fadeIn();
	  			$("#tag_line").text("Forgot password?");
	    		$("#tag_line").css({"visibility": "visible", "margin-left": "350px"});
	    	});
	    			
	    	$("#index_divider").animate({
			  	top: '0px'
			  }, {
			    "duration": 500,
			    specialEasing: {
			    	top: 'easeOutBounce',
			      	height: 'easeOutBounce'
			  	}
			 })
	}
		
	$("#signup").live("click",function(){
		if(page != 2){
			page = 2;
		  		
			if(!expanded){
				$("#index_logo").fadeOut();
				$("#mini_title").hide();
				$("#index_divider").animate({
		  			top: '60px'
		  		}, {
		    		"duration": 500,
		    		specialEasing: {
		    			top: 'easeOutBounce',
		      			height: 'easeOutBounce'
		  			}
		  		})
				$("#index_top_strip").animate({
						top: '200px',
						height: '230px'
					}, {
		    		"duration": 500,
		    		specialEasing: {
		    			top: 'easeOutBounce',
		      			height: 'easeOutBounce'
		    		},complete: function() {
		    			populateSignUpInput();
		    		}
				})
				$("#index_container").animate({
						top: '200px'
					}, {
		    		"duration": 800,
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
		    		"duration": 500,
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
	})
	
	function populateSignUpInput(){
		$("#password_enter").hide().fadeIn();
	    	$("#title").animate({
	    		right:'50px'
			}, {
	    		"duration": 700,
	    		specialEasing: {
	      			right: 'easeOutBounce'
	  			}
	  		});	  				
	  				
	  		$("#mini_title").text("Sign up");
	  		m_left = $("#email_enter").position().left+280+'px';
	  		m_top = ($("#email_enter").position().top-85)+'px';
	  		$("#mini_title").css({left:m_left, top:m_top});
	    	$("#mini_title").fadeIn();
	    	$("#name_enter").show();
	}
	
</script>