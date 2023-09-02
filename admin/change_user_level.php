<?php  
include("../include/session.php");

if($session->userlevel >= LEVEL_ADMIN){
	include("../include/header.tpl.php");
	include("../include/constants.php")
?>

<section id="main" class="wrapper">
    <div class="container">
        <header class="major special">
            <h2>Change User Level</h2>
        </header>
	<form action="adminprocess.php" method="POST">
		<div class="row uniform">
        	<div class="6u">
                <input type="text" name="upduser" value="<?php   echo $form->value("upduser"); ?>" placeholder="Username">
            </div>
			<div class="6u$">
				<div class="select-wrapper">
					<select name="updlevel" required>
						<option value="" disabled selected hidden>User Level</option>
						<option value=0>Non-member (not activated)</option>
						<option value=1>Activated (paid) member</option>
						<option value=2>Session manager</option>
						<option value=3>Committee</option>
						<option value=4>Web Admin</option>
					</select>
				</div>
            </div>
        </div>
		<div class="row uniform">
        	<div class="12u$(small)">
                <ul class="actions">
                    <li><input type="submit" name="subupdlevel" value="Update"></li>
                </ul>
            </div>
        </div>
	</form>
</div>
<?php   include("../include/footer.tpl.php"); ?>
<?php   }else{header("Location: " . SITE_URL);} ?>
