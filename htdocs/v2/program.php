<?php
require 'common/base.php';
require 'model/class.user.php';
$user = new User();

require 'utils/facebook/facebook.php';

$facebook = new Facebook(array(
	'appId'  => FB_APID,
	'secret' => FB_SECRET,
));

$fb_user = $facebook->getUser();

$me = $user -> getME($fb_user);

if(isset($me)){
	$profile_pic = $me['profile_pic'];
	$name = $me['first_name']." ".$me['last_name'];
}else{
	$profile_pic = "images/alex_pic.jpg";
	$name = "Alex Yau";
}
?>
<?php 
	$pageTitle = $name;
	include_once "common/header.php";
?>

<div id='container' style='top:200px'>
	<nav id='nav_left'>
		<div><img src='images/profile.png'/><p>Profile</p></div>
		<div><img src='images/record.png'/><p>Record</p></div>
		<div><img src='images/diary.png'/><p>Diary</p></div>
		<div><p>Tracking: 45</p></div>
		<div><p>Tracked by: 52</p></div>
		<div class='selected'><p>Summer 2012 workout program</p></div>
		<div><p>Thor workout</p></div>
		<div><img src='images/addprogram.png'/><p>New Program</p></div>
	</nav>
	
	<section id='content'>
		<section id='profile_header' style='background: rgba(0,0,0,0.02); width:100%; height:100px; overflow:hidden'>
		<img src="<?php echo $profile_pic?>" style='height:100px; width:100px; float:left;' class='medium_pic'/>
		<div style='float:left; margin-left:10px'>
			<h3 style='padding:10px 0px; margin:0px' class='text-shadow'><?php echo $name?></h3>
			<!--<div style='float:left; position:absolute; top:15px; left:400px;' class='btn'>Track</div>-->
			<p>CEO and Co-Founder of trackCraze. Casual gym goer</p>
			<div id='profile_info' style='margin-top:10px'>
				<img src='images/birthday.png' style='float:left; width:15px;position:relative; top:-3px; opacity:0.7'/><p style='float:left; margin-left:10px'>03/12/91</p>
			</div>
		</div>
	</section>
		<!--<div style='float:left; position:absolute; top:125px; right:30px;' class='btn'>Edit</div>-->
		<p style='font-size:28px;margin:10px 0px 10px 20px' class='text-shadow'>Summer 2012 workout program</p>
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

<?php include_once "common/footer.php" ?>
