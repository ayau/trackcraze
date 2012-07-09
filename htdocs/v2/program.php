<?php
require 'common/base.php';
require 'model/class.user.php';
$user = new User();

require 'model/class.program.php';
$program = new Program();

require 'utils/facebook/facebook.php';

$facebook = new Facebook(array(
	'appId'  => FB_APID,
	'secret' => FB_SECRET,
));

//$fb_user = $facebook->getUser();
//$_SESSION['loggedin'] = true;
//$_SESSION['uid'] = 3;

if(isset($_SESSION['loggedin'], $_SESSION['uid']) && $_SESSION['loggedin'] === true && $_SESSION['uid'] != 0){
	$me = $user -> getME($_SESSION['uid']);
}

//getting program_id and user_id
if(isset($_GET['id']) && $_GET['id'] > 0):
	$program_id = $_GET['id'];
	$user_id = $program -> getOwner($program_id);
elseif(isset($_GET['uid']) && $_GET['uid']>0):
	$program_id = $user -> getMainProgram($_GET['uid']);
	$user_id = $_GET['uid'];
elseif(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['uid']) && $_SESSION['uid'] != 0):
	$program_id = $user -> getMainProgram($_SESSION['uid']);
	$user_id = $_SESSION['uid'];
else:
	header ("Location: index.php");
endif;

$profile = $user -> getProfile($user_id);

//Redirects if pid or uid does not exist
if($profile == null)
	header("Location: index.php");

$main_program = $user -> getMainProgram($user_id);

$isOwner = ($_SESSION['uid'] == $user_id);

$profile_pic = $profile['profile_pic'];
$name = $profile['first_name']." ".$profile['last_name'];

?>
<?php 
	$pageTitle = $name;
	include_once "common/header.php";
?>

<div id='container'>
	<nav id='nav_left'>
		<div><a><img src='images/profile.png'/><p>Profile</p></a></div>
		<div><a><img src='images/record.png'/><p>Record</p></a></div>
		<div><a><img src='images/diary.png'/><p>Diary</p></a></div>
		<div><a><p>Tracking: 45</p></a></div>
		<div><a><p>Tracked by: 52</p></a></div>
<?php
	$programs = $program -> getPrograms($user_id);
	for($i = 0; $i < count($programs); $i++){
		$p = $programs[$i];
		echo "<div pid='".$p['id']."'";
		if($p['id'] === $program_id)
			echo " class='selected'";
		echo "><span class='";
		if($isOwner)
			echo "star";
		if($main_program === $p['id'])
			echo " program_select";
		echo "'></span><a href='program.php?id=".$p['id']."'><p>".$p['name']."</p></a></div>";
	}
?>
	<?php if($isOwner):?>
		<div id='new_program'><img src='images/addprogram.png'/><p>New Program</p>
			<textarea id='new_program_name' class='input' type='text' hidden placeholder='program name'></textarea>
			<p id='new_program_create' hidden style='margin:5px; float:left' type='button' value='create'>create</p>
		</div>
	<?php endif;?>
	</nav>

	<section id='content'>
		<section id='profile_header' style='background: rgba(0,0,0,0.02); width:100%; height:100px; overflow:hidden'>
		<img src="<?php echo $profile_pic?>" style='height:100px; width:100px; float:left;' class='medium_pic'/>
		<div style='float:left; margin-left:10px'>
			<h3 style='padding:10px 0px; margin:0px; text-shadow:none' class='text-shadow'><?php echo $name?></h3>
			<!--<div style='float:left; position:absolute; top:15px; left:400px;' class='btn'>Track</div>-->
			<p>CEO and Co-Founder of trackCraze. Casual gym goer</p>
			<div id='profile_info' style='margin-top:10px'>
				<img src='images/birthday.png' style='float:left; width:15px;position:relative; top:-3px; opacity:0.7'/><p style='float:left; margin-left:10px'>03/12/91</p>
			</div>
		</div>
		</section>

