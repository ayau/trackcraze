<?php
    include_once "common/base.php";
        $pageTitle = "Photos";
        include_once "common/header.php";
        echo "<div id='container'>";
		if(isset($_GET['user'])):
			$UID = $_GET['user'];
		else:
			$UID = $_SESSION['UserID'];
		endif;
		include_once "common/sidebar.php";
		include_once "common/rsidebar.php";?>
        <div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
<?php
	if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']==1):
		include_once 'inc/class.news.inc.php';
		$news = new GSNews($db);
		echo "<h2>Profile pics</h2>";
		echo "<div id='gallery'>";
		$news->getPhotos($cuser);
		echo "</div>";
?>
<br /><br />




<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
            <script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
            <script type="text/javascript" src="js/lists.js"></script>
            <script type="text/javascript">      
            
            </script>
            <script src="js/grid.locale-en.js" type="text/javascript"></script>
			<script src="js/jquery.jqGrid.min.js" type="text/javascript"></script>
</div>
<?php
    else:
        echo"You do not have permission to view this page. Unpopular much?";
    endif;
?>
<?php
    include_once "common/footer.php";
?>