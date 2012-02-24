<?php
 
session_start();
 
include_once "../inc/constants.inc.php";
include_once "../inc/class.lists.inc.php";
$listObj = new GymScheduleItems();
 
if (!empty($_POST['action'])
	&& isset($_SESSION['LoggedIn'])
	&& $_SESSION['LoggedIn']==1 )
{

    switch($_POST['action'])
    {
        case 'add':
            echo $listObj->addListItem($_POST['sid'], $_POST['pos'], $_POST['eid']);
            //header("Location: /");
            break;
        case 'addnew':
            echo $listObj->addnewListItem();//new Exercise
            //header("Location: /");
            break;
        case 'update':
            $listObj->updateListItem();
            break;
        case 'updateExercise':
        	$listObj->updateExerciseItem();
        	break;
        case 'addSet':
        	$listObj->addSet();
        	break;
        case 'addExercise':
        	$listObj->addExerciseItem();
        	break;
        case 'addSplit':
        	$listObj->addSplit();
        case 'sort':
            $listObj->changeListItemPosition();
            break;
        case 'color':
            echo $listObj->changeListItemColor();
            break;
        case 'done':
            echo $listObj->toggleListItemDone();
            break;
        case 'delete':
            echo $listObj->deleteListItem();
            break;
        case 'deleteSet':
        	echo $listObj->deleteSetItem();
        	break;
        case 'dragset':
        	$listObj->dragset();
        	break;
        case 'getprogramprivacy':
        	echo $listObj->getprogramprivacy($_POST['pid']);
        	break;
        case 'updateprogramprivacy':
        	$listObj->updateprogramprivacy($_POST['pid'],$_POST['val']);
        	break;
     	case 'updatemainprogram':
        	$listObj->updatemainprogram();
        	break;
        case 'loadExerciseSearch':
        	echo $listObj->loadExerciseSearch();
        	break;
        case 'addProgram':
        	echo $listObj->addProgram();
        	break;
        case 'deletesection':
        	echo $listObj->deleteSection();
        	break;
        case 'deleteprogram':
        	echo $listObj->deleteProgram();
        	break;
        case 'updateSectionname':
        	echo $listObj->updateSectionname();
        	break;
        case 'updateProgramname':
        	echo $listObj->updateProgramname();
        	break;
        default://Weird things happen when default runs
            header("Location: /");
            break;
    }
}
else
{
    header("Location: /");
    exit;
}
 
?>
