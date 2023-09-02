<?php 
/* The AdminProcess class is meant to simplify the task of processing
* admin submitted forms from the admin center, these deal with
* member system adjustments.  */

include("../include/session.php");

class AdminProcess
{
	/* Class constructor */
	function AdminProcess(){
		global $session;
		// Make sure administrator is accessing page
		if(!$session->userlevel >= LEVEL_ADMIN){
			header("Location: " . SITE_URL);
			return;
		}
		/* Admin submitted create session */
		else if(isset($_POST['subcreatesession'])){
			$this->procCreateSession();
		}
		/* Admin submitted update user level form */
		else if(isset($_POST['subupdlevel'])){
			$this->procUpdateLevel();
		}
		/* Admin submitted activate user */
		else if(isset($_POST['subactuser'])){
			$this->procActivateUser();
		}
		/* Admin submitted edit session form */
		else if(isset($_POST['subeditsession'])){
			$this->procEditSession();
		}
		/* Admin submitted delete session form */
		else if(isset($_POST['subdelsession'])){
			$this->procDeleteSession();
		}
		/* Admin submitted create match */
		else if(isset($_POST['subcreatematch'])){
			$this->procCreateMatch();
		}
		/* Admin submitted delete match form */
		else if(isset($_POST['subdelmatch'])){
			$this->procDeleteMatch();
		}
		/* Admin submitted edit match form */
		else if(isset($_POST['subeditmatch'])){
			$this->procEditMatch();
		}
		/* Should not get here, redirect to home page */
		else{
			header("Location: " . SITE_URL);
		}
	}


	function procCreateSession(){
		global $session, $form;

		/* Create session attempt */
		$retval = $session->addSession( htmlentities(mysql_real_escape_string($_POST['day'])),
								htmlentities(mysql_real_escape_string($_POST['month'])),
								htmlentities(mysql_real_escape_string($_POST['year'])),
								htmlentities(mysql_real_escape_string($_POST['starthour'])),
								htmlentities(mysql_real_escape_string($_POST['startmin'])),
								htmlentities(mysql_real_escape_string($_POST['endhour'])),
								htmlentities(mysql_real_escape_string($_POST['endmin'])),
								htmlentities(mysql_real_escape_string($_POST['location'])),
								htmlentities(mysql_real_escape_string($_POST['places'])),
								htmlentities(mysql_real_escape_string($_POST['managers'])),
								htmlentities(mysql_real_escape_string($_POST['trad']))
								);

		/* Create session Successful */
		if($retval == 0){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Create session attempt failed */
		else if($retval == 2){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}


	function procEditSession() {
		global $session, $form;

		/* Edit session attempt */
		$retval = $session->edSession( htmlentities(mysql_real_escape_string($_POST['s_id'])),
									htmlentities(mysql_real_escape_string($_POST['s_start'])),
									htmlentities(mysql_real_escape_string($_POST['s_end'])),
									htmlentities(mysql_real_escape_string($_POST['loc'])),
									htmlentities(mysql_real_escape_string($_POST['plac'])),
									htmlentities(mysql_real_escape_string($_POST['managers'])),
									htmlentities(mysql_real_escape_string($_POST['trad']))
									);

		/* Edit session Successful */
		if($retval == 0){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Edit session attempt failed */
		else if($retval == 2){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}


	function procDeleteSession() {
		global $session, $form;

		/* Edit session attempt */
		$retval = $session->delSession(mysql_real_escape_string($_POST['sid']));

		/* Edit session Successful */
		if($retval == 0){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Edit session attempt failed */
		else if($retval == 2){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	//MATCHES
	function procCreateMatch(){
		global $session, $form;

		/* Create match attempt */
		$retval = $session->addMatch( htmlentities(mysql_real_escape_string($_POST['day'])),
								htmlentities(mysql_real_escape_string($_POST['month'])),
								htmlentities(mysql_real_escape_string($_POST['year'])),
								htmlentities(mysql_real_escape_string($_POST['starthour'])),
								htmlentities(mysql_real_escape_string($_POST['startmin'])),
								htmlentities(mysql_real_escape_string($_POST['opponent'])),
								htmlentities(mysql_real_escape_string($_POST['location'])),
								htmlentities(mysql_real_escape_string($_POST['places'])),
								htmlentities(mysql_real_escape_string($_POST['trad']))
								);

		/* Create session Successful */
		if($retval == 0){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Create session attempt failed */
		else if($retval == 2){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}


	function procEditMatch() {
		global $session, $form;

		/* Edit session attempt */
		$retval = $session->edMatch( htmlentities(mysql_real_escape_string($_POST['m_id'])),
									htmlentities(mysql_real_escape_string($_POST['m_start'])),
									htmlentities(mysql_real_escape_string($_POST['opp'])),
									htmlentities(mysql_real_escape_string($_POST['loc'])),
									htmlentities(mysql_real_escape_string($_POST['plac'])),
									htmlentities(mysql_real_escape_string($_POST['trad']))
									);

		/* Edit session Successful */
		if($retval == 0){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Edit session attempt failed */
		else if($retval == 2){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}


	function procDeleteMatch() {
		global $session, $form;

		/* Edit session attempt */
		$retval = $session->delMatch(mysql_real_escape_string($_POST['mid']));

		/* Edit session Successful */
		if($retval == 0){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Edit session attempt failed */
		else if($retval == 2){
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['newspostsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procUpdateLevel(){
		global $database, $session;
		$database->updateUserField( htmlentities( mysql_real_escape_string($_POST['upduser']) ), 'usr_lvl', htmlentities(mysql_real_escape_string((int)$_POST['updlevel'])));
		header("Location: ".$session->referrer);
	}


	// also used to de-activate users
	function procActivateUser(){
		global $session, $database, $form;

		$user = htmlentities(mysql_real_escape_string($_POST['actuser']));

		if($database->userExists($user)){
			$act = (int)$_POST['activate'];

			// activate the account and if non-admin then update userlevel
			// (i.e. if already got admin userlevel, then don't modify it)
			if($act==1){
				$database->updateUserField($user, 'active', '1');

				$info = $database->getUserInfo($user);
				if($info['usr_lvl']==LEVEL_GUEST){
					$database->updateUserField($user, 'usr_lvl', LEVEL_MEMBER);
				}

			}
			// otherwise if de-activating
			elseif($act==0){
				$database->updateUserField($user, 'active', '0');
				$database->updateUserField($user, 'usr_lvl', LEVEL_GUEST);
			}

			$_SESSION['actusersuccess'] = true; // do success msg, if one $database call worked, all should
		}else{
			$_SESSION['actusersuccess'] = false; // if username doesn't exist
		}
		header("Location: ".$session->referrer);
	}

};
$adminprocess = new AdminProcess; // initialise process
?>
