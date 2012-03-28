<?php
    include_once "common/base.php";
    $pageTitle = "Verify Your Account";
    include_once "common/header.php";
 
    if(isset($_GET['v']) && isset($_GET['e'])):
?>
 
        <div id="loginheading"><h2>Thanks for using this website...Can't really think of some ass-kissing thankful message, but just know that we are thankful, really, we are.</h2></div>
 		<br /><br /><br />
        <form method="post" action="accountverify.php">
                <input type="hidden" name="v" value="<?php echo $_GET['v'] ?>" />
                <input type="button" name="verify" id="verify" value="Verify Your Account" />
        </form>
<script>
$("#verify").live("click",function(){
		$.ajax({
       type: "POST",
       url: "db-interaction/users.php",
       data: "action=verifyaccount&email="+"<?php echo $_GET['e']?>"+
       "&vercode="+"<?php echo $_GET['v']?>",
       success:function(){
       	alert("Lol");
    	window.location.replace("profile.php");       	
   },
     error:function(){
 }  
  });
});
</script>
<?php
    else:
        //echo '<meta http-equiv="refresh" content="0;index.php">';
    endif;
 
    include_once 'common/footer.php';
?>