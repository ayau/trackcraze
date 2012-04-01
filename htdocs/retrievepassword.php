<?php
	include_once "common/base.php";    
	$pageTitle = "Retrieve Password";
	include_once "common/header.php";
 
    if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['UserID'])):
 		echo "<meta HTTP-EQUIV='REFRESH' content='0; /board.php'>";
    else:
        include_once 'inc/class.users.inc.php';
        $users = new GymScheduleUsers($db);
?>
DERRRPPPPP DOESNT WORK YET!!!!!
<?php
    endif;
    include_once 'common/footer.php';
?>