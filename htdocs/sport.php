<?php
	include_once "common/base.php"; 
    $pageTitle = "Exercise";//Change to the name of the person?
	include_once "common/header.php"; 
	echo "<div id='container'>";
	//include_once "/common/sidebar.php";
	//include_once "common/rsidebar.php";
	?>
<div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
            
<?php
if(isset($_GET['sport'])):

include_once 'inc/class.news.inc.php';
include_once 'inc/class.users.inc.php';

$users = new GymScheduleUsers($db);
if($_GET['sport']==0):
echo "<p class='h3'>Other fun activities that people enjoy</p><table border='0'><tr><td></td><td></td></tr>";
list ($sport, $search) = $users->getEasterSportEgg();//RANDOM:???????????????????????
$placeholder = 'Search a fun activity';
echo "<input type='text' class='searchsport' value='' autocomplete='off' placeholder='$placeholder'/><div class='search sp'></div><div class='errortrack'></div>";
echo "<br /><br /><br />";
echo $sport;
echo "</table></div>";
else:

echo "<p class='h3'>";
$sName = $users->getSportName($_GET['sport']);
if ($sName == NULL):
if ($_GET['sport']==1337):
echo "This website is founded by Alex Yau, Timothy Tse, Derek Mok and Martin So. This, my friend, is an Easter Egg. <img style='float:right' src='images/egg.jpg'> <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />Oh, and also...";
endif;
echo "There is no such sport.";
die();
else:
list($string, $search)=$users->getSport($_GET['sport']);
echo $sName." (".count($search)." people enjoy doing this)</p><table border='0'><tr><td>List of people who enjoys this sport</td><td></td></tr>";
$placeholder = 'Search who enjoys this sport';
echo "<input type='text' class='searchsport' value='' autocomplete='off' placeholder='$placeholder'/><div class='search sp'></div><div class='errortrack'></div>";
echo "<br /><br /><br />";
echo $string;
echo "</table></div>";
endif;
endif;             
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
           $(".searchsport").autocomplete({
           		data:<?php echo json_encode($search)?>           		
       		});
       		var search = <?php echo json_encode($search)?>;
       		$(".search").live("click",function(){
       			var found=false;//people with the same name CHEKCKKKKKKKKKKKKKKKKKKKKKK
       			for(i=0;i<search.length;i++){
       				if($(this).prev().val()==search[i][0]){
       					found=true;
       					window.location.replace("board.php?user="+search[i][1]);
   					}
   				}
   				if (found==false){
   					$(".errortrack").show();
   					$(".errortrack").text("This person does not enjoy "+"<?php echo $users->getSportName($_GET['sport'])?>");
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
                   
<?php else:
die();
endif; ?>
                                       
        </div>
		

<?php 
include_once "common/footer.php"; ?>