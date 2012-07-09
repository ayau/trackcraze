<!DOCTYPE html>
<html style='background: url("images/blur_bg.png") no-repeat center center fixed;'>

<head>
<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<![endif]-->

	<title><?php echo $pageTitle ?></title>
	<meta name='KEYWORDS' content='trackcraze,fitness,tracking,gym,workout,log,exercise,record,share'/>
	<meta name='Description' content="Trackcraze lets you follow other people's workout, record your own, and share it with others. Track your friends, your idol, and most importantly, yourself. Make fitness simple, customizable and motivating."/>

	<meta charset="UTF-8">
	<link rel="stylesheet" href="style.css" type="text/css" />
	<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js?ver=1.3.2'></script>
	<script type='text/javascript' src='js/jquery.min.js'></script> 
</head>


<body style='margin:0px'>
<div style='background: rgba(0,0,0,0.3); height:160px; width:100%'>
		<center style='position:relative; top:10px'>
			<a style='text-decoration:none' href='program.php'><p id='header_title' style='margin-left:30px'>trackCraze</p><p class='text-shadow' style='display:inline; color:white; font-size:12px'>BETA</p></a>
			<input id='search_enter' class='input' type='text' placeholder='Search trackCraze' autocomplete='off' style='display:block'/>
		<a onclick='FB.logout()' class='header_link' style='position:relative; top:-120px; left:250px;'>Log out</a>
	</div>
	<div id="fb-root"></div>
	<script type='text/javascript'>
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>', 
          cookie: true, 
          xfbml: true,
          oauth: true
        });

        FB.Event.subscribe('auth.logout', function(response) {
        	//destroy session
        	$.ajax({
	    			type: "POST",
	    			url: "db-interaction/user.php",
	    			data: {
	    				"action":"logout"
					},	
	    			success: function(){
	    				window.location = "index.php";
	    			},
	    			error: function(){
	    			    // should be some error functionality here
	    			}
	    		});   
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());

</script>
