<?php
	include_once "common/base.php"; 
    $pageTitle = "Trackers";//Change to the name of the person?
	include_once "common/header.php"; 
	echo "<div id='container'>";
	include_once "common/sidebar.php";
	include_once "common/rsidebar.php";?>
<div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
            <input type='text' class='searchtracki' value="" autocomplete='off' placeholder='Search your trackers'/><div id='searchtrack' class='search sp'></div><div class='errortrack'></div>
            <br /><br /><br />
<?php
if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])):

include_once 'inc/class.news.inc.php';
include_once 'inc/class.users.inc.php';

$users = new GymScheduleUsers($db);
echo "<p class='h3'>Trackers</p><table border='0'><tr><td></td></tr>";
list($string, $search)=$users->getTrackers($cuser);
echo $string;

echo "</table></div>";
             
?>			
            <div class="clear"></div>
 
            <div id="share-area">

            </div>
 
  <span></span>
            <script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
   			<script type="text/javascript" src="js/autocomplete/jquery.autocomplete.js"></script>
            <script type="text/javascript" src="js/news.js"></script>
            <script type="text/javascript">
            initializeNews();
           $(".searchtracki").autocomplete({
           		data:<?php echo json_encode($search)?>           		
       		});
       		var search = <?php echo json_encode($search)?>;
       		$("#searchtrack").live("click",function(){
       			var found=false;
       			for(i=0;i<search.length;i++){
       				if($(this).prev().val()==search[i][0]){
       					found=true;
       					window.location.replace("board.php?user="+search[i][1]);
   					}
   				}
   				if (found==false){
   					$(".errortrack").show();
   					$(".errortrack").text("This person is not in your trackers list");
   					t = setTimeout(function(){$(".errortrack").fadeOut(1000)},3000);
   				}
       		})
            </script>
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