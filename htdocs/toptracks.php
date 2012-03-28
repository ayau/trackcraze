<?php
	include_once "common/base.php"; 
    $pageTitle = "Tracking";//Change to the name of the person?
	include_once "common/header.php"; 
	echo "<div id='container'>";
	include_once "common/sidebar.php";
	include_once "common/rsidebar.php";?>
<div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
           
            
<?php
if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])):

include_once 'inc/class.news.inc.php';
include_once 'inc/class.users.inc.php';

$users = new GymScheduleUsers($db);
echo "<p class='h3'>Top Tracks</p>";
echo $users->getTopTracks($cuser);

echo "</table></div>";
             
?>			
            <div class="clear"></div>
 
            <div id="share-area">

            </div>
 
  <span></span>
            <script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
            <script type="text/javascript">
          
            </script>

                   
<?php endif; ?>
                                       
        </div>
		

<?php 
include_once "common/footer.php"; ?>