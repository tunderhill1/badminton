<?php   // Session class simplifies tracking users
include("database.php");
include("mailer.php");
include("form.php");

class Session
{
	var $username;     //Username given on sign-up
	var $activated;    //Activated = 1. Unactivated = 0
	var $userid;       //Random value generated on current login
	var $userlevel;    //The level to which the user pertains
	var $logged_in;    //True if user is logged in, false otherwise
	var $userinfo = array();  //The array holding all user info
	var $url;          //The page url current being viewed
	var $referrer;     //Last recorded site page viewed
	/** Note: referrer should really only be considered the actual
	* page referrer in process.php, any other time it may be
	* inaccurate. */

	// Class constructor
	function Session(){
		session_name("icbc_errs"); // default PHP session name
		session_set_cookie_params(COOKIE_EXPIRE, COOKIE_PATH);
		$this->startSession();
	}

	/** initialises this session object based on if user is logged on **/
	function startSession(){
		global $database;  //The database connection

		session_start();   // start PHP auto-session

		// check for cookie & if valid
		if(isset($_COOKIE['icbc'])){
			$this->logged_in = $this->checkLogin();
		}else{
			$this->logged_in = 0;
		}

		// if no (or bad) cookie
		if(!$this->logged_in){
			unset($this->username);
			unset($this->userid);

			// check if at Imperial
			$ip7 = substr(htmlentities(mysql_real_escape_string($_SERVER['REMOTE_ADDR'])),0,7);
			if($ip7=="155.198" || $ip7=="144.169" || $ip7=="129.31."){
				$this->userlevel = LEVEL_GUEST;
			}else{
				$this->userlevel = LEVEL_NULL;
			}
		}

		// set referrer page
		// this "magic" combination works! don't play with it!
		if(isset($_SESSION['url'])){
			$this->referrer = $_SESSION['url'];
		}else{
			$this->referrer = "index.php";
		}
        $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF']; // current url, excluding the shoutbox

		// $this->logged_in = true;
		// $this->userlevel = 4;
		// $this->activated = 1;
		// $this->username = 'qf316';
	}