<?php $p = $program -> getProgram($program_id);?>
		<!--<div style='float:left; position:absolute; top:125px; right:30px;' class='btn'>Edit</div>-->
		<p style='font-size:28px;margin:10px 0px 10px 20px' class='text-shadow'><?php echo $p['name']?></p>
		<article style='margin-top:20px'>
			<p style='font-size:24px;margin:10px 0px 10px 20px'>Chest</p>
			<table class='workout_table'>
				<tr><td class='workout_exercise' rowspan='2'>Incline bench press</td><td class='workout_set'>150lbs x 3 (2 sets)</td><td rowspan='2' class='workout_comment'>Can definitely increase weight next time</td></tr>
				<tr><td>160lbs x 3 (1 set)</td></tr>
				<tr><td style='padding-top:10px'></td></tr>
				<tr><td>Flat bench press</td><td>175lbs x 3 (3 sets)</td><td></td></tr>
				<tr><td style='padding-top:10px'></td></tr>
				<tr><td>Barbell row</td><td>180lbs x 5 (3 sets)</td><td></td></tr>
				<tr><td style='padding-top:10px'></td></tr>
				<tr><td class='workout_exercise' rowspan='2'>Weighted pull ups</td><td class='workout_set'>60lbs x 8 (2 sets)</td><td rowspan='2' class='workout_comment'></td></tr>
				<tr><td>45lbs x 7 (1 set)</td></tr>
			</table>
		</article>
		<article style='margin-top:20px'>
			<p style='font-size:24px;margin:10px 0px 10px 20px'>Shoulder</p>
			<table class='workout_table'>
				<tr><td>Seated shoulder press</td><td>105lbs x 6 (3 sets)</td><td></td></tr>
				<tr><td style='padding-top:10px'></td></tr>
				<tr><td>Dumbbell press</td><td>45lbs x 6 (2 sets)</td><td></td></tr>
				<tr><td style='padding-top:10px'></td></tr>
				<tr><td class='workout_exercise' rowspan='2'>Lateral raises</td><td class='workout_set'>25lbs x 6 (2 sets)</td><td rowspan='2' class='workout_comment'>Hurt my wrist during this exercise</td></tr>
				<tr><td>10lbs x 5 (1 set)</td></tr>
				<tr><td style='padding-top:10px'></td></tr>
				<tr><td class='workout_exercise' rowspan='1'>Barbell front raise</td><td class='workout_set'>50lbs x 6 (2 sets)</td><td rowspan='1' class='workout_comment'></td></tr>
			</table>
		</article>
	</section>

</div>
<?php if($isOwner):?>
<div id='button_container'>
	<div id='program_delete'></div>
	<div id='program_edit'></div>
</div>
<?php endif;?>
<?php include_once "common/footer.php" ?>

<script type='text/javascript'>

$(document).ready(function () {
	var program_name = "<?php echo $p['name'] ?>";
	var program_id = parseInt("<?php echo $p['id']?>");
	var create_disabled = false;

	$("#new_program").live("click",function(){
		if(!$(this).hasClass('selected')){
			$(this).addClass("selected");
			$(this).animate({
				height: '110px'
				}, {
				 	"duration": 400,
				    specialEasing: {
				    	top: 'easeOutBounce'
					}, complete:function(){
				  		$(this).find("#new_program_name").fadeIn();
				  		$("#new_program_create").fadeIn();
				  	}
			})
		}
	})

	$("#new_program_create").live("click",function(){
		if(!create_disabled){
			create_disabled = true;
			new_program = $("#new_program");

			name = $("#new_program_name").val();
			name = name.trim();
			if(name.split(' ').join('').length > 0){
				$.ajax({
			    	type: "POST",
			    	url: "db-interaction/program.php",
			    	data: {
			    		"action":"createProgram",
			    		"name":name
		    		},
					success: function(r){
			    		if(r!= null && r != 0)
			    			window.location = "program.php?id="+r;
			    		else{
			    			alert("Something went wrong. Try again?");
			    			create_disabled = false;
			    		}
			    	},
			    	error: function(){
			    		alert("Something went wrong. Try again?");
			    		create_disabled = false;
			    	}
				})
			}else{
				$("#new_program_name").val('');
				new_program.find("#new_program_name").hide();
				$("#new_program_create").hide();
				new_program.animate({
					height: '21px'
					}, {
						"duration": 400,
						specialEasing: {
						    top: 'easeOutBounce'
						}, complete:function(){
							new_program.removeClass("selected");
						}
				})
				create_disabled = false;
			}
		}
	});

	$(".star").live("click",function(){
		if(!$(this).hasClass("program_select")){
			$(".program_select").removeClass("program_select");
			$(this).addClass("program_select");
			pid = $(this).parent().attr("pid");

			$.ajax({
			    	type: "POST",
			    	url: "db-interaction/user.php",
			    	data: {
			    		"action":"setMainProgram",
			    		"pid": pid
		    		},
					success: function(){
			    	},
			    	error: function(){
			    	}
			})
		}
	})


	//scrolling of the button container
	var top = $('#button_container').position().top;
	$(window).scroll(function (event) {
	    var y = $(this).scrollTop();
	  
	    if (y >= top - 10) 
	      $('#button_container').addClass('fixed');
	    else
	      $('#button_container').removeClass('fixed');
  	});

	$("#content").mouseout(function(){
      $("#button_container").addClass('hidden');
    }).mouseover(function(){
       $("#button_container").removeClass('hidden');
    });

    $("#button_container").mouseout(function(){
      $("#button_container").addClass('hidden');
    }).mouseover(function(){
       $("#button_container").removeClass('hidden');
    });

    //Make the content stretch to fit 
    $("#content").height($("#nav_left").height()+80);


    $("#program_delete").live("click", function(){
    	var r = confirm("Do you really want to delete "+program_name+"?");
	    if( r == true ){
			$.ajax({
			    	type: "POST",
			    	url: "db-interaction/program.php",
			    	data: {
			    		"action":"deleteProgram",
			    		"pid": program_id
		    		},
					success: function(r){
						if(r == true){
							window.location = "program.php";
						}
			    	},
			    	error: function(){
			    	}
			})
	    }
    })
});
</script> 