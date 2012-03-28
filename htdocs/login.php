<?php
    include_once "common/base.php";
    $pageTitle = "Home";
    include_once "common/header.php";
 
    if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['UserID'])):
 		echo "<meta HTTP-EQUIV='REFRESH' content='0; /board.php'>";
    elseif(!empty($_POST['username']) && !empty($_POST['password'])):
        include_once 'inc/class.users.inc.php';
        $users = new GymScheduleUsers($db);
        if($users->accountLogin()===TRUE):
            echo "<meta HTTP-EQUIV='REFRESH' content='0; URL=/board.php'>";
            exit;
        else:
?>
           <div id="loginheading">       
        <h2>Login Failed&mdash;Try Again?</h2>
        </div>
        <form method="post" action="login.php" name="loginform" id="loginform">
            <div>
                <input type="text" name="username" id="username" class='inputfields' placeholder='Email'/>
                <label for="username">Email</label>
                <br /><br />
                <input type="password" name="password" id="password" class='inputfields' placeholder='Password'/>
                <label for="password">Password</label>
                <br /><br />
                <input type="submit" name="login" id="login" value="Login" class="button" />
            </div>
        </form>
        <p><a href="/password.php">Did you forget your password?</a></p>
<?php
        endif;
    else:
?>
               
        <div id="loginheading"><h2 >Get back to your workout</h2></div>
        <form method="post" action="login.php" name="loginform" id="loginform">
            <div>
                <input type="text" name="username" id="username" class='inputfields' placeholder='Email'/>
                <label for="username">Email</label>
                <br /><br />
                <input type="password" name="password" id="password" class='inputfields' placeholder='Password'/>
                <label for="password">Password</label>
                <br /><br />
                <input type="submit" name="login" id="login" value="Login" class="button wide box" />
            </div>
        </form><br /><br />
        <p><a href="/password.php" class="twentyfont">Did you forget your password?</a></p>
<?php
    endif;
?>
 
        <div style="clear: both;"></div>
<?php
    include_once "common/footer.php";
?>