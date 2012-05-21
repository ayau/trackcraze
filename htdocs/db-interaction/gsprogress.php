<?php

session_start();
 
include_once "../inc/constants.inc.php";
include_once "../inc/class.progress.inc.php";
$listObj = new GSProgress();

if (!empty($_POST['action'])
	&& isset($_SESSION['LoggedIn'])
	&& $_SESSION['LoggedIn']==1 )
{
    switch($_POST['action'])
	    {
        case 'loadInputExercise':
            echo $listObj->loadInputExercise();
            //header("Location: /");
            break;
        case 'addweight':
            $listObj->addWeight();
            break;
        case 'loadweight':
        	echo $listObj->loadWeight();
        	break;
        case 'loadweightoption':
        	echo $listObj->loadWeightOption();
        	break;
        case 'changeweightoption':
        	$listObj->changeWeightOption();
        	break;
        case 'deleteweight':
        	$listObj->deleteweight();
        	break;
        case 'recordsubmit':
        	$listObj->recordsubmit();
        	break;
        case 'loadPrevRecordByDate':
        	$listObj->loadPrevRecordByDate($_POST['uid'],$_POST['date'],$_POST['prevnext']);
        	break;
        case 'fillCalendar':
        	echo $listObj->fillCalendar();
        	break;
        case 'loadbyExercise':
        	echo $listObj->loadbyExercise();
        	break;
        case 'loadTrackExercise':
        	echo $listObj->loadTrackExercise();
        	break;
        case 'loadGeneralExercise':
        	echo $listObj->loadGeneralExercise();
        	break;
        case 'printbyExercise':
        	echo $listObj->printbyExercise();
        	break;
        case 'addnewset':
        	echo $listObj->addnewset();
        	break;
        case 'maxminweight':
        	echo $listObj->maxminWeight();
        	break;
        case 'addOldExerciseSet':
        	echo $listObj->addOldExerciseSet();
        	break;
        case 'getSplitIDByDate':
        	echo $listObj->getSplitIDByDate();
        	break;
        case 'getProgramIDbySplitID':
        	echo $listObj->getProgramIDbySplitID();
        	break;
    }
}
else
{
    header("Location: /");
    exit;
}
 
?>
