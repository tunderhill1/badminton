<?php

/**
* The Process class is meant to simplify the task of processing
* user submitted forms, redirecting the user to the correct
* pages if errors are found, or if form is successful, either
* way. Also handles the logout procedure.
*/

include("session.php");

class Process
{
	/* Class constructor */
	function Process(){
		global $session;
		/* User submitted login form */
		if(isset($_POST['sublogin'])){
			$this->procLogin();
		}
		/* Registration form */
		else if(isset($_POST['subjoin'])){
			$this->procManualCreateUser();
		}

		/* Forgot password form */
		else if(isset($_POST['subforgot'])){
			$this->procForgotPass();
		}
		/* Edit account form */
		else if(isset($_POST['subaccountedit'])){
			$this->procEditAccount();
		}
		/* Edit profile form */
		else if(isset($_POST['subprofileedit'])){
			$this->procEditProfile();
		}
		/* Add booking form */
		else if(isset($_POST['addbooking'])){
			$this->procAddBooking();
		}
		/* Paying for coaching session form */
		else if(isset($_POST['paying'])){
			$this->procPaying();
		}
		/* Remove booking form */
		else if(isset($_POST['removebooking'])){
			$this->procRemoveBooking();
		}
		/* Remove manage form */
		else if(isset($_POST['removemanage'])){
			$this->procRemoveManage();
		}
		/* Add manager form */
		else if(isset($_POST['addmanager'])){
			$this->procAddManager();
		}
		/* Submit noshows */
		// else if(isset($_POST['noshow'])){
		// 	$this->procRegisterNoshow();
		// }
		/* Add queue form */
		else if(isset($_POST['addqueue'])){
			$this->procAddQueue();
		}
		/* Remove queue form */
		else if(isset($_POST['removequeue'])){
			$this->procRemoveQueue();
		}
		/* Remove captain form */
		else if(isset($_POST['removecaptain'])){
			$this->procRemoveCaptain();
		}
		/* Add captain form */
		else if(isset($_POST['addcaptain'])){
			$this->procAddCaptain();
		}
		/* Remove player form */
		else if(isset($_POST['removeplaying'])){
			$this->procRemovePlayer();
		}
		/* Add comment form */
		else if(isset($_POST['addcomment'])){
			$this->procAddComment();
		}
		/* Edit comment form */
		else if(isset($_POST['editcomment'])){
			$this->procEditComment();
		}
		/* Delete comment form */
		else if(isset($_POST['deletecomment'])){
			$this->procDeleteComment();
		}
		/* Contact us form */
		else if(isset($_POST['contactus'])){
			$this->procContactUs();
		}
		/** The only other reason user should be directed here
		* is if he wants to logout, which means user is
		* logged in currently.	*/
		else if($session->logged_in){
			$this->procLogout();
		}
		// Should not get here, error or hacker, so:
		else{
			header("Location: " . SITE_URL);
		}
	}

