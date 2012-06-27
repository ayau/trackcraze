<?php
require 'utils/facebook/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '104011209743511',
  'secret' => 'f3142bf48ff18c093903afb90c6eb49d',
));

// See if there is a user from a cookie
$user = $facebook->getUser();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    //echo '<pre>'.htmlspecialchars(print_r($e, true)).'</pre>';
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}


?>

<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <body>
    <?php if ($user) { ?>
    	 <a href="<?php echo $logoutUrl; ?>">Logout</a>
      Your user profile is 
       <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">
      <pre>            
        <?php print htmlspecialchars(print_r($user_profile, true)) ?>
      </pre> 
    <?php } else { ?>
    	Login using OAuth 2.0 handled by the PHP SDK:
      <fb:login-button></fb:login-button>
    <?php } ?>
    <div id="fb-root"></div>
    
    <h3>PHP Session</h3>
    <pre><?php print_r($_SESSION); ?></pre>

    
    <script>               
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>', 
          cookie: true, 
          xfbml: true,
          oauth: true
        });
        FB.Event.subscribe('auth.login', function(response) {
          	//FB.api('/me', function(response) {
      		//	alert('You have successfully logged in, '+response.name+"!");
			//});
        	//login(); //
        	window.location.reload();
        });
        FB.Event.subscribe('auth.logout', function(response) {
          window.location.reload();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());
      
    </script>
  </body>
</html>
