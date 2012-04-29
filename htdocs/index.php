<html style='overflow:hidden'>
<title>trackCraze</title>
<meta name='KEYWORDS' content='trackcraze,fitness,tracking,gym,workout,log,exercise,record,share'>
<meta name='Description' content="Trackcraze lets you follow other people's workout, record your own, and share it with others. Track your friends, your idol, and most importantly, yourself. Make fitness simple, customizable and motivating.">
<link rel="stylesheet" href="/style.css" type="text/css" />
<head><meta name="google-site-verification" content="ZsfdLKxUCoT4ItYyO2KQNsM1kx8IqP8bE8ZB_su4h_4" /></head>
<body id='index'>
<link rel="shortcut icon"  href="images/favicon.ico" type="images/favicon.ico" />
<center id='trackcrazelogo'><a href='login.php'><img src='images/trackcrazelogo.png'></a></center>

<a href='login.php'><img src='images/indexlogo.png'></a>
	<h2>Coming Soon</h2>
	<br />
	<div class='box1'>
		<p class='indextext4'>A simple workout tracking application.</p>
	<p class='indextext'>Connect with your friends, share your goals and post snapshots at the gym.</p>
</div> <br /> <p class='indextext2'>Join our exclusive e-mail list </p><p class='indextext3'>today to participate in our alpha version test run.</p>
<input id='emailenter' class='inputfields' type='text' placeholder='Enter Email' autocomplete='off'/>
<div class='indexbg'></div>
<div id='emailbutton'></div>
<p id='emailerror'></p>
 <script type='text/javascript' src='js/jquery.min.js'></script>
<script>
$('#emailenter').keypress(function(e){
    if (e.which == 13) {
    	addemail();
    }
});
$("#emailbutton").live("click",function(){
	addemail();
});
 function addemail(){
    	 var exp = /^[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[A-Za-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)\b$/
    	if($("#emailenter").val().match(exp)){
    		$.ajax({
       						type: "POST",
       						url: "db-interaction/users.php",
       						data: "action=addemail&email="+$("#emailenter").val(),
       						success: function(r){
       							$("#emailerror").text(r).show();
    							t = setTimeout(function(){$("#emailerror").fadeOut(1000)},5000);
       						},
      						error: function(){
       						}
      					});
    	}
    	else if($("#emailenter").val()){
    		$.ajax({
       						type: "POST",
       						url: "db-interaction/users.php",
       						data: "action=addfailemail&email="+$("#emailenter").val(),
       						success: function(){
       							$("#emailerror").text("This email address is problematic.").show();
    							t = setTimeout(function(){$("#emailerror").fadeOut(1000)},5000);
       						},
      						error: function(){
       						}
      					});
    	}
 }
</script>
</body>
</html>