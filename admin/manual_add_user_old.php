<?php
$pagetitle = "Manual Add User";
include("../include/session.php");

if($session->username == "rh2015" || $session->username == "el2018"){

	if(isset($_POST['submanualadduser'])){
		global $database;
		$username = htmlentities(mysql_real_escape_string($_POST['username']));
		// $firstname = htmlentities(mysql_real_escape_string($_POST['firstname']));
		// $lastname = htmlentities(mysql_real_escape_string($_POST['lastname']));
		// $email = htmlentities(mysql_real_escape_string($_POST['email']));

		if($database->userExists($username)){
			if($database->updateUserField($username, 'active', '1')){
				echo "<div class=\"alert info\"> User ".$username." has successfully been activated! </div>";
			} else {
				echo "<div class=\"alert info\"> User ".$username." could not be activated. </div>";
			}
		} else {
			if($database->autoCreateUser($username)){
				if($database->updateUserField($username, 'active', '1')){
					echo "<div class=\"alert info\"> User ".$username." has successfully been activated! </div>";
				} else {
					echo "<div class=\"alert info\"> User ".$username." has been added but could not be activated. </div>";
				}
			} else {
				echo "<div class=\"alert info\"> Error: User could not be added. </div>";
			}
		}
	}
	
	include("../include/header.tpl.php");

?>
<section id="main" class="wrapper">
	<div class="container">
		<header class="major special">
			<h2>Manual Add User (ONLY ADMIN AND PRESIDENT)</h2>
		</header>

		<form action="" method="POST">
			<div class="row uniform">
				<div class="12u$(small)">
					<input type="text" name="username" value="<?php   echo $username; ?>" placeholder="username" required>
				</div>
				<!-- <div class="3u">
					<input type="text" name="firstname" value="<?php   echo $firstname; ?>" placeholder="first name" required>
				</div>
				<div class="3u">
					<input type="text" name="lastname" value="<?php   echo $lastname ?>" placeholder="last name" required>
				</div>
				<div class="3u">
					<input type="text" name="email" value="<?php   echo $email ?>" placeholder="email" required>
				</div> -->
			</div>
			<input type="hidden" name="submanualadduser" value="1">
			<div class="row uniform">
	    		<div class="12u$(small)">
		            <ul class="actions">
		                <li><input type="submit" value="Add User"></li>
		            </ul>
		        </div>
		    </div>
  		</form>
	</div>
<?php   

include("../include/footer.tpl.php");
} else {
	header("Location: " . SITE_URL);
}
?>
