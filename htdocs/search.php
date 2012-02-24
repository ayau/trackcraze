<?php
include_once "common/base.php";
$pageTitle = "Search";
include_once "common/header.php";
include_once 'inc/class.news.inc.php';
include_once 'inc/class.users.inc.php';
echo "<div id='container'>";
	?>
<div id="main">
            <noscript>This site just doesn't work, period, without JavaScript</noscript>
<?php
if(isset($_GET['search'])):
$item = urldecode($_GET['search']);
echo "<h3>Search results for: ".$item."</h3>";
echo "<br />";

$users = new GymScheduleUsers($db);
echo "<table>";
echo $users->search($item);
echo "</table>";
?>

 <script type='text/javascript' src='/js/jquery.min.js'></script>
 <script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
    	<?php
    endif;
?>