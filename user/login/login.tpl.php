<section id="main" class="wrapper">
    <div class="container">
<?php  
// already logged in
if($session->logged_in){
	echo "<div class=\"alert info\">You are already logged in!</div>";
} else{	?>

    <header class="major special">
        <h2>Login</h2>
        <p>Sign in to your account to book sessions.</p>
    </header>

	<form action="<?php   echo SITE_URL; ?>/include/process.php" method="POST">
        <div class="row uniform">
        	<div class="12u$">
                <input type="text" name="lguser" value="<?php   echo $form->value("lguser") ?>" placeholder="Enter your username" required autofocus>
            </div>
        </div>

        <div class="row uniform">
        	<div class="12u$">
                <input type="password" name="lgpass" placeholder="Enter your password" required>
                <?php   if ($form->error("login")): ?>
	                <span class="help-block">
	                    <strong><?php   echo $form->error("login") ?></strong>
	                </span>
	            <?php   endif ?>
            </div>
        </div>
        <div class="row uniform">
            <div class="12u$">
                <input type="checkbox" name="remember" id="remember" checked>
                <label for="remember">Remember me</label>
            </div>
        </div>

        <input type="hidden" name="sublogin" value="1">
        <div class="row uniform">
            <div class="10u 12u$(small)">
                <ul class="actions">
                    <li><input type="submit" value="Login"></li>
                    <li><a class="button alt" href="<?php   echo SITE_URL; ?>/user/forgotpass/">Forgot Your Password?</a></li>
                </ul>
            </div>
            <div class="2u 12u$(small) center-brand-container">
                <img src="<?php   echo SITE_URL ?>/images/icu.jpg" id="icu-brand">
            </div>
        </div>
	</form>
	<?php   } ?>
</div>
</section>
