<?php
 
session_start();
 
include_once "../inc/constants.inc.php";
include_once "../inc/class.users.inc.php";
$userObj = new GymScheduleUsers();


if(!empty($_POST['action'])){
if (isset($_SESSION['LoggedIn'])
&& $_SESSION['LoggedIn']==1)
{    
    switch($_POST['action'])
    {
        case 'changeemail':
            $status = $userObj->updateEmail() ? "changed" : "failed";
            header("Location: /account.php?email=$status");
            break;
        case 'changepassword':
            $status = $userObj->updatePassword() ? "changed" : "nomatch";
            header("Location: /account.php?password=$status");
            break;
        case 'deleteaccount':
            $userObj->deleteAccount();
            break;
    	case 'editProfile':
         echo $userObj->editProfile();
         break;
        case 'newsupdate':
        	$userObj->newsUpdate($_POST['newstype'], $_POST['content']);
        break;
        case 'newgoal':
        	echo $userObj->addNewGoal();
        break;
        case 'deletegoal':
        	$userObj->deleteGoal();
        break;
        case 'weightcheck':
        	$userObj->checkgoalWeight();
        break;
        case 'trackthisperson':
        	$userObj->trackthisperson();
        break;
        case 'goaldisplay':
        	$userObj->goalshownChange();
        break;
        case 'addsport':
        	$userObj->addSport();
        break;
        case 'deletesport':
        	$userObj->deleteSport();
        break;
        case 'deletecustomsport':
        	$userObj->deleteCustomSport();
        break;
        case 'getTrackerO':
        	echo $userObj->getTrackerO();
        break;
        case 'updatestatus':
        	echo $userObj->updatestatus();
        break;
        case 'changetoptrack':
        	$userObj->changeTopTrack();
        break;
        case 'sendfeedback':
        	$userObj->sendfeedback();
        break;
        default:
            header("Location: ");
        break;
    }
}
else{
	switch($_POST['action'])
    {
        case 'resetpassword':
           if($resp=$userObj->resetPassword()===TRUE){
        		header("Location: resetpending.php");
    		}else{
        		echo $resp;
    		}
    		exit;
        break;
		case "createaccount":
			echo $userObj->accountCreate();
		break;
		case "verifyaccount":
			$userObj->verifyAccount();
		break;
		case "alpha":
			echo "chickensalad";
		break;
		case "addemail":
			echo $userObj->addemail();
		break;
		case "addfailemail":
			echo $userObj->addfailemail();
		break;
	}
}
}
else
{
    header("Location: ");
    exit;
}
 
?>