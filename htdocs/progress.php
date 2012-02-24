<?php
    include_once "common/base.php";
        $pageTitle = "Progress";
        include_once "common/header.php";
        echo "<div id='container'>";
		include_once "common/sidebar.php";
		include_once "common/rsidebar.php";
		
	?>
		<script type="text/javascript" src="js/progress.js"></script>
        <div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
<?php
        include_once 'inc/class.progress.inc.php';
   		$progress = new GSProgress($db);
	echo "<div id='progressTitle'><div id='progressLeft'>Progress</div>";
	if ($cuser==$_SESSION['UserID']):
	echo "<div id='progressRight'><p>|</p><a id='RecordSelect' disabled='1'>Record</a><a id='TrackSelect'>Track</a><a id='PhysiqueSelect'>Physique</a></div></div>";
	echo "<div id='trackoptions' hidden><a id='daySelect'>By Day</a><a id='exerciseSelect'>By Exercise</a><a>Graphs and other shit</a></div>";
	echo "<div id='progressSelector'><p>Select Program</p><p>Select Split</p><p id='ExerciseSel' style='display:none'>Select Exercise</p></div>";
   		echo "<div id='inputLine'><div id='recordLine'>";
   		list($order) = $progress->loadProgramsOptionByUser();
   		echo "<div id='selectandbutton' hidden><input id='viewbyExercise' type=button value='Filter'/></div>";
   		echo"</div><div display:'none' id='weightLine' hidden><p>New Weight</p><input id='newWeightInput' maxlength = '4' size='4' /><select class='lbkgselect'><option value='lbs'>lbs</option><option value='kg'>kg</option></select></div><input id='weightdate' maxlength='10' size='7'/><input id='recordsubmit' type=button value='Record'/><input hidden id='weightsubmit' type=button value='Enter'/><div id='errordate'></div></div>";
   		echo "<div id='trackbyExercise' hidden><h3 hidden id='sortby'>Sort by: <select id='sortbyCol'><option value='RecordDate'>Date</option><option value='Weight'>Weight</option></select> <select id='sortbyDESCASC'><option value='desc'>Decreasing</option><option value='asc'>Increasing</option></select><input id='sortbyExercise' type=button value='Sort'/><input id='printbyExercise' type=button value='Export to Excel'/></h3><table id='byExerciseTable'></table><div id='pager'></div><div id='tablebyExercise' hidden><h3>General Information</h3></div></div>";//here!!!
   		echo "<div id='InputTable'>";
   		echo "</div>";	
   		echo "<div id='TrackTable' hidden></div>";
   		echo "<div id='PhyTable' hidden></div>";
   	else://DOESN"T WORK YET. NEED TO PUT IN THE INPUT TABLE, PHY TABLE AND TRACKTABLE" and TRACKBYEXERCISE
   	echo "<div id='progressRight'><p>|</p><a id='TrackSelect' disabled='1'>Track</a><a id='PhysiqueSelect'>Physique</a></div></div>";
	//echo "<div id='progressSelector'><p>Select Program</p><p>Select Split</p></div>";
   	echo "<div id='trackoptions' ><a id='daySelect'>By Day</a><a id='exerciseSelect'>By Exercise</a><a>Graphs and other shit</a></div>";	
	//echo "<div id='inputLine'><input id='weightdate' display:none maxlength='10' size='7'/><input id='weightsubmit' type=button value='Enter' hidden/><div id='errordate'></div></div>";
   		echo "<div id='TrackTable'></div>"; 
   		echo "<div id='PhyTable'></div>";
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
   endif;
   		//$progress->loadInputExercise();
?>
<br /><br />
<br /><br />
<br /><br />


			 <input type="hidden" id="current-id" value="<?php echo $_SESSION['UserID']; ?>" /><!--ENCRYPT THIS-->
			 <!--[if lt IE 9]><script language="javascript" type="text/javascript" src="excanvas.js"></script><![endif]-->
			 <script language="javascript" type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
			 <script language="javascript" type="text/javascript" src="js/jqplot/jqplot.trendline.min.js"></script>
			 <script language="javascript" type="text/javascript" src="js/jqplot/jqplot.dateAxisRenderer.min.js"></script>
			  <script language="javascript" type="text/javascript" src="js/jqplot/jqplot.highlighter.min.js"></script>
			  <script language="javascript" type="text/javascript" src="js/jquery.maskedinput.min.js"></script>			  
			<link rel="stylesheet" type="text/css" href="js/jqplot/jquery.jqplot.css" />
			<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
            <script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
             <script language="javascript" src="js/datepicker/js/datepicker.js" type="text/javascript"></script>
            <script type="text/javascript">
 				initializeProgress('<?php echo $cuser?>');
            </script>
            <link rel="stylesheet" type="text/css" media="screen" href="js/datepicker/css/datepicker.css" />
            <script src="js/jqgrid/grid.locale-en.js" type="text/javascript"></script>
			<script src="js/jqgrid/jquery.jqGrid.min.js" type="text/javascript"></script>
			<link rel="stylesheet" type="text/css" media="screen" href="js/jqgrid/ui.jqgrid.css" />
			<link rel="stylesheet" type="text/css" media="screen" href="js/jquery-ui-1.8.16.custom.css" />
</div>

<?php
  //  else:
  //      header("Location: ");
  //      exit;
  //  endif;
?>
<?php
    include_once "common/footer.php";
?>