	function checkLogin(){
		global $database;

		// escape data. never trust user input!
		$cook = htmlentities(mysql_real_escape_string($_COOKIE['icbc']));

		// check for bad cookie
		if(!$this->checkCookie($cook)){
			// unset cookies - not necessary - but annoys people playing (hacking) cookies
			setcookie("icbc", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
			setcookie("is_admin", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
			return false;
		}

		// user logged in, set class variables
		$this->userid = substr($cook,20,44);
		$this->username = $database->unameFromCookUID($this->userid);
		$this->userinfo  = $database->getUserInfo($this->username);
		$this->userlevel = $this->userinfo['usr_lvl'];
		$this->activated = $this->userinfo['active'];

		return true;
	}

	function checkCookie($cook){
		global $database;

		// username check. will also fail if UID doesnt exist
		if( $this->HashUsername($database->unameFromCookUID(substr($cook,20,44))) != substr($cook,0,20) ){return 0;}

		// user agent check
		if( $this->HashAgent($_SERVER['HTTP_USER_AGENT']) != substr($cook,79,15) ){return 0;}

		// IP check
		if( $this->HashIP($_SERVER['REMOTE_ADDR']) != substr($cook,64,15) ){return 0;}

		return 1;
	}


	// I don't trust the previous person who coded this to properly escape strings,
	// so I'll pass in another password which is only to be used for pam_auth() - provided by the union
	function login($subuser, $subpass, $subremember, $unsanitisedPassword){
		global $database, $form;  //The database and form object

		// check login
		$result = $database->manualLogin($subuser,$this->HashPwd($subpass));

		// If user has been banned from social sessions for right amount of time, unban
		$database->updateBanStatus($subuser);

		// COMMENT OUT BEFORE LOCAL TESTING
		if(!$result){
			$result = pam_auth($subuser,$unsanitisedPassword); // global function supplied by IC Union sysadmin
		}
		
		// if still no result = error
		if(!$result){
			$form->setError("login", "Invalid username or password.");
			return false;
		}

		// if not in Users table, add
		if(!$database->userExists($subuser)){
			$database->autoCreateUser($subuser);
		}
		//UNTIL HERE

		/* Username and password correct, register session variables */
		$this->userinfo  = $database->getUserInfo($subuser);
		$this->username  = $subuser;
		$this->userid    = $this->generateRandStr(44);
		$this->userlevel = $this->userinfo['usr_lvl'];

		// create secure cookie
		$this->icbc_cookie =  $this->HashUsername($this->username) . $this->userid . $this->HashIP($_SERVER['REMOTE_ADDR']) . $this->HashAgent($_SERVER['HTTP_USER_AGENT']);

		$database->updateUserField($this->username, "usr_id", $this->userid);

		/* set cookies to keep user logged in
		1. is_admin is a fake cookie to trick would-be "hackers", making it "true" triggers die.php in root dir
		2. the first IF statement forces cookies over HTTPS when HTTPS is being used */
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']!="off"){ $secure=true; }else{ $secure=false; }
		if($subremember){
			setcookie("icbc", $this->icbc_cookie, time()+COOKIE_EXPIRE, COOKIE_PATH, "", $secure, true);
			setcookie("is_admin", 0, time()+COOKIE_EXPIRE, COOKIE_PATH, "", $secure, true);
		}else{
			// cookie expire=0 = destroy when browser closes
			setcookie("icbc", $this->icbc_cookie, 0, COOKIE_PATH, "", $secure, true);
			setcookie("is_admin", 0, 0, COOKIE_PATH, "", $secure, true);
		}
		return true;
	}

	/** changes to hash function need to be reflected in database.php ! **/

	function HashPwd($pwd){
		$salt1 = "8u84h5r8w3u4n5g0o83uh".$pwd."oiu43g5ilj34";
		$hash1 = md5($salt1); // basic (weak-ish) md5 hash
		$salt2 = "k3jqbzcx-ur6904nfq".$hash1."!lqk3jnfq34qplzmx"; // then salt that
		return sha1($salt2); // before returning a sha-2 hash of md5+salt
	} // security complete! db obfustication in: procManualCreateUser()

	function HashUsername($un){
		$un_salt = "!*sfdhas�$^".strtoupper($un)."asdfjhZACKWOZHEREdghaskgjb";
		return substr(sha1($un_salt),9,20);
	}

	function HashIP($ip){
		$ip_salt = "lkjh!awe23498".$ip.";lkjasd;flk";
		return substr(sha1($ip_salt),13,15);
	}

	function HashAgent($agent){
		$agent_salt = "zm,xbvoiaurh".$agent."39ruqeih4y3897...";
		return substr(md5($agent_salt),14,15);
	}

	function logout(){
		global $database;

		if(isset($_COOKIE['icbc'])){
			setcookie("icbc", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
			setcookie("is_admin", "", time()-COOKIE_EXPIRE, COOKIE_PATH);
		}

		// Reflect fact that user has logged out
		$this->logged_in = false;
		$this->userlevel = LEVEL_NULL;
		unset($this->username);
	}

	function addNewsPost($news_title, $news_article, $news_author, $news_type){
		global $database, $mailer;  //The database, form and mailer object

		/* Title error checking */
		$field = "title";  //Use field name for title
		if(!$news_title || strlen($news_title = trim($news_title)) == 0){
			$form->setError($field, "Title was not entered");
		}

		/* Comment  error checking */
		$field = "article";  //Use field name for title
		if(!$news_article || strlen($news_articlet = trim($news_article)) == 0){
			$form->setError($field, "A comment was not entered");
		}

		$news_article = addslashes($news_article);
		$news_article = strip_tags($news_article, '<p><a><b><i><strong><emphasis><ol><ul><li>');

		if($form->num_errors > 0){
			return 1;  // error
		}else{
			if($database->createNewsPost($news_title, $news_article, $news_author, $news_type)){
				return 0;  // news post submitted succesfully
			}else{
				return 2;  // news post attempt failed
			}
		}
	}

	function edNewsPost($nid, $tit, $artic, $newstype){
		global $database;
		if($database->editNewsPost($nid, $tit, $artic, $newstype)) {
			return 0;  //News post edited succesfully
		}else{
			return 2;  //News post edit attempt failed
		}
	}

	function addSession($day, $month, $year, $starthour, $startmin, $endhour, $endmin, $location, $places, $managers,$training_details){
		global $database;  //The database object

		$start = $year ."-". $month ."-". $day ." ". $starthour .":". $startmin .":00";
		$end = $year ."-". $month ."-". $day ." ". $endhour .":". $endmin .":00";
    $startTime = strtotime($start);
    $endTime = strtotime($end);

    //Check valid start and end time. Check start time before end time
    if($startTime === false || $endTime === false || $endTime < $startTime) {
      return 1;
    }
		if($database->createSession($start, $end, $location, $places, $managers,$training_details)){
			return 0;  //Session submitted succesfully
		}else{
			return 2;  //Add Session attempt failed
		}
	}

	function edSession($s_id, $s_start, $s_end, $loc, $plac,$managers,$training_details) {
		global $database;  //The database object

		if($database->editSession($s_id, $s_start, $s_end, $loc, $plac,$managers,$training_details)) {
			return 0;  //Session edited succesfully
		} else {
			return 2;  //Edit Session attempt failed
		}
	}


	function delSession($sid) {
		global $database;  //The database object

		if($database->deleteSession($sid)) {
			return 0;  //Session deleted succesfully
		} else {
			return 2;  //Delete Session attempt failed
		}
	}


	function addBooking($username, $session_id){
		global $database, $form, $mailer;  //The database, form and mailer object

		$req_session_info = $database->getSessionInfo($session_id);
		$field = "username";  //Use field name for username

		// prevent not logged in users from using this function
		if($this->logged_in == false){	$form->setError($field, "Please log in to book.");	}

		// prevent un-activated users from booking
		if($this->activated == false){ $form->setError($field, "Your account is not yet activated.");	}

		$places = $req_session_info['places']; // check if there are enough places left
		$places_booked = $database->getNumSessionBookings($session_id);
		$places_left = $places - $places_booked;
		if($places_left <= 0){
			$form->setError($field, "Sorry, but there are no places available in this session.");
		}

		// check if session has been locked
		$session_start_timestamp = strtotime($req_session_info['session_start']);
		$sessionDayNum = date("w", $session_start_timestamp);
		$current_time = time();
		$time_difference = $session_start_timestamp - $current_time;
		if($time_difference < SESSION_LOCKING_TIME){
			$form->setError($field, "You could not be added to this session because it has been locked.");
		}
		// Different locking time for training sessions
		if($sessionDayNum == 0 &&
			$time_difference < TRAINING_LOCKING_TIME){
				$form->setError($field, "You could not be added to this session because it has been locked.");	
		}
		// check if booking in too-far-future
		$advance_time = $current_time + SESSION_BOOKING_ADVANCE;
		if($session_start_timestamp > $advance_time){
			$form->setError($field, "You cannot book session more than 28 days in advance.");
		}

		/* Check if username has previously booked a session */
		// if($database->isUserAlreadyBooked($this->username,$req_session_info['location'])){
		// 	$form->setError($field, "You have already been booked for an upcoming session.");
		// }

		$dayOfWeek = [
			"Monday",
			"Tuesday",
			"Wednesday",
			"Thursday",
			"Friday",
			"Saturday",
			"Sunday",
		];

		/* Check if user wants to book more than 1 non-training (Monday and Tuesday) session */
		if ($sessionDayNum != 0 &&
			$database->isUserAlreadyBookedNonTraining($this->username)) {
			$form->setError($field, "You have already been booked for a non-coaching session.");
		}

		/* Check if user has booked */
		// if ($database->isUserAlreadyBooked($this->username, $sessionDayNum)) {
		// 	$form->setError($field, "You have already been booked for a {$dayOfWeek[$sessionDayNum]} session.");
		// }

		/* Check if user has booked more than 1 training (Sunday) session */
		if ($sessionDayNum == 0 &&
			$database->isUserAlreadyBookedTraining($this->username)) {
			$form->setError($field, "You have already been booked for a coaching session.");
		}

		// cannot play the same session twice!
		if($database->isUserBookedToSession($this->username,$session_id)){
			$form->setError($field, "You are already playing in this sessions!");
		}

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}

		/* No errors, add the new account to the */
		else{
			$booking_date = date("Y-m-d H:i:s");
			if($database->createBooking($username, $session_id, $booking_date)){
				return 0;  //New user added succesfully
			}else{
				return 2;  //Registration attempt failed
			}
		}
	}


	function addManager($username, $session_id){
		global $database, $form, $mailer;  //The database, form and mailer object

		$req_session_info = $database->getSessionInfo($session_id);
		$field = "username";  //Use field name for username

		// check logged in
		if(!$this->logged_in){	$form->setError($field, "Please log in to book.");	}

        // check if activated
        if($this->activated == false){ $form->setError($field, "Your account is not yet activated.");	}

		// check they are actually a manager
		if($this->userlevel < LEVEL_MEMBER){	$form->setError($field, "Please log in to book");	}

		// managers can only manage 2 sessions at any time
		if($database->isUserAtManageLimit($this->username)){	$form->setError($field, "You are already managing a session.");	}

		// cannot manage the same session twice!
		if($database->isUserBookedToManage($this->username,$session_id)){	$form->setError($field, "You are already managing this sessions!");	}

		// maximum managers check
		$places = $req_session_info['managers'];
		$places_booked = $database->getNumManagers($session_id);
		$places_left = $places - $places_booked;
		if($places_left <= 0){
			$form->setError($field, "Maximum number of managers has been reached.");
		}

		// check session is not locked
		$session_start_timestamp = strtotime($req_session_info['session_start']);
		$current_time = time();
		$advance_time = $current_time + SESSION_BOOKING_ADVANCE;
		if($session_start_timestamp > $advance_time){
			$form->setError($field, "You cannot manage session more than 28 days in advance.");
		}

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}else{
			if ($database->createManager($this->username, $session_id)){
				return 0;  // success
			}else{
				return 2;  // attempt failed
			}
		}
	}


	function removeBooking($username, $session_id){
		global $database, $form, $mailer;  //The database, form and mailer object

		$req_session_info = $database->getSessionInfo($session_id);
		$field = "username";  //Use field name for username

		if(!$database->isUserBookedToSession($username,$session_id)){
			$form->setError($field, "Cannot remove. You are not signed into it.");
		}

		/* Check if session has been locked */
		$session_start = strtotime($req_session_info['session_start']);
		$current_time = time();
		$time_difference = $session_start - $current_time;
		$sessionDayNum = date("w", $session_start);
		/* Check if the time is less than the locking time */
		if($time_difference < SESSION_LOCKING_TIME){
			$form->setError($field, "You could not be removed from this session because it has been locked.");
		}
		if($sessionDayNum == 0 &&
			$time_difference < TRAINING_LOCKING_TIME){
			$form->setError($field, "You could not be removed from this session because it has been locked.");
		}
		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}else{
			if($database->deleteBooking($username, $session_id)){
				return 0;
			}else{
				return 2; // remove fail
			}
		}
	}


