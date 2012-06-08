<?php
    include_once "common/base.php";
        $pageTitle = "Progress";
        include_once "common/header.php";
        echo "<div id='container'>";
		include_once "common/sidebar.php";
		include_once "common/rsidebar.php";
		include_once 'inc/class.users.inc.php';
   		$users = new GymScheduleUsers($db);
	?>
		<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
		<script type="text/javascript" src="js/progress.js"></script>

			
			
			
        <div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
<?php
        include_once 'inc/class.progress.inc.php';
   		$progress = new GSProgress($db);
	echo "<div id='progressTitle'><div id='progressLeft'>Progress</div>";
	if ($cuser==$_SESSION['UserID']):
	echo "<div id='progressRight'><p>|</p><a id='RecordSelect' disabled='1'>Record</a><a id='TrackSelect'>Track</a><a id='PhysiqueSelect'>Physique</a></div></div>";
	echo "<div id='trackoptions' hidden><a id='daySelect'>By Day</a><a id='exerciseSelect'>By Exercise</a></div>";//<a>Graphs and other shit</a></div>";
	echo "<div id='progressSelector'><p>Select Program</p><p>Select Split</p><p id='ExerciseSel' style='display:none'>Select Exercise</p></div>";
   		echo "<div id='inputLine'><div id='recordLine'>";
   		list($order) = $progress->loadProgramsOptionByUser();
   		echo "<div id='selectandbutton' hidden><input id='viewbyExercise' type=button class='small box' value='Filter'/></div>";
   		echo"</div><div display:'none' id='weightLine' hidden><p>New Weight</p><input id='newWeightInput' maxlength = '6' size='4' /><select class='lbkgselect'><option value='lbs'>lbs</option><option value='kg'>kg</option></select></div><input id='weightdate' maxlength='10' size='7'/><input id='recordsubmit' class='red mid box' type=button value='Record'/><input hidden id='weightsubmit' type=button value='Enter'/><div id='errordate'></div></div>";
   		echo "<div id='trackbyExercise' hidden><h3 hidden id='sortby'>Sort by: <select id='sortbyCol'><option value='RecordDate'>Date</option><option value='Weight'>Weight</option></select> <select id='sortbyDESCASC'><option value='desc'>Decreasing</option><option value='asc'>Increasing</option></select><input id='sortbyExercise' type=button class='small box' value='Sort'/><input id='printbyExercise' type=button class='orange small box' value='Export to Excel'/></h3><table id='byExerciseTable'></table><div id='pager'></div><div id='tablebyExercise' hidden><h3>General Information</h3></div></div>";//here!!!
   		echo "<div id='InputTable'>";
   		echo "</div>";	
   		echo "<div id='TrackTable' hidden></div>";
   		echo "<div id='PhyTable' hidden></div>";
   	else:	//not your own progress
   	$is_tracking = $users->trackingCheck($_SESSION['UserID'],$cuser);
   	list($weight_privacy, $progress_privacy) = $progress ->loadProgressPrivacy($cuser);
   	
   	if($progress_privacy==0 || ($progress_privacy == 1 && $is_tracking==2))
   		$progress_enabled = true;
   	else
	   	$progress_enabled = false;
	
	if($weight_privacy==0 || ($weight_privacy == 1 && $is_tracking==2))
   		$weight_enabled = true;
   	else
	   	$weight_enabled = false;
	  
   	echo "<div id='progressRight'><p>|</p>";
   	if($progress_enabled){
   		echo "<a id='TrackSelect' disabled='1'>Track</a>";
   	}
   	if($weight_enabled){
   		echo "<a id='PhysiqueSelect'>Physique</a>";
   	}
   	echo "</div></div>";
   	
   	if($progress_enabled):
	//echo "<div id='progressSelector'><p>Select Program</p><p>Select Split</p></div>";
   	echo "<div id='trackoptions' ><a id='daySelect'>By Day</a></div>";//<a id='exerciseSelect'>By Exercise</a><a>Graphs and other shit</a></div>";	
	//echo "<div id='inputLine'><input id='weightdate' display:none maxlength='10' size='7'/><input id='weightsubmit' type=button value='Enter' hidden/><div id='errordate'></div></div>";
   		echo "<div id='TrackTable'></div>"; 
   		
   		?><script>
   				today = new Date();
		if (today.getMonth()+1<10){
    		month = "0"+eval(today.getMonth()+1);
    	} else {
    		month = today.getMonth()+1;
    	}
    	if (today.getDate()<10){
    		day = "0"+today.getDate();
    		}else{
    			day = today.getDate();
    		}
  		stoday =  today.getFullYear()+"-"+month+"-"+day;
  		loadRecordsByDate('<?php echo $cuser ?>',stoday);
				calgen();
				currentMonth=today.getMonth()+1;
    			currentYear=today.getFullYear();
				populateFields(today.getMonth()+1, today.getFullYear(), '<?php echo $cuser?>');
  			 </script><?php
  		elseif($weight_enabled):
  			echo "<div id='PhyTable'></div>";
  		else:
  			echo "<br /><br /><br /><h3>This user has chosen to hide his/her progress.. Probably slacking off</h3>";
  		endif; //weight_enable
   endif;
   //echo "<div id='calHover'></div>";
   		//$progress->loadInputExercise();
?>
<br /><br />
<br /><br />
<br /><br />



			 <input type="hidden" id="current-id" value="<?php echo $_SESSION['UserID']; ?>" /><!--ENCRYPT THIS-->
</div>
		 <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
			 <script language="javascript" type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
			 <script language="javascript" type="text/javascript" src="js/jqplot/jqplot.trendline.min.js"></script>
			 <script language="javascript" type="text/javascript" src="js/jqplot/jqplot.dateAxisRenderer.min.js"></script>
			  <script language="javascript" type="text/javascript" src="js/jqplot/jqplot.highlighter.min.js"></script>
			  <script language="javascript" type="text/javascript" src="js/jquery.maskedinput.min.js"></script>			  
			<link rel="stylesheet" type="text/css" href="js/jqplot/jquery.jqplot.css" />
            <script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
             <script language="javascript" src="js/datepicker/js/datepicker.js" type="text/javascript"></script>
            <script type="text/javascript">
 				initializeProgress('<?php echo $cuser?>', '<?php echo $_SESSION["UserID"]?>'); //Need a better way so people cannot edit this
            </script>
            <link rel="stylesheet" type="text/css" media="screen" href="js/datepicker/css/datepicker.css" />
            <script src="js/jqgrid/grid.locale-en.js" type="text/javascript"></script>
			<script src="js/jqgrid/jquery.jqGrid.min.js" type="text/javascript"></script>
			<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/ui.jqgrid.css" />
			<link rel="stylesheet" type="text/css" media="screen" href="js/jquery-ui-1.8.16.custom.css" />
<?php
	
	//This has to go here after the javascript. Tried moving the javascript earlier but the weight date thing messes up. Duplicate code.
  	list($weight_privacy, $progress_privacy) = $progress ->loadProgressPrivacy($cuser);
   	$is_tracking = $users->trackingCheck($_SESSION['UserID'],$cuser);
   	if($progress_privacy==0 || ($progress_privacy == 1 && $is_tracking==2))
   		$progress_enabled = true;
   	else
	   	$progress_enabled = false;
	
	if($weight_privacy==0 || ($weight_privacy == 1 && $is_tracking==2))
   		$weight_enabled = true;
   	else
	   	$weight_enabled = false;
	if(!$progress_enabled && $weight_enabled):?>
  		<script>$("#PhysiqueSelect").click();</script>
  	<?php endif; ?>


<?php
    include_once "common/footer.php";
?>