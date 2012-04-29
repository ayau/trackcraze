<?php

session_start();
 
include_once "../inc/constants.inc.php";
include_once "../inc/class.news.inc.php";
$listObj = new GSNews();

if (!empty($_POST['action'])
	&& isset($_SESSION['LoggedIn'])
		&& $_SESSION['LoggedIn']==1 )//not logged in people can't view
{

    switch($_POST['action'])
	    {
        case 'acceptTR':
        	echo $listObj->acceptTR();
        	break;
        case 'autoacceptTR':
        	echo $listObj->autoacceptTR();
        	break;
        case 'ignoreTR':
        	echo $listObj->ignoreTR();
        	break;
        case 'addPost':
        	echo $listObj->addPost();
        	break;
        case 'getPosts':
        	echo $listObj->getPosts($_POST['UID'],$_POST['Page']);
        	break;
        case 'changeProfilePic':
        	echo $listObj->checkProfilePic();
        	break;
        case 'addComment':
        	echo $listObj->addComment();
        	break;
        case 'deleteComment':
        	$listObj->deleteComment();
        	break;
        case 'deletePost':
        	$listObj->deletePost();
        	break;
        case 'givekudos':
        	$listObj->givekudos();
        	break;
        case 'expandcomments':
        	echo $listObj->expandcomments();
        	break;
        case 'stopTrack':
        	$listObj->stopTrack();
        break;
    }
}
else
{
    header("Location: /");
    exit;
}
 
?>
