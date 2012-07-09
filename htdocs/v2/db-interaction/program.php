<?php
 
session_start();
 
include_once "../utils/constants.php";
include_once "../model/class.user.php";
include_once "../model/class.program.php";
$program = new Program();


if(!empty($_POST['action'])){
    
    switch($_POST['action'])
    {
        case 'createProgram':
            echo $program->createProgram();
            break;
        case 'deleteProgram':
        	echo $program->deleteProgram();
        	break;
        default:
        	header("../index.php");
        	break;
    }
}
?>