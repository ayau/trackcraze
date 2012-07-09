<?php
 
session_start();
 
include_once "../utils/constants.php";
include_once "../model/class.user.php";
include_once "../model/class.program.php";
$user = new User();


if(!empty($_POST['action'])){
    
    switch($_POST['action'])
    {
        case 'facebook_login':
            echo $user->facebook_login();
            break;
        case 'logout':
        	$user->destroySession();
        	break;
        case 'setMainProgram':
            $user->setMainProgram($_POST['pid']);
            break;
        default:
        	header("../index.php");
        	break;
    }
}
?>