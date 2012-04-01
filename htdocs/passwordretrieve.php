<?php
	include_once "common/base.php";    
	$pageTitle = "Thank You";
	include_once "common/header.php";
 
    if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['UserID'])):
 		echo "<meta HTTP-EQUIV='REFRESH' content='0; /board.php'>";
    else:
?>
<br />
An email will be sent to your address shortly with instructions to retrieve your password
<?php
    endif;
    include_once 'common/footer.php';
?>