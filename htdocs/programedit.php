<?php
	include_once "common/base.php"; 
    $pageTitle = "Edit Program";
	include_once "common/header.php"; 
	echo "<div id='container'>";
	include_once 'inc/class.lists.inc.php';
	$program = new GymScheduleItems($db);
	if(isset($_GET['program'])):
	$UID = $program->loadUserID($_GET['program']);
	$PID = $_GET['program'];
	else:
	$UID = $_SESSION['UserID'];
	$PID = $program->loadMainProgramByUser();
	endif;
	if($UID==NULL):
	else:
	include_once "common/sidebar.php";
	endif;
	$OSID = $program->getDefaultProgram($UID);?>
<div id="mainNSB">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
            
<?php

if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])&&($_SESSION['UserID']==$UID))://PROBLEM HERE IS THAT USERID COULD BE NULL(VISITOR)AND ABLE TO VIEW PAGES THAT DON"T EXIST"
if($PID!=$OSID):
echo "<a id='moreprograms' href=\"programlists.php?user=".$UID."\">View More Programs</a>";
echo "<a id='editview' href=\"program.php?program=".$PID."\">View Program</a>";
list($LID, $URL, $order) = $program->loadProgramByUser($PID);    //ORDER IS WRONG BECAUSE IT IS READING PER FORMAT LIST. DONT NEED ORDER IF WE DONT ALLOW EDIT POSITION OF SPLITS IN MAIN PAGE
               
?>			
			<div id='popup' hidden><input type='text' class='searchexercise inputfields' value="" autocomplete='off' placeholder='Enter existing exercise'/><input type='button' id='popupsubmit' value='Add'/></div>
			<form action="db-interaction/lists.php" id="add-split" method="post"> 
				<a id='addsplittrigger'>Click to add a new Split</a>
				<input type='text' id='addsplittextbox' autocomplete='off'/>
				<div id='addsplitbuttons' >
					<input type='submit' id='addsplitsubmit' value='Add' />
					<input type='button' id='addsplitcancel' value='Cancel' />
				</div>
            <input type="hidden" id="current-list" name="current-list" value="<?php echo $LID; ?>" />
			<input type="hidden" id="new-list-item-position" name="new-list-item-position" value="<?php echo ++$order; ?>" />
			</form>
<br /><br />
            <div class="clear"></div>
 
            <div id="share-area">
                <p>Share your workout program with your friends! <a href="<?php echo $URL ?>.html">www.workoutplanner.com/<?php echo strtolower($Username)?></a></p>
            </div>
 
  <span></span>
  
            <script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
            <script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
            <script type="text/javascript" src="js/autocomplete/jquery.autocomplete.js"></script>
            <script type="text/javascript" src="js/lists.js"></script>
            <script type="text/javascript">      
             initialize();
            </script>
            <script src="js/grid.locale-en.js" type="text/javascript"></script>
			<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>
<?php
elseif($UID==NULL):                 
    echo "This program does not exist!";
else:
	echo "Don't try to edit your Default Program. This is built in and changing something may destroy your account. Stop messing with the URL";
	endif;
	else:
?>
                     Don't try to edit something that's not yours! Stop messing with the URL. I mean it.
            <!--<img src="/assets/images/newlist.jpg" alt="Your new list here!" />-->
                   
<?php endif; ?>
                                       
        </div>


<?php include_once "common/footer.php"; ?>