	function removeManager($username, $session_id){
		global $database, $form, $mailer;

		$req_session_info = $database->getSessionInfo($session_id);
		$field = "username";

		if(!$database->isUserBookedToManage($username,$session_id)){
			$form->setError($field, "Cannot remove. You're not managing this session.");
		}

		$session_start = strtotime($req_session_info['session_start']); // check if locked
		$current_time = time();
		$time_difference = $session_start - $current_time;
		if($time_difference < SESSION_LOCKING_TIME){
			$form->setError($field, "You could not un-manage this session because it has been locked.");
		}

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  // Errors
		}else{
			if($database->deleteManager($username, $session_id)){
				return 0;  // succesfully
			}else{
				return 2;  // failed
			}
		}
	}

	/* Submit noshows */
	/*
	function registerNoshows($usernames, $session_id){
		global $database, $form, $mailer;

		$session_date = $database->getSessionDatefromID($session_id);

		for($i=0; $i<sizeof($usernames); $i++){
			$un = $usernames[$i];
			$name_array = $database->getNamefromUsername($un);
			$firstname = $name_array['firstname'];
			$lastname = $name_array['lastname'];
			$email = $database->getEmailfromUsername($un);

			$status = $database->getUserNoshowStatus($un);

			if ($status == "none") {
				$action = 'warning';
			} else if ($status == "warning") {
				$action = 'ban';
				$database->updateUserField($un, 'usr_lvl', LEVEL_GUEST);
				$database->removeUserFromFutureSessions($un);
			}
			
			if (!$database->addNoshow($session_id, $session_date, $un, $firstname, $lastname, $action)) {
				$form->setError("noshow", "Could not add noshow to database.");
			}
			if (!$mailer->sendBookingWarning($name_array, $email, $session_date)){
				$form->setError("email", "Could not email ".$name_array." at ".$email);
			}
		}

		if ($form->num_errors > 0) {
			return 1; // Errors
		} else {
			return 0; // No errors
		}
	}
	*/

	/*
	
	---------------------------
	Social match functions
	---------------------------

	*/

	function addMatch($day, $month, $year, $starthour, $startmin, $opponent, $location, $places, $match_details){
		global $database;  //The database object

		$start = $year ."-". $month ."-". $day ." ". $starthour .":". $startmin .":00";
    	$startTime = strtotime($start);

    //Check valid start and end time. Check start time before end time
    if($startTime === false) {
      return 1;
    }
		if($database->createMatch($start, $opponent, $location, $places, $match_details)){
			return 0;  //Match submitted succesfully
		}else{
			return 2;  //Add match attempt failed
		}
	}

	function edMatch($m_id, $m_start, $opponent, $loc, $plac, $match_details) {
		global $database;  //The database object

		if($database->editMatch($m_id, $m_start, $opponent, $loc, $plac, $match_details)) {
			return 0;  //Match edited succesfully
		} else {
			return 2;  //Edit match attempt failed
		}
	}

	function delMatch($mid) {
		global $database;  //The database object

		if($database->deleteMatch($mid)) {
			return 0;  //Session deleted succesfully
		} else {
			return 2;  //Delete Session attempt failed
		}
	}


	function addQueue($username, $match_id){
		global $database, $form, $mailer;  //The database, form and mailer object

		$req_match_info = $database->getMatchInfo($match_id);
		$field = "username";  //Use field name for username

		// prevent not logged in users from using this function
		if($this->logged_in == false){	$form->setError($field, "Please log in to queue.");	}

		// prevent un-activated users from booking
		if($this->activated == false){ $form->setError($field, "Your account is not yet activated.");	}

		// cannot play the same match twice!
		if($database->isUserBookedToMatch($this->username,$match_id)){
			$form->setError($field, "You are already booked to this match!");
		}

		// Errors exist, have user correct them
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}

		// No errors, add the new account to the match
		else{
			$booking_date = date("Y-m-d H:i:s");
			if($database->createQueue($username, $match_id, $booking_date)){
				return 0;  //New user added succesfully
			}else{
				return 2;  //Registration attempt failed
			}
		}
	}


	function addCaptain($username, $match_id){
		global $database, $form, $mailer;  //The database, form and mailer object

		$req_match_info = $database->getMatchInfo($match_id);
		$field = "username";  //Use field name for username

		// check logged in
		if(!$this->logged_in){	$form->setError($field, "Please log in to book.");	}

        // check if activated
        if($this->activated == false){ $form->setError($field, "Your account is not yet activated.");	}

		// check they are actually a member
		if($this->userlevel < LEVEL_MEMBER){	$form->setError($field, "Please log in to book");	}

		// cannot play in the same match twice!
		if($database->isUserBookedToMatch($this->username,$match_id)){	$form->setError($field, "You are already playing in this match!");	}

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}else{
			$booking_date = date("Y-m-d H:i:s");
			if ($database->createCaptain($this->username, $match_id, $booking_date)){
				return 0;  // success
			}else{
				return 2;  // attempt failed
			}
		}
	}


	function removeQueue($username, $match_id){
		global $database, $form, $mailer;  //The database, form and mailer object

		$req_session_info = $database->getMatchInfo($match_id);
		$field = "username";  //Use field name for username

		if(!$database->isUserBookedToMatch($username,$match_id)){
			$form->setError($field, "Cannot remove. You are not queued into it.");
		}

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}else{
			if($database->deleteQueue($username, $match_id)){
				return 0;
			}else{
				return 2; // remove fail
			}
		}
	}


	function removeCaptain($username, $match_id){
		global $database, $form, $mailer;

		$req_session_info = $database->getMatchInfo($match_id);
		$field = "username";

		if(!$database->isUserBookedToMatch($username,$match_id)){
			$form->setError($field, "Cannot remove. You're not captain in this match.".$username." ".$match_id);
		}

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  // Errors
		}else{
			if($database->deleteCaptain($username, $match_id)){
				return 0;  // succesfully
			}else{
				return 2;  // failed
			}
		}
	}

	function removePlaying($username, $match_id){
		global $database, $form, $mailer;  //The database, form and mailer object

		$req_session_info = $database->getMatchInfo($match_id);
		$field = "username";  //Use field name for username

		if(!$database->isUserBookedToMatch($username,$match_id)){
			$form->setError($field, "Cannot remove. You are not on the team.");
		}

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}else{
			if($database->deletePlayer($username, $match_id)){
				return 0;
			}else{
				return 2; // remove fail
			}
		}
	}

