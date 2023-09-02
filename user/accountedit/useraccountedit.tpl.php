<section id="main" class="wrapper">
    <div class="container">

<?php  
/**
 * User has submitted form without errors and user's
 * account has been edited successfully.
 */
if(isset($_SESSION['useredit'])){
   unset($_SESSION['useredit']);

   echo "<div class=\"alert info\">Your account has been successfully updated.</div>";
}
else{
/**
 * If user is not logged in, then do not display anything.
 * If user is logged in, then display the form to edit
 * account information, with the current email address
 * already in the field.
 */
if($session->logged_in){
?>

<header class="major special">
    <h2>Account</h2>
    <p>You can update your account details below.</p>
</header>

<p>All fields are optional. Only fill in the fields you wish to update.</p>
<form action="<?php   echo $server_url; ?>/include/process.php" method="POST">
    <div class="row uniform">
    	<div class="12u$">
            <input type="email" name="email" placeholder="Update your email">
            <?php   if ($form->error("email")): ?>
                <span class="help-block">
                    <strong><?php   echo $form->error("email") ?></strong>
                </span>
            <?php   endif ?>
        </div>
    </div>
    <div class="row uniform">
    	<div class="12u$">
            <p><i>To delete your nickname, enter a single exclamation mark (!) in the Nickname field.</i></p>
            <input type="text" name="nickname" placeholder="Update your nickname">
            <?php   if ($form->error("nickname")): ?>
                <span class="help-block">
                    <strong><?php   echo $form->error("nickname") ?></strong>
                </span>
            <?php   endif ?>
        </div>
    </div>
    <div class="row uniform">
    	<div class="12u$">
            <input type="password" name="newpass" placeholder="Update your password">
            <?php   if ($form->error("newpass")): ?>
                <span class="help-block">
                    <strong><?php   echo $form->error("newpass") ?></strong>
                </span>
            <?php   endif ?>
        </div>
    </div>
    <div class="row uniform">
    	<div class="12u$">
            <input type="password" name="newpass2" placeholder="Confirm your new password">
            <?php   if ($form->error("newpass2")): ?>
                <span class="help-block">
                    <strong><?php   echo $form->error("newpass2") ?></strong>
                </span>
            <?php   endif ?>
        </div>
    </div>
    <hr>
    <div class="row uniform">
    	<div class="12u$">
            <p>To save any changes you made, please confirm your current password.</p>
            <input type="password" name="curpass" placeholder="Enter your current password" required>
            <?php   if ($form->error("curpass")): ?>
                <span class="help-block">
                    <strong><?php   echo $form->error("curpass") ?></strong>
                </span>
            <?php   endif ?>
        </div>
    </div>

    <input type="hidden" name="subaccountedit" value="1">
    <div class="row uniform">
    	<div class="12u$">
            <ul class="actions">
                <li><input type="submit" value="Save"></li>
                <li><input type="reset" value="Reset" class="alt"></li>
            </ul>
        </div>
    </div>
</form>

<?php  
}else{
   echo "<h2>You are not logged in</h2>";
   echo "<p>To edit your user account you need to be logged in.</p>";
}
}
?>
    </div>
</section>
