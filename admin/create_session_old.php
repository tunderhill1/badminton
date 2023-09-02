<?php  
$pagetitle = "Create Session";
include("../include/session.php");

if($session->userlevel >= LEVEL_ADMIN){
	include("../include/header.tpl.php");
?>
	<!-- update the presets here -->
	<script type="text/javascript">
	function updateTimeFields(time){
		switch(time){
		case "1":
			document.getElementById('starthour').value = "16";
			document.getElementById('startmin').value = "00";
			document.getElementById('endhour').value = "18";
			document.getElementById('endmin').value = "00";
			document.getElementById('location').value = "Ethos";
			document.getElementById('places').value = "26";
			document.getElementById('managers').value = "2";
			document.getElementById('trad').value = "";
			break;
    	case "2":
			document.getElementById('starthour').value = "18";
			document.getElementById('startmin').value = "00";
			document.getElementById('endhour').value = "20";
			document.getElementById('endmin').value = "00";
			document.getElementById('location').value = "Ethos";
			document.getElementById('places').value = "26";
			document.getElementById('managers').value = "2";
			document.getElementById('trad').value = "";
			break;
		case "3":
			document.getElementById('starthour').value = "08";
			document.getElementById('startmin').value = "00";
			document.getElementById('endhour').value = "10";
			document.getElementById('endmin').value = "00";
			document.getElementById('location').value = "Ethos";
			document.getElementById('places').value = "8";
			document.getElementById('managers').value = "0";
			document.getElementById('trad').value = "Coaching session. Cost: £5 (cash)";
			break;
		}
	}
	</script>
<section id="main" class="wrapper">
    <div class="container">
        <header class="major special">
            <h2>Create New Session</h2>
        </header>
		<div class="row uniform">
			<ul class="actions">
				<li><button class="button alt" onclick="updateTimeFields('1');">1 Mon</button></li>
				<li><button class="button alt" onclick="updateTimeFields('2');">2 Tues </button></li>
				<li><button class="button alt" onclick="updateTimeFields('3');">3 Sun (Coaching)</button></li>
			</ul>
		</div>
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
            <input type="text" id="endhour" name="endhour" value="<?php   echo $form->value("endhour") ?>" placeholder="End Hour" required>
        </div>
        <div class="3u">
            <input type="text" id="endmin" name="endmin" value="<?php   echo $form->value("endmin") ?>" placeholder="End Minute" required>
			<input type="hidden" name="endsec" value="00">
        </div>
    </div>
    <div class="row uniform">
    	<div class="12u$">
            <textarea name="trad" id="trad" placeholder="Enter any notes about the session"></textarea>
        </div>
    </div>
    <div class="row uniform">
    	<div class="6u 12u$(small)">
            <div class="select-wrapper">
				<select name="location" id="location" required>
					<option value="" disabled selected hidden>Session location</option>
		            <option value="Ethos">Ethos</option>
		            <option value="St John Bosco College">St John Bosco College</option>
		            <option value="South Bank University">South Bank University</option>
		            <option value="Kensington Leisure Centre">Kensington Leisure Centre</option>
		            <option value="testsessionplsignore">testsessionplsignore</option>
	            </select>
            </div>
        </div>
        <div class="3u 6u(small)">
            <input type="text" id="managers" name="managers" placeholder="Number of managers" required>
        </div>
        <div class="3u 6u(small)">
            <input type="text" id="places" name="places" placeholder="Number of places" required>
        </div>
    </div>
	<input type="hidden" name="subcreatesession" value="1">
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
