<?php
    include_once "common/base.php";
    if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']==1):
        $pageTitle = "My Account";
        include_once "common/header.php";
        include_once 'inc/class.users.inc.php';
        $users = new GymScheduleUsers($db);
 
        if(isset($_GET['email']) && $_GET['email']=="changed")
        {
            echo "<div class='message good'>Your email address "
                . "has been changed.</div>";
        }
        else if(isset($_GET['email']) && $_GET['email']=="failed")
        {
            echo "<div class='message bad'>There was an error "
                . "changing your email address.</div>";
        }
 
        if(isset($_GET['password']) && $_GET['password']=="changed")
        {
            echo "<div class='message good'>Your password "
                . "has been changed.</div>";
        }
        elseif(isset($_GET['password']) && $_GET['password']=="nomatch")
        {
            echo "<div class='message bad'>The two passwords "
                . "did not match. Try again!</div>";
        }elseif(isset($_GET['password']) && $_GET['password']=="failed")
        {
            echo "<div class='message bad'>Password should be 6-18 characters. Letters, numbers, underscores and hyphens allowed</div>";
        }
 
        if(isset($_GET['delete']) && $_GET['delete']=="failed")
        {
            echo "<div class='message bad'>There was an error "
                . "deleting your account.</div>";
        }
 
        list($userID, $v) = $users->retrieveAccountInfo();
?>
 		<div id="loginheading">
        <h2>Your Account</h2>
        </div>
        <!--<form method="post" action="db-interaction/users.php">
            <div>
                <input type="hidden" name="userid"
                    value="<?php echo $userID ?>" />
                <input type="hidden" name="action"
                    value="changeemail" />
                <input type="text" class='inputfields' name="username" id="username" placeholder="New Email Address"/>
                <label for="username">Change Email Address</label>
                <br /><br />
                <input type="submit" name="change-email-submit"
                    id="change-email-submit" value="Change Email"
                    class="lightgreen wide box" />
            </div>
        </form><br /><br /> -->
 
        <form method="post" action="db-interaction/users.php"
            id="change-password-form">
            <div>
                <input type="hidden" name="user-id"
                    value="<?php echo $userID ?>" />
                <input type="hidden" name="v"
                    value="<?php echo $v ?>" />
                <input type="hidden" name="action"
                    value="changepassword" />
                <input type="password"
                    name="p" class='inputfields' id="new-password" placeholder="New Password" />
                <label for="password">New Password</label>
                <br /><br />
                <input type="password" name="r" class='inputfields' 
                    id="repeat-new-password" placeholder="Retype New Password"/>
                <label for="password">Repeat New Password</label>
                <br /><br />
                <input type="submit" name="change-password-submit"
                    id="change-password-submit" value="Change Password"
                    class="lightgreen wide box" />
            </div>
        </form>
        <hr />
 
        <form method="post" action="deleteaccount.php"
            id="delete-account-form">
            <div>
                <input type="hidden" name="user-id"
                    value="<?php echo $userID ?>" />
                <input type="submit"
                    name="delete-account-submit" id="delete-account-submit"
                    value="Delete Account?" class="red wide box" />
            </div>
        </form>
 
<?php
    else:
        header("Location: ");
        exit;
    endif;
?>
 
<div class="clear"></div>
 
<?php
    include_once "common/footer.php";
    ?>
