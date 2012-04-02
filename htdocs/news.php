<?php
	include_once "common/base.php"; 
    $pageTitle = "Updates";
	include_once "common/header.php"; 
	echo "<div id='container'>";
	include_once "common/sidebar.php";
	include_once "common/rsidebar.php";?>
<div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
            
<?php
if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])):

include_once 'inc/class.news.inc.php';

$news = new GSNews($db);
echo "<div id='news'><h2>News & Updates</h2><br />";
$news->getNews();
echo "</div>";
             
?>			
			
            <div class="clear"></div>
 
            <div id="share-area">

            </div>
 
  <span></span>
            <script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
            <script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
            <script type="text/javascript" src="js/autogrow.js"></script>
            <script type="text/javascript" src="js/charcounter.js"></script>
            <script type="text/javascript" src="js/news.js"></script>
            <script type="text/javascript">
            initializeNews();
            </script>
            <script src="js/grid.locale-en.js" type="text/javascript"></script>
			<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>
<?php
/*elseif(isset($_GET['list'])):                 
    echo "ttt<ul id='list'>n";
 
    include_once '/inc/class.lists.inc.php';
    $lists = new GymScheduleItems($db);
    list($LID, $URL) = $lists->loadListItemsByProgramID();
 
    echo "\t\t\t</ul>";
    else:
    */

?>
                    
            <!--<img src="/assets/images/newlist.jpg" alt="Your new list here!" />-->
                   
<?php endif; ?>
                                       
        </div>
		

<?php 
include_once "common/footer.php"; ?>