<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<section id="main" class="wrapper">
    <div class="container">

        <header class="major special">
            <h2>Forgot your password?</h2>
            <p>Enter your username and we'll send you an email to your registered email address.</p>
        </header>
<?php  
/**
* This page is for those users who have forgotten their
* password and want to have a new password generated for
* them and sent to the email address attached to their
* account in the database. The new password is not
* displayed on the website for security purposes.
*
* Note: If your server is not properly setup to send
* mail, then this page is essentially useless and it
* would be better to not even link to this page from
* your website.
*/

if(isset($_SESSION['forgotpass'])){

	// new password generated & emailed
	if($_SESSION['forgotpass']){
		echo "<div class=\"alert info\">Your new password has been generated and sent to the email associated with your account.</div>";
	}
	// email could not be sent, so password not edited in the database
	else{
		echo "<div class=\"alert warning\">There was an error sending you the email with the new password, so your password has not been changed. Please try again later.</div>";
	}
	unset($_SESSION['forgotpass']);

}else{
	if($session->logged_in){
		?>
		<div class="alert warning">You are currently logged in. To change your password please go to <a href="<?php   echo $server_url; ?>/user/accountedit">Edit Account Details</a>.</div>
		<?php  
	}else{
		?>
<p>If you cannot access your email account, then please contact us via email (see bottom of page).</p>
<form action="<?php   echo SITE_URL; ?>/include/process.php" method="post">
    <div class="row uniform">
    	<div class="12u$">
            <input type="text" name="user" value="<?php   echo $form->value("user") ?>" placeholder="Enter your username" required autofocus>
            <?php   if ($form->error("user")): ?>
                <span class="help-block">
                    <strong><?php   echo $form->error("user") ?></strong>
                </span>
            <?php   endif ?>
        </div>
    </div>
    <div class="row uniform">
        <div class="12u$(small)">
            <div class="g-recaptcha" data-sitekey="6Lc3jRQTAAAAAB5vDMM4aln38oHmEXYwaKEiqJNG"></div>
        </div>
    </div>
    <input type="hidden" name="subforgot" value="1">
    <div class="row uniform">
    	<div class="12u$(small)">
            <input type="submit" value="Send Password Reset Link">
        </div>
    </div>
</form>

<?php   }} ?>

  </div>
</section>
