<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<?php
	if (!isset($_SESSION['LoggedIn'])&&substr($_SERVER['REQUEST_URI'],0,10)!="/login.php"&&substr($_SERVER['REQUEST_URI'],0,11)!="/signup.php"&&substr($_SERVER['REQUEST_URI'],0,18)!="/accountverify.php"&&substr($_SERVER['REQUEST_URI'],0,13)!="/password.php"&&substr($_SERVER['REQUEST_URI'],0,21)!="/passwordretrieve.php"&&substr($_SERVER['REQUEST_URI'],0,21)!="/retrievepassword.php"):
	echo '<meta http-equiv="REFRESH" content="0;url=/index.php">';	
	die();
	endif;?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  

<?php
	if(substr($_SERVER['REQUEST_URI'],0,12)!="/profile.php"):
?>
    <title><?php echo $pageTitle ?></title>
<?php
	endif;
	?>
    <link rel="stylesheet" href="/style.css" type="text/css" />
    <link rel="shortcut icon"  href="/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" media="screen" href="js/ui.jqgrid.css" />

    <!--<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js?ver=1.3.2'></script>-->
    <script type='text/javascript' src='/js/jquery.min.js'></script>
</head>

<body>

    <div id="page-wrap">

        <div id="header">

            <h1 class="headerhover gradientShadows"><a href="/board.php" class="">trackCraze</a></h1>

			<h2 id="BTW" class="gradientShadows"><a href="http://behindtheworkout.com">BehindTheWorkout</a></h2>

            <div id="control">

<?php
    if(isset($_SESSION['LoggedIn']) && isset($_SESSION['Username'])
        && $_SESSION['LoggedIn']==1):
        $Username=$_SESSION['Username'];
?>
				<p class='loggedinas'><b>Logged in as: <a href="/board.php" class='usernameurl'><?php echo $_SESSION['Username'] ?></a></b></p>
                <a href="/logout.php" class="logout sp"></a> <a href="/account.php" class="account sp"></a><a href="/programlists.php" class="programlists sp"></a>
              
<input type='text' class='searchusers headersearchi' name='search' value="" autocomplete='off' placeholder='Search users'/><div class='search headersearch sp'></div>  <!-- remove this after fix login and sign up page-->
<?php
	//echo "<div id='newsCon'><a class='TitleConlink' href='news.php'><div class='TitleCon hover h3'>News & Updates</div></a><div id='newscontent'>";
	//$news->getMiniNews();
	//echo "</div></div>";//PUT IT IN A POP UP
?>
<?php else: 
		if(substr($_SERVER['REQUEST_URI'],0,10)=="/login.php"):
			echo "<a class='headerbutton sp' href='signup.php'>Sign up</a><p class='headertext'>New to trackCraze?</p>";
        elseif(substr($_SERVER['REQUEST_URI'],0,11)=="/signup.php"):
        	echo "<a href='login.php' class='headerbutton sp'>&nbspLog in&nbsp</a><p class='headertext'>Have an account?</p>";
                else:
                echo '<a href="login.php" class="headerbutton sp">Log in </a> <a class="headerbutton sp" href="signup.php">Sign up</a>';
                endif;
		
endif; ?>

            </div>
<!--<input type='text' class='searchusers headersearchi' name='search' value="" autocomplete='off' placeholder='Search users'/><div class='search headersearch sp'></div> put this back in after change login and sign up page-->
    <script>
    $('.headersearchi').keypress(function(e){
    if (e.which == 13) {
    	searchenter();
    }
	});
	$(".headersearch").live("click",function(){
		searchenter();
	});
    	function searchenter(){
    		if($(".headersearchi").val().replace(/\s/g,"").length>0){
    			item = $(".headersearchi").val().replace(/^(\s+)|(\s+)$/g,"");
    			item = item.replace(/(\s+)/g," ");
    			window.location = "search.php?search="+encodeURIComponent(item)
  		 }
    	};
    	</script>    
	</div>
        