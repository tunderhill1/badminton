<?php  
$pagetitle = "Create Match";
include("../include/session.php");

if($session->userlevel >= LEVEL_ADMIN || $session->username == "bgl19"){
	include("../include/header.tpl.php");
?>
<section id="main" class="wrapper">
    <div class="container">
        <header class="major special">
            <h2>Create New Match</h2>
        </header>
  <form action="adminprocess.php" method="POST">
	<div class="row uniform">
    	<div class="4u">
            <input type="text" name="day" value="<?php   echo $form->value("day") ?>" placeholder="DD" required>
        </div>
        <div class="4u">
            <input type="text" name="month" value="<?php   echo $form->value("month") ?>" placeholder="MM" required>
        </div>
        <div class="4u">
            <input type="text" name="year" value="<?php   echo $form->value("year") ?>" placeholder="YYYY" required>
        </div>
    </div>
	<div class="row uniform">
    	<div class="3u">
            <input type="text" id="starthour" name="starthour" value="<?php   echo $form->value("starthour") ?>" placeholder="Start Hour" required>
        </div>
        <div class="3u">
            <input type="text" id="startmin" name="startmin" value="<?php   echo $form->value("startmin") ?>" placeholder="Start Minute" required>
			<input type="hidden" name="startsec" value="00">
        </div>
    	<div class="3u">
            <!-- <input type="text" id="endhour" name="endhour" value="<?php   echo $form->value("endhour") ?>" placeholder="End Hour" required> -->
        </div>
        <div class="3u">
            <!-- <input type="text" id="endmin" name="endmin" value="<?php   echo $form->value("endmin") ?>" placeholder="End Minute" required>
			<input type="hidden" name="endsec" value="00"> -->
        </div>
    </div>
    <div class="row uniform">
    	<div class="12u$">
            <textarea name="trad" id="trad" placeholder="Enter any notes about the match"></textarea>
        </div>
    </div>
    <div class="row uniform">
    	<div class="6u 12u$(small)">
    		<input type="text" id="location" name="location" placeholder="Location" required>
        </div>
        <div class="3u 6u(small)">
            <input type="text" id="opponent" name="opponent" placeholder="Opponent" required>
        </div>
        <div class="3u 6u(small)">
            <input type="text" id="places" name="places" placeholder="Number of players" required>
        </div>
    </div>
	<input type="hidden" name="subcreatematch" value="1">
    <div class="row uniform">
    	<div class="12u$(small)">
            <ul class="actions">
                <li><input type="submit" value="Save"></li>
                <li><input type="reset" class="alt" value="Reset"></li>
            </ul>
        </div>
    </div>
  </form>
</div>
<?php   include("../include/footer.tpl.php") ?>

<?php   }else{header("Location: " . SITE_URL);} ?>
