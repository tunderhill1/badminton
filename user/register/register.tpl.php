<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<section id="main" class="wrapper">
    <div class="container">
<?php  
// already logged in
if($session->logged_in){
	echo "<div class=\"alert info\">You are already registered and logged in!</div>";
}

// user has submitted registration form
else if(isset($_SESSION['regsuccess'])){
	/* Registration was successful */
	if($_SESSION['regsuccess']){
		echo "<div class=\"alert info\">You have successfully registered with the username <strong>{$_SESSION['reguname']}</strong></div>";
    	echo "<p>After you have paid for club membership through the Union, <strong>please contact us via email (below) so that we can manually activate your account</strong>, otherwise you will not be able to book in to club sessions.</p>";
	}else{
		echo "<div class=\"alert warning\">Sorry, an error has occurred and your registration could not be completed. Please try again later.</div>";
	}
	unset($_SESSION['regsuccess']);
	unset($_SESSION['reguname']);
}

// user not yet filled out registration form
// below is the page with the sign-up form
else{	?>

	<header class="major special">
        <h2>Register</h2>
        <p>Create an account, pay the membership fee, and start attending club sessions.</p>
    </header>

	<form action="<?php   echo SITE_URL; ?>/include/process.php" method="POST">
		<div class="row uniform">
        	<div class="4u 12u$(small)">
                <input type="text" name="firstname" value="<?php   echo $form->value("firstname") ?>" placeholder="Enter your first name" autofocus required>
				<?php   if ($form->error("firstname")): ?>
	                <span class="help-block">
	                    <strong><?php   echo $form->error("firstname") ?></strong>
	                </span>
	            <?php   endif ?>
            </div>
            <div class="4u 12u$(small)">
                <input type="text" name="secondname" value="<?php   echo $form->value("secondname") ?>" placeholder="Enter your last name" required>
				<?php   if ($form->error("secondname")): ?>
	                <span class="help-block">
	                    <strong><?php   echo $form->error("secondname") ?></strong>
	                </span>
	            <?php   endif ?>
            </div>
			<div class="4u$ 12u$(small)">
                <input type="text" name="nickname" value="<?php   echo $form->value("nickname") ?>" placeholder="Enter your nickname">
				<?php   if ($form->error("nickname")): ?>
	                <span class="help-block">
	                    <strong><?php   echo $form->error("nickname") ?></strong>
	                </span>
	            <?php   else: ?>
					<span class="help-block">
						This is <strong>optional</strong>.
					</span>
	            <?php   endif ?>
            </div>
        </div>
        <div class="row uniform">
        	<div class="12u$">
                <input type="email" name="email" value="<?php   echo $form->value("email") ?>" placeholder="Enter your email address" required>
				<?php   if ($form->error("email")): ?>
	                <span class="help-block">
	                    <strong><?php   echo $form->error("email") ?></strong>
	                </span>
	            <?php   endif ?>
            </div>
        </div>
        <div class="row uniform">
        	<div class="12u$">
                <input type="email" name="email2" value="<?php   echo $form->value("email2") ?>" placeholder="Confirm your email address" required>
				<?php   if ($form->error("email2")): ?>
	                <span class="help-block">
	                    <strong><?php   echo $form->error("email2") ?></strong>
	                </span>
	            <?php   endif ?>
            </div>
        </div>
		<hr>
		<div class="row uniform">
        	<div class="12u$">
                <input type="text" name="user" value="<?php   echo $form->value("user") ?>" placeholder="Pick your username" required>
				<?php   if ($form->error("user")): ?>
	                <span class="help-block">
	                    <strong><?php   echo $form->error("user") ?></strong>
	                </span>
	            <?php   endif ?>
            </div>
        </div>
        <div class="row uniform">
        	<div class="12u$">
                <input type="password" name="pass" placeholder="Enter your password" required>
				<?php   if ($form->error("pass")): ?>
	                <span class="help-block">
	                    <strong><?php   echo $form->error("pass") ?></strong>
	                </span>
	            <?php   endif ?>
            </div>
        </div>
        <div class="row uniform">
        	<div class="12u$">
                <input type="password" name="pass2" placeholder="Confirm your password" required>
				<?php   if ($form->error("pass2")): ?>
	                <span class="help-block">
	                    <strong><?php   echo $form->error("pass2") ?></strong>
	                </span>
	            <?php   endif ?>
            </div>
        </div>

		<input type="hidden" name="subjoin" value="1">
		<div class="row uniform">
			<div class="g-recaptcha" data-sitekey="6Lc3jRQTAAAAAB5vDMM4aln38oHmEXYwaKEiqJNG"></div>
			<?php   if ($form->error("captcha")): ?>
				<span class="help-block">
					<strong><?php   echo $form->error("captcha") ?></strong>
				</span>
			<?php   endif ?>
		</div>
        <div class="row uniform">
        	<div class="12u$(small)">
                <input type="submit" value="Register">
            </div>
        </div>
	</form>
	<?php   } ?>
</div>
</section>
