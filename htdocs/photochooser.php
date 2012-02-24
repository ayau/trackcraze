<?php
    include_once "common/base.php";
        echo "<div id='container'>";
		if(isset($_GET['user'])):
			$UID = $_GET['user'];
		else:
			$UID = $_SESSION['UserID'];
		endif;

	if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']==1):
		include_once '/inc/class.news.inc.php';
		$news = new GSNews($db);
		echo "<h2>Profile pics</h2>";
		echo "<div id='photochooser'>";
		$news->getchoosePhotos($UID);
		echo "</div>";
?>
<br /><br />

<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
            <script type="text/javascript">      
            	$(".photo").live("click",function(){
            		$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    					"action":"changeProfilePic",
    					"pid":$(this).attr('id')
    				},
    			success: function(){
    				 tb_remove();
    				 window.location.reload(true);//change picture so no need to reload
    				},
    			error: function() {
    			}
    		});
            	})
            </script>
</div>
<?php
    else:
        echo"What are you trying to do...";
    endif;
?>
<?php
    include_once "common/footer.php";
?>