/**
* newspost - Gets called when the user has just submitted a
* news post. Determines if there were any errors with
* the entry fields, if so, it records the errors and returns
* 1. If no errors were found, it submits the new post and
* returns 0. Returns 2 if news post submission falied.
*/
	function addComment($news_id, $comment, $username){
		global $database, $form, $mailer;  //The database, form and mailer object

		/* Comment  error checking */
		$field = "title";  //Use field name for title
		if(!$comment || strlen($comment = trim($comment)) == 0){
			$form->setError($field, "A comment was not entered");
		}

		$comment = addslashes($comment);
		$comment = strip_tags($comment, '<p><a><b><i><strong><emphasis><ol><ul><li>');
		$comment = nl2br($comment);

		$comment = mysql_real_escape_string($comment);
		$news_id = mysql_real_escape_string($news_id);

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}
		/* No errors, add the new account to the */
		else{
			if($database->createCommentEntry($news_id, $comment, $username)){
				return 0;  //New comment added succesfully
			}else{
				return 2;  //Registration attempt failed
			}
		}
	}

	function editComment($comment_id, $comment){
		global $database, $form, $mailer;  //The database, form and mailer object

		/* Comment  error checking */
		$field = "title";  //Use field name for title
		if(!$comment || strlen($comment = trim($comment)) == 0){
			$form->setError($field, "A comment was not entered");
		}

		$comment = addslashes($comment);
		$comment = strip_tags($comment, '<p><a><b><i><strong><emphasis><ol><ul><li>');
		$comment = nl2br ($comment);

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}else{
			if($database->updateCommentEntry($comment_id, $comment)){
				return 0;  //succes
			}else{
				return 2;  // failed
			}
		}
	}

	/**
* newspost - Gets called when the user has just submitted a
* news post. Determines if there were any errors with
* the entry fields, if so, it records the errors and returns
* 1. If no errors were found, it submits the new post and
* returns 0. Returns 2 if news post submission falied.
*/
	function deleteComment($comment_id){
		global $database, $form, $mailer;  //The database, form and mailer object

		/* Errors exist, have user correct them */
		if($form->num_errors > 0){
			return 1;  //Errors with form
		}

		/* No errors, add the new account to the */
		else{
			if($database->deleteCommentEntry($comment_id)){
				return 0;  //New user added succesfully
			}
			else{
				return 2;  //Registration attempt failed
			}
		}
	}

	function getNextSession(){
		if($this->logged_in){
			global $database;
			$dat = date('Y-m-d H:i:s');
			$q = "SELECT * FROM session_list sl, session_booking sb WHERE username='$this->username' AND session_start >= CURRENT_TIMESTAMP AND sl.session_id = sb.session_id AND NOT deleted = '1' ORDER BY sl.session_start ASC";
			$result = mysql_query($q, $database->connection);
			if(!$result || (mysql_num_rows($result) < 1)){
				echo "<p>You are not booked for any sessions.</p>";
			}else{
				$output = "<p>You are booked for:<br>";
				for($i = 0; $i < mysql_num_rows($result); $i++){
					$r = mysql_fetch_array($result, $i);
					$date = date("D jS M, H:i", strtotime($r['session_start']));
					$sessid = $r['session_id'];
					$output .= "<strong>".$date."</strong>, <a href=\"".SITE_URL."/sessions/view/index.php?sid=".$sessid."\">view</a><br>";
				}
				$output .= "</p>";
				echo $output;
			}
		}
	}

	function getNextMatch(){
		if($this->logged_in){
			global $database;
			$dat = date('Y-m-d H:i:s');
			$q = "SELECT * FROM ".TBL_MATCH_LIST." sl, ".TBL_MATCH_BOOKING." sb WHERE username='$this->username' and match_start > '$dat' and sl.match_id = sb.match_id AND NOT deleted = '1'";
			$result = mysql_query($q, $database->connection);
			if(!$result || (mysql_num_rows($result) < 1)){
				echo "<p>You are not booked for any sessions.</p>";
			}else{
				$r = mysql_fetch_array($result);
				$date = date("D jS M, H:i", strtotime($r['match_start']));
				$mid = $r['match_id'];
				echo "<p>You are booked for: <strong>".$date."</strong>, <a href=\"".SITE_URL."/matches/view/index.php?mid=".$mid."\">view</a></p>";
			}
		}
	}

	// random string chars (low+up case), and digits,
	// md5 hash it, return as userid
	// same as generateRandID() but with length param
	function generateRandStr($length){
		$randstr = "";
		for($i=0; $i<$length; $i++){
			$randnum = mt_rand(0,61);
			if($randnum < 10){
				$randstr .= chr($randnum+48);
			}else if($randnum < 36){
				$randstr .= chr($randnum+55);
			}else{
				$randstr .= chr($randnum+61);
			}
		}
		return $randstr;
	}

}; // end session class
$session = new Session; // session initialized before form object
$form = new Form; // Initialize form object
?>