	function procLogin(){
		global $session, $database, $form;

		$un = htmlentities(mysql_real_escape_string($_POST['lguser']));
		$pwd = htmlentities(mysql_real_escape_string($_POST['lgpass']));

		// I don't trust the previous person who coded this to properly escape strings,
		// so I'll pass in another password which is only to be used for pam_auth() - provided by the union
		$retval = $session->login($un, $pwd, isset($_POST['remember']), $_POST['lgpass']);

		if($retval){ // login good
			// If user has been banned from social sessions for right amount of time, unban
			$database->updateBanStatus($un);
			
			// Redirect to bookings
			header("Location: " . SITE_URL . "/sessions");
		}else{ // login fail
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
	}

    function procPaying(){
        global $session, $form;
        header("Location: https://www.imperialcollegeunion.org/shop/student-groups/382", true, 301);
        exit();
    }

	function procLogout(){
		global $session;
		$session->logout();
		header("Location: ".$session->referrer);
	}


	function procManualCreateUser(){	// if user choses to register manually
		global $session, $form, $database;

		// recaptcha verification
    $subuser = htmlentities(mysql_real_escape_string($_POST['user']));
    $captcha = htmlentities(mysql_real_escape_string($_POST['g-recaptcha-response']));
    $privatekey = "6Lc3jRQTAAAAAD4zCtiPwdtbkUFRZo7cZceEd8dk"; // from registering on recaptcha site
    $resp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$privatekey."&response=".$captcha);
    $resp = json_decode($resp, true);

		if($resp["success"] == false){
			$form->setError("captcha", "Captcha was incorrect.");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$pwd1 = $session->HashPwd($_POST['pass']); // custom pwd hash in session.php
		$pwd2 = $session->generateRandStr(2) . $pwd1 . $session->generateRandStr(6); // add db obfustication (also in procEditAccount, and procForgotPass)

		$firstname = htmlentities(mysql_real_escape_string($_POST['firstname']));
		$secondname = htmlentities(mysql_real_escape_string($_POST['secondname']));
		$nickname = htmlentities(mysql_real_escape_string($_POST['nickname']));
		$user = htmlentities(mysql_real_escape_string($_POST['user']));
		$email = htmlentities(mysql_real_escape_string($_POST['email']));

		// first name error checking
		$field = "firstname";
		if(!$firstname || strlen($firstname = trim($firstname)) == 0){
			$form->setError($field, "First name was not entered.");
		}else{
			if(strlen($firstname) > 20){
				$form->setError($field, "First name is above 20 characters.");
			}
			// check if first name is not alphanumeric
			else if( $firstname != $_POST['firstname'] ){
				$form->setError($field, "Invalid first name. Must contain letters only.");
			}
		}

		// Second name error checking
		$field = "secondname";
		if(!$secondname || strlen($secondname = trim($secondname)) == 0){
			$form->setError($field, "Second name was not entered.");
		}else{
			if(strlen($secondname) > 20){
				$form->setError($field, "Second name above 20 characters.");
			}
			else if( $secondname != $_POST['secondname'] ){
				$form->setError($field, "Invalid second name. Must contain letters only.");
			}
		}

		// nickname (optional, can be 0) error checking
		$field = "nickname";
		if(strlen($nickname) > 15){
			$form->setError($field, "Nickname cannot be above 15 characters.");
		}
		else if( $nickname != $_POST['nickname'] ){
			$form->setError($field, "Invalid nickname. Must contain letters only.");
		}

		// username error checking
		$field = "user";
		if(!$user || strlen($user = trim($user)) == 0){
			$form->setError($field, "Username not entered");
		}else{
			if(strlen($user) < 4){
				$form->setError($field, "Username must be more than 4 characters.");
			}
			else if(strlen($user) > 20){
				$form->setError($field, "Username must be less than 20 characters.");
			}
			else if( $user != $_POST['user'] ){
				$form->setError($field, "Invalid username. Must contain alphanumeric characters only.");
			}
			else if($database->userExists($user)){
				$form->setError($field, "Username already in use.");
			}
		}

		// Password error checking
		$field = "pass";
		if(!$_POST['pass']){
			$form->setError($field, "Password not entered.");
		}else{
			if($_POST['pass']!=$_POST['pass2']){
				$form->setError($field, "Passwords do not match.");
			}
			else if(strlen($_POST['pass']) <= 6){
				$form->setError($field, "Password is too short. Must be at least 6 characters long.");
			}
		}

		// email error checking
		$field = "email";  //Use field name for email
		if(!$_POST['email'] || strlen($_POST['email'] = trim($_POST['email'])) == 0){
			$form->setError($field, "Email not entered.");
		}else if ($_POST['email']!=$_POST['email2']){
				$form->setError($field, "Emails do not match.");
		}

		if($form->num_errors > 0){
			// errors found
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}else{
			// no errors
			$retval = $database->manualCreateUser($user,$firstname,$secondname,$pwd2,$nickname,$email);
		}

		// Registration Successful
		if($retval == 1){
			$_SESSION['reguname'] = htmlentities(mysql_real_escape_string($_POST['user']));
			$_SESSION['regsuccess'] = true;
			header("Location: ".$session->referrer);
			return;
		}else{
			// register fail
			$_SESSION['reguname'] = htmlentities(mysql_real_escape_string($_POST['user']));
			$_SESSION['regsuccess'] = false;
			header("Location: ".$session->referrer);
			return;
		}
	}


	// edits user details, if supplied pwd is correct
	function procEditAccount(){
		global $session, $form, $database;

		$new_pwd = $_POST['newpass'];
		$new_pwd2 = $_POST['newpass2'];
		$new_email = htmlentities(mysql_real_escape_string($_POST['email']));
		$new_nickname = htmlentities(mysql_real_escape_string($_POST['nickname']));

		// check for correct password
		$user = $session->username;
		$result = pam_auth($user,$_POST['curpass']);
		if(!$result){
			$result = $database->manualLogin($user,$session->HashPwd($_POST['curpass']));
		}
		if(!$result){
			$form->setError("curpass", "The current password is not correct.");
		}
		if($form->num_errors > 0){
			// wrong password error
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		// update new password
		if($new_pwd!=""){
			$field = "newpass";
			if($new_pwd!=$new_pwd2){
				$form->setError($field, "New passwords do not match.");
			}
			if(strlen($new_pwd) <= 6){
				$form->setError($field, "New password must be at least 6 characters long.");
			}

			if( ! $form->num_errors > 0){
				// db obfustication - only use 3 chars as post-hash obfust. just to confuse any hacker
				$new_pass = $session->generateRandStr(2).$session->HashPwd($new_pwd).$session->generateRandStr(3);
				$database->updateUserField($user,"usr_pwd",$new_pass);
			}
		}

		// update email
		if($new_email!=""){
			$database->updateUserField($user,"alt_email",$new_email);
		}

		if($new_nickname!=""){
			if($new_nickname != $_POST['nickname']){
				$form->setError("newnick", "New nickname is invalid. Alphanumeric characters only.");
			}
			if( ! $form->num_errors > 0){
				$database->updateUserField($user,"nickname",$new_nickname);
			}
		}

		// delete nickname
		if($new_nickname=="!" || $new_nickname==" "){
			$database->updateUserField($user,"nickname", '');
		}

		if($form->num_errors > 0){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}else{
			$_SESSION['useredit'] = true;
			header("Location: ".$session->referrer);
			return;
		}
	}


	function procForgotPass(){
		global $database, $session, $mailer, $form;
	  $field = "user";  //Use field name for username
		$subuser = htmlentities(mysql_real_escape_string($_POST['user']));
    $captcha = htmlentities(mysql_real_escape_string($_POST['g-recaptcha-response']));
    $privatekey = "6Lc3jRQTAAAAAD4zCtiPwdtbkUFRZo7cZceEd8dk"; // from registering on recaptcha site
    $resp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$privatekey."&response=".$captcha);
    $resp = json_decode($resp, true);

		if($resp["success"] == false){
			$form->setError("user", "Captcha was incorrect.");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		if(!$subuser || strlen($subuser = trim($subuser)) == 0){
			$form->setError($field, "Username not entered<br>");
		}else{
			if(!$database->userExists($subuser)){
				$form->setError($field, "Username does not exist<br>");
			}
		}

		// if account is not manually created, then cannot use pwd reset sys
		// slightly confusing for people playing with pwd reset system, but gets the message across
		if(!$database->hasManualPassword($subuser)){
			$form->setError($field, "Username does not exist<br>");
		}

		if($form->num_errors > 0){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
		}else{
			$newpass = $session->generateRandStr(10);	// new pass
			$email  = $database->getEmailfromUsername($subuser);

			// attempt to send the email with new password
			if($mailer->sendNewPass($subuser,$email,$newpass)){
				$pwd1 = $session->HashPwd($newpass); // custom pwd hash
				$pwd2 = $session->generateRandStr(2) . $pwd1 . $session->generateRandStr(6); // db obfus.

				$database->updateUserField($subuser, "usr_pwd", $pwd2);
				$_SESSION['forgotpass'] = true;
			}else{
				$_SESSION['forgotpass'] = false; // email fail, do not change password
			}
		}
		header("Location: ".$session->referrer);
	}


	function procAddBooking(){
		global $session, $form;

		$uname = $session->username;
		$session_id = htmlentities(mysql_real_escape_string($_POST['sessionid']));
		if((string)$session_id === (string)(int)$session_id){/**/}else{
			$form->setError("username", "Please log in to book");	// confusing err msg to confuse "hackers" who send fake session_id
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$retval = $session->addBooking($uname,$session_id);

		/* Registration Successful */
		if($retval == 0){
			$_SESSION['addbookingsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Registration attempt failed */
		else if($retval == 2){
			$_SESSION['addbookingsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procAddManager(){
		global $session, $form;

		$uname = $session->username;
		$session_id = htmlentities(mysql_real_escape_string($_POST['sessionid']));
		if((string)$session_id === (string)(int)$session_id){/**/}else{
			$form->setError("username", "Please log in to book");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$retval = $session->addManager($uname,$session_id);

		/* Registration Successful */
		if($retval == 0){
			$_SESSION['addmanagersuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Registration attempt failed */
		else if($retval == 2){
			$_SESSION['addmanagersuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procRemoveBooking(){
		global $session, $form;

		$uname = $session->username;
		$session_id = htmlentities(mysql_real_escape_string($_POST['sessionid']));
		if((string)$session_id === (string)(int)$session_id){/**/}else{
			$form->setError("username", "Cannot remove. You are not signed into it.");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$retval = $session->removeBooking($uname,$session_id);

		/* Registration Successful */
		if($retval == 0){
			$_SESSION['removebookingsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Registration attempt failed */
		else if($retval == 2){
			$_SESSION['removebookingsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}


	function procRemoveManage(){
		global $session, $form;

		$uname = $session->username;
		$session_id = htmlentities(mysql_real_escape_string($_POST['sessionid']));
		if((string)$session_id === (string)(int)$session_id){/**/}else{
			$form->setError("username", "Cannot remove. You're not managing this session.");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$retval = $session->removeManager($uname,$session_id);

		/* Registration Successful */
		if($retval == 0){
			$_SESSION['removemanagesuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Registration attempt failed */
		else if($retval == 2){
			$_SESSION['removemanagesuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}
	
	/*
	function procRegisterNoshow(){
		global $session, $form;

		$usernames = $_POST['noshow'];
		$session_id = $_POST['noshow_session_id'];

		$retval = $session->registerNoshows($usernames, $session_id);
		
		if ($retval == 0) {
			header("Location: ".$session->referrer);
		} else if ($retval == 1) {
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
		}
	}
	*/

	function procAddQueue(){
		global $session, $form;

		$uname = $session->username;
		$match_id = htmlentities(mysql_real_escape_string($_POST['matchid']));
		if((string)$match_id === (string)(int)$match_id){/**/}else{
			$form->setError("username", "Please log in to book");	// confusing err msg to confuse "hackers" who send fake session_id
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$retval = $session->addQueue($uname,$match_id);

		/* Registration Successful */
		if($retval == 0){
			$_SESSION['addqueuesuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Registration attempt failed */
		else if($retval == 2){
			$_SESSION['addqueuesuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procRemoveQueue(){
		global $session, $form;

		$uname = $session->username;
		$match_id = htmlentities(mysql_real_escape_string($_POST['matchid']));
		if((string)$match_id === (string)(int)$match_id){/**/}else{
			$form->setError("username", "Cannot remove. You are not signed into it.");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$retval = $session->removeQueue($uname,$match_id);

		/* Registration Successful */
		if($retval == 0){
			$_SESSION['removequeuesuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Registration attempt failed */
		else if($retval == 2){
			$_SESSION['removequeuesuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procAddCaptain(){
		global $session, $form;

		$uname = $session->username;
		$match_id = htmlentities(mysql_real_escape_string($_POST['matchid']));
		if((string)$match_id === (string)(int)$match_id){/**/}else{
			$form->setError("username", "Please log in to book");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$retval = $session->addCaptain($uname,$match_id);

		/* Registration Successful */
		if($retval == 0){
			$_SESSION['addcaptainsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Registration attempt failed */
		else if($retval == 2){
			$_SESSION['addcaptainsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procRemoveCaptain(){
		global $session, $form;

		$uname = $session->username;
		$match_id = htmlentities(mysql_real_escape_string($_POST['matchid']));
		if((string)$match_id === (string)(int)$match_id){/**/}else{
			$form->setError("username", "Cannot remove. You're not captain in this match.");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$retval = $session->removeCaptain($uname,$match_id);

		/* Registration Successful */
		if($retval == 0){
			$_SESSION['removecaptainsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Registration attempt failed */
		else if($retval == 2){
			$_SESSION['removecaptainsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procRemovePlayer(){
		global $session, $form;

		$uname = $session->username;
		$match_id = htmlentities(mysql_real_escape_string($_POST['matchid']));
		if((string)$match_id === (string)(int)$match_id){/**/}else{
			$form->setError("username", "Cannot remove. You are not signed into it.");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$retval = $session->removePlaying($uname,$match_id);

		/* Registration Successful */
		if($retval == 0){
			$_SESSION['removeplayingsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Registration attempt failed */
		else if($retval == 2){
			$_SESSION['removeplayingsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}


	//OUTDATED comments and news

	function procAddComment(){
		global $session, $form;
		/* Add comment attempt */
		$newsid2 = $_POST['newsid'];
		$comment2 = $_POST['comment'];
		$uname = $session->username;
		$retval = $session->addComment($newsid2, $comment2, $uname);

		/* Add comment successful */
		if($retval == 0){
			$_SESSION['addcommentsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Add comment attempt failed */
		else if($retval == 2){
			$_SESSION['addcommentsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procEditComment(){
		global $session, $form;
		/* Add comment attempt */
		$commentid2 = htmlentities(mysql_real_escape_string($_POST['commentid']));
		$comment2 = htmlentities(mysql_real_escape_string($_POST['comment']));
		$retval = $session->editComment($commentid2, $comment2);

		/* Add comment successful */
		if($retval == 0){
			$_SESSION['editcommentsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Add comment attempt failed */
		else if($retval == 2){
			$_SESSION['editcommentsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procDeleteComment(){
		global $session, $form;
		/* Add comment attempt */
		$commentid2 = htmlentities(mysql_real_escape_string($_POST['commentid']));
		$retval = $session->deleteComment($commentid2);

		/* Add comment successful */
		if($retval == 0){
			$_SESSION['deletecommentsuccess'] = true;
			header("Location: ".$session->referrer);
		}
		/* Error found with form */
		else if($retval == 1){
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
		}
		/* Add comment attempt failed */
		else if($retval == 2){
			$_SESSION['deletecommentsuccess'] = false;
			header("Location: ".$session->referrer);
		}
	}

	function procContactUs() {
		global $session, $mailer, $form;

		// recaptcha verification
		$captcha = htmlentities(mysql_real_escape_string($_POST['g-recaptcha-response']));
		$privatekey = "6Lc3jRQTAAAAAD4zCtiPwdtbkUFRZo7cZceEd8dk"; // from registering on recaptcha site
		$resp = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$privatekey."&response=".$captcha);
		$resp = json_decode($resp, true);

		if($resp["success"] == false){
			$form->setError("captcha", "Captcha was incorrect.");
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
			header("Location: ".$session->referrer);
			return;
		}

		$name = htmlentities($_POST['name']);
		$email = htmlentities($_POST['email']);
		$message = htmlentities($_POST['message']);

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$form->setError("email", "The email is invalid.");
		}
		if (strlen($name) == 0) {
			$form->setError("name", "The name is required.");
		}
		if (strlen($message) == 0) {
			$form->setError("message", "The message is required.");
		}

		if ($form->num_errors > 0) {
			$_SESSION['value_array'] = $_POST;
			$_SESSION['error_array'] = $form->getErrorArray();
		} else {
			if ($mailer->sendContact($name, $email, $message)) {
				$_SESSION['contactus'] = true;
			} else {
				$_SESSION['contactus'] = false;
			}
		}

		header("Location: ".$session->referrer);
	}
} // end class
$process = new Process;
?>
