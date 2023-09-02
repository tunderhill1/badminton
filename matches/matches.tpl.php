<section id="main" class="wrapper">
    <div class="container">
        <header class="major special">
           <h2>Social Matches</h2>
           <p>If you queue to join a social match, you'll be notified if you are added.</p> 
        </header>

<?php
	if($session->logged_in){
		if(!$session->activated){
			// non-activated account warning
			echo "<div class=\"alert warning\">Your user account has not been activated. Activation is required to register for social matches. To become activated, you must purchase the membership for the Imperial College Badminton Club. Click <a href=\"".SITE_URL."/about/membership/\">here</a> to find out more information.</div>";
		}
	} else {
		// not logged in
		echo "<div class=\"alert warning\">To register for matches you must be logged in.</div>";
	}

	if(isset($_SESSION['addqueuesuccess'])){
		// Registration was successful
		if($_SESSION['addqueuesuccess']){
			echo "<div class=\"alert info\">Queued for match! Only register for matches if you can attend. You will be notified by email if you are added to the roster.</div>";
		}
		// Registration failed
		else {
			echo "<div class=\"alert warning\">Sorry, an error has occurred and your queue request could not be completed. Please try again at a later time.</div>";
		}
		unset($_SESSION['addqueuesuccess']);
	}

	if(isset($_SESSION['addcaptainsuccess'])){
		// Registration was successful
		if($_SESSION['addcaptainsuccess']){
			echo "<div class=\"alert info\">You are now the captain!</div>";
		}
		// Registration failed
		else {
			echo "<div class=\"alert warning\">Sorry, an error has occurred and your request could not be completed. Please try again at a later time.</div>";
		}
		unset($_SESSION['addcaptainsuccess']);
	}

	if(isset($_SESSION['removequeuesuccess'])){
		// Registration was successful
		if($_SESSION['removequeuesuccess']){
			echo "<div class=\"alert info\">You were removed from the queue!</div>";
		}
		// Registration failed
		else {
			echo "<div class=\"alert warning\">Sorry, an error has occurred and your queue request could not be completed. Please try again at a later time.</div>";
		}
		unset($_SESSION['removequeuesuccess']);
	}

	if(isset($_SESSION['removecaptainsuccess'])){
		// Registration was successful
		if($_SESSION['removecaptainsuccess']){
			echo "<div class=\"alert info\">You are no longer the captain!</div>";
		}
		// Registration failed
		else {
			echo "<div class=\"alert warning\">Sorry, an error has occurred and your request could not be completed. Please try again at a later time.</div>";
		}
		unset($_SESSION['removecaptainsuccess']);
	}

	// display errors set in '/include/session.php' e.g. no places left
    if($form->num_errors > 0){
    	if($form->error("username")){
    		echo "<div class=\"alert warning\">".$form->error("username")."</div>";
    	}
    }

    $session->getNextMatch(); // display user's next session

    // show upcoming sessions
    include("view/display_matches_info.php");
    displayMatches(10,1); // 10 sessions, 1 = in the future

?>


    </div>
</section>