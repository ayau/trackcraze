<?php require "utils/constants.php";
  
  require 'utils/facebook/facebook.php';
  $facebook = new Facebook(array(
      'appId'  => FB_APID,
      'secret' => FB_SECRET,
  ));
    // See if there is a user from a cookie
  $fb_user = $facebook->getUser();
?>

<!DOCTYPE html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <div id="fb-root"></div>
<pre><?php print_r($_SESSION); print_r($facebook->api('/me')); ?></pre>
<?php if ($fb_user): ?>

  <script type='text/javascript'>
      window.fbAsyncInit = function() {
        FB.init({
          appId: '<?php echo $facebook->getAppID() ?>', 
          cookie: true, 
          xfbml: true,
          oauth: true
        });

        FB.Event.subscribe('auth.logout', function(response) {
          window.location = "index.php";
        });
        FB.getLoginStatus(function(response){
          FB.logout();
        });
      };
      (function() {
        var e = document.createElement('script'); e.async = true;
        e.src = document.location.protocol +
          '//connect.facebook.net/en_US/all.js';
        document.getElementById('fb-root').appendChild(e);
      }());

</script>
<?php
  else:?>

    <script> window.location = "index.php";</script>

<?php endif;?>


