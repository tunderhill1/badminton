<?php   // database class simplifies db access
include_once('fix_mysql.inc.php');
include("constants.php");
include("debug.php");

class MySQLDB
{
	var $connection;	//The MySQL database connection

	// Class constructor
	function MySQLDB(){
		// make db connection
		$this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
		mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
	}

	function confirmUserID($un,$uid){
		$q = "SELECT usr_id FROM ".TBL_USERS." WHERE usr_name = '$un'";

		$result = mysql_query($q, $this->connection);
		if(!$result || (mysql_numrows($result) < 1)){
			return 0; // no result
		}

		$dbarray = mysql_fetch_array($result); // Retrieve userid from result

		// validate that userid is correct
		if($uid == $dbarray['usr_id']){
			return 1; // success user name/id confirmed
		}else{
			return 0;	// userid invalid
		}
	}

	function unameFromCookUID($cookUID){
		$q = "SELECT usr_name FROM ".TBL_USERS." WHERE usr_id = '$cookUID'";

		$result = mysql_query($q, $this->connection);
		if(!$result || (mysql_numrows($result) < 1)){
			return 0; // no result
		}

		$dbarray = mysql_fetch_array($result);
		$uname = $dbarray['usr_name'];
		return $uname;
	}

	function manualLogin($un,$pwd){
		$q = "SELECT usr_pwd FROM ".TBL_USERS." WHERE usr_name = '$un'";
		$result = mysql_query($q, $this->connection);

		if(!$result || (mysql_numrows($result) < 1)){
			return 0; // username fail
		}

		$db_array = mysql_fetch_array($result); // Retrieve password from result
		$db_pwd = substr($db_array['usr_pwd'],2,40); // remove db obfustication

		if($pwd == $db_pwd){return 1;}else{return 0;}
	}

	function manualCreateUser($un,$first,$last,$pwd,$nickname,$email){
		$q = "INSERT INTO ".TBL_USERS." (usr_name, firstname, lastname, usr_pwd, nickname, alt_email) VALUES ('$un', '$first', '$last', '$pwd', '$nickname', '$email')";
		return mysql_query($q, $this->connection);
	}

	// userExists - Returns true if the username has been taken by another user, false otherwise.
	function userExists($un){
		$q = "SELECT usr_name FROM ".TBL_USERS." WHERE usr_name = '$un'";
		$result = mysql_query($q, $this->connection);
		return (mysql_numrows($result) > 0);
	}

	// if never logged in before
	function autoCreateUser($un){
		$name = ldap_get_names($un); // global fn from ICU sysadmin
		$q = "INSERT INTO ".TBL_USERS." (usr_name, firstname, lastname) VALUES ('$un', '$name[0]', '$name[1]')";
		return mysql_query($q, $this->connection);
	}

	// update User table: Field=Value for Username
	function updateUserField($username, $field, $value){
		$q = "UPDATE ".TBL_USERS." SET ".$field." = '$value' WHERE usr_name = '$username'";
		return mysql_query($q, $this->connection);
	}

	function getUserInfo($un){
		$q = "SELECT * FROM ".TBL_USERS." WHERE usr_name = '$un'";
		$result = mysql_query($q, $this->connection);
		if(!$result || (mysql_numrows($result) < 1)){ return NULL; }
		$dbarray = mysql_fetch_array($result);
		return $dbarray; // returns array of data
	}

	function getNamefromUsername($un){
		$q = "SELECT firstname, lastname, nickname FROM ".TBL_USERS." WHERE usr_name = '$un'";
		$result = mysql_query($q, $this->connection);
		if(!$result || (mysql_numrows($result) < 1)){ return NULL; }
		$dbarray = mysql_fetch_array($result);
		return $dbarray; // returns array of data
	}

	function getFirstName($un){
		$q = "SELECT firstname FROM ".TBL_USERS." WHERE usr_name = '$un'";
		$result = mysql_query($q, $this->connection);
		if(!$result || (mysql_numrows($result) < 1)){ return NULL; }
		$dbarray = mysql_fetch_array($result);
		return $dbarray; // returns array of data
	}

	function getEmailfromUsername($un){
		$q = "SELECT alt_email FROM ".TBL_USERS." WHERE usr_name = '$un'";
		$result = mysql_query($q, $this->connection);

		$dbarray = mysql_fetch_array($result);
		$email = $dbarray['alt_email'];

		if(!$result || (mysql_numrows($result) < 1) || $email==""){
			$ic_mail = $un."@ic.ac.uk";
			return $ic_mail; // no alt email, so use IC one
		}

		return $email;
	}

	function hasManualPassword($un){
		$q = "SELECT usr_pwd FROM ".TBL_USERS." WHERE usr_name='$un'";
		$result = mysql_query($q, $this->connection);
		if(!$result || (mysql_numrows($result) < 1)){
			return 0; // no result
		}
		$dbarray = mysql_fetch_array($result);
		$pwdHash = $dbarray['usr_pwd'];

		if($pwdHash==""){ return 0; }else{ return 1; }
	}

	/* OUTDATED - NEWS NOT USED ANYMORE
	function createNewsPost($news_title, $news_article, $news_author, $type){
		$q = "INSERT INTO ".TBL_NEWS." (title, article, username, type) VALUES ('$news_title', '$news_article', '$news_author', '$type')";
		return mysql_query($q, $this->connection);
	}

	function editNewsPost($nid, $tit, $artic, $newstype) {
		$q = "UPDATE news SET title='$tit', article='$artic', type='$newstype' WHERE news=$nid";
		return mysql_query($q, $this->connection);
	}
	*/

	function createSession($start, $end, $location, $places, $managers,$training_details){
		$q = "INSERT INTO ".TBL_SESSION_LIST." (session_start, session_end, location, places, managers,training_details) VALUES ('$start', '$end', '$location', '$places', '$managers','$training_details')";
		return mysql_query($q, $this->connection);
	}

	function editSession($id, $start, $end, $loc, $places,$managers,$training_details){
		$q = "UPDATE session_list SET session_start='$start', session_end='$end', location='$loc', places='$places', training_details='$training_details',managers='$managers' WHERE session_id=$id";
		return mysql_query($q, $this->connection);
	}

	function deleteSession($sid){
		$q = "DELETE FROM session_list WHERE session_id=$sid";
		return mysql_query($q, $this->connection);
	}

	function getSessionDatefromID($id){
		$q = "SELECT session_start FROM ".TBL_SESSION_LIST." WHERE session_id='$id'";
		$result = mysql_query($q, $this->connection);
		$dbarray = mysql_fetch_array($result);
		$date = $dbarray['session_start'];
		return date("Y-m-d", strtotime($date));
	}

	/* OUTDATED - NEWS NOT USED ANYMORE
	function createCommentEntry($news_id, $comment, $username){
		$q = "INSERT INTO ".TBL_COMMENTS." (news_id, comment, username) VALUES ('$news_id', '$comment', '$username')";
		return mysql_query($q, $this->connection);
	}

	function updateCommentEntry($comment_id, $comment){
		$q = "UPDATE ".TBL_COMMENTS." SET comment = '$comment' WHERE comment_id = '$comment_id'";
		return mysql_query($q, $this->connection);
	}

	function deleteCommentEntry($comment_id){
		$q = "UPDATE ".TBL_COMMENTS." SET deleted = '1' WHERE comment_id = '$comment_id'";
		return mysql_query($q, $this->connection);
	}
	*/

	// returns true if user is already booked, false otherwise.
	function isUserAlreadyBooked($username, $day) {
		$q = "SELECT 1";
		if ($day == 1 || $day == 5) {
			// ***Allows booking 1 Monday OR 1 Friday session at a time***可能要改时间
			// $q = "SELECT ".TBL_SESSION_BOOKING.".session_id
			// 	  FROM ".TBL_SESSION_BOOKING.", ".TBL_SESSION_LIST."
			// 	  WHERE ".TBL_SESSION_BOOKING.".username = '$username'
			// 			  AND ".TBL_SESSION_LIST.".session_id = ".TBL_SESSION_BOOKING.".session_id
			// 			  AND ".TBL_SESSION_BOOKING.".deleted = '0'
			// 			  AND ".TBL_SESSION_LIST.".session_end > NOW()
			// 			  AND (DAYOFWEEK(".TBL_SESSION_LIST.".session_start) = '2' OR DAYOFWEEK(".TBL_SESSION_LIST.".session_start) = '5')";

			// ***Allows booking 1 Monday AND 1 Thursday session at a time***
			$q = "SELECT ".TBL_SESSION_BOOKING.".session_id
				  FROM ".TBL_SESSION_BOOKING.", ".TBL_SESSION_LIST."
				  WHERE ".TBL_SESSION_BOOKING.".username = '$username'
						  AND ".TBL_SESSION_LIST.".session_id = ".TBL_SESSION_BOOKING.".session_id
						  AND ".TBL_SESSION_BOOKING.".deleted = '0'
						  AND ".TBL_SESSION_LIST.".session_end > NOW()
						  AND (DAYOFWEEK(".TBL_SESSION_LIST.".session_start) = '".($day + 1)."')"; //DAYOFWEEK starts from Sunday=1
		} else if ($day == 4) {
			// Allows for booking multiple weeks at a time
			// $q = "SELECT ".TBL_SESSION_BOOKING.".session_id
			// 	  FROM ".TBL_SESSION_BOOKING.", ".TBL_SESSION_LIST."
			// 	  WHERE ".TBL_SESSION_BOOKING.".username = '$username'
			// 			  AND ".TBL_SESSION_LIST.".session_id = ".TBL_SESSION_BOOKING.".session_id
			// 			  AND ".TBL_SESSION_BOOKING.".deleted = '0'
			// 			  AND ".TBL_SESSION_LIST.".session_start > NOW()
			// 			  AND (DAYOFWEEK(".TBL_SESSION_LIST.".session_start) = '1'
			// 				OR DAYOFWEEK(".TBL_SESSION_LIST.".session_start) = '6')
			// 			  AND (WEEKOFYEAR(".TBL_SESSION_LIST.".session_start) = '$weekOfYear')";


			$q = "SELECT ".TBL_SESSION_BOOKING.".session_id
				  FROM ".TBL_SESSION_BOOKING.", ".TBL_SESSION_LIST."
				  WHERE ".TBL_SESSION_BOOKING.".username = '$username'
						  AND ".TBL_SESSION_LIST.".session_id = ".TBL_SESSION_BOOKING.".session_id
						  AND ".TBL_SESSION_BOOKING.".deleted = '0'
						  AND ".TBL_SESSION_LIST.".session_start > NOW()
						  AND DAYOFWEEK(".TBL_SESSION_LIST.".session_start) = '5'";
		}

		$result = mysql_query($q, $this->connection);
		return (mysql_numrows($result) > 0);
	}

	// returns true if user is booked for upcoming non-training session, false otherwise.
	function isUserAlreadyBookedNonTraining($username) {
		$q = "SELECT ".TBL_SESSION_BOOKING.".session_id FROM ".TBL_SESSION_BOOKING.", ".TBL_SESSION_LIST." WHERE ".TBL_SESSION_BOOKING.".username = '$username' AND ".TBL_SESSION_LIST.".session_id = ".TBL_SESSION_BOOKING.".session_id AND ".TBL_SESSION_BOOKING.".deleted = '0' AND ".TBL_SESSION_LIST.".session_start > NOW() AND DAYOFWEEK(".TBL_SESSION_LIST.".session_start) != '5'";
		$result = mysql_query($q, $this->connection);
		return (mysql_numrows($result) > 0);
	}

	// returns true if user is booked for upcoming training session, false otherwise.
	function isUserAlreadyBookedTraining($username) {
		$q = "SELECT ".TBL_SESSION_BOOKING.".session_id FROM ".TBL_SESSION_BOOKING.", ".TBL_SESSION_LIST." WHERE ".TBL_SESSION_BOOKING.".username = '$username' AND ".TBL_SESSION_LIST.".session_id = ".TBL_SESSION_BOOKING.".session_id AND ".TBL_SESSION_BOOKING.".deleted = '0' AND ".TBL_SESSION_LIST.".session_start > NOW() AND DAYOFWEEK(".TBL_SESSION_LIST.".session_start) = '5'";
		$result = mysql_query($q, $this->connection);
		return (mysql_numrows($result) > 0);
	}

	// true if the user is managing a session
	function isUserAtManageLimit($username){
		$q = "SELECT ".TBL_SESSION_MANAGER.".username FROM ".TBL_SESSION_MANAGER.", ".TBL_SESSION_LIST." WHERE ".TBL_SESSION_MANAGER.".username = '$username' AND ".TBL_SESSION_LIST.".session_id = ".TBL_SESSION_MANAGER.".session_id AND ".TBL_SESSION_MANAGER.".deleted = '0' AND ".TBL_SESSION_LIST.".session_start > NOW()";
		$result = mysql_query($q, $this->connection);
		return (mysql_numrows($result) >= 1);
	}

	function getSessionInfo($session_id){
		$q = "SELECT * FROM ".TBL_SESSION_LIST." WHERE session_id = '$session_id'";
		$result = mysql_query($q, $this->connection);
		if(!$result || (mysql_numrows($result) < 1)){ return NULL; }
		$dbarray = mysql_fetch_array($result);
		return $dbarray;
	}

	// returns number of places booked for given seesion_id
	function getNumSessionBookings($session_id){
		$q = "SELECT session_id FROM ".TBL_SESSION_BOOKING." WHERE session_id = '$session_id' AND deleted='0'";
		$result = mysql_query($q, $this->connection);
		return mysql_numrows($result);
	}

	// returns number of managers booked for given seesion_id
	function getNumManagers($session_id){
		$q = "SELECT session_id FROM ".TBL_SESSION_MANAGER." WHERE session_id=".$session_id." AND deleted='0'";
		$result = mysql_query($q, $this->connection);
		return mysql_numrows($result);
	}

	// returns max number of places
	function getMaxNumPlaces($session_id){
		$q = "SELECT places FROM ".TBL_SESSION_LIST." WHERE session_id='$session_id'";
		$r = mysql_query($q, $this->connection);
		$result = mysql_fetch_array($r);
		$maxP = $result['places'];
		return $maxP;
	}

	// returns number of managers for given seesion_id
	function getMaxNumManagers($session_id){
		$q = "SELECT managers FROM ".TBL_SESSION_LIST." WHERE session_id=".$session_id;
		$r = mysql_query($q, $this->connection);
		$result = mysql_fetch_array($r);
		$maxM = $result['managers'];
		return $maxM;
	}

	// returns number of spare spaces left
	function remainingSessionPlaces($sid){
		$remaining = $this->getMaxNumPlaces($sid) - $this->getNumSessionBookings($sid);
		return $remaining;
	}

	// returns number of managing spots left
	function remainingSessionManagers($sid){
		$remaining = $this->getMaxNumManagers($sid) - $this->getNumManagers($sid);
		return $remaining;
	}

	// true if already booked into this session, else false
	function isUserBookedToSession($un,$s){
		$q = "SELECT session_id FROM ".TBL_SESSION_BOOKING." WHERE session_id='$s' AND username='$un' AND deleted='0'";
		$r = mysql_query($q, $this->connection);
		if(mysql_numrows($r)>=1){ return true; }else{ return false; }
	}

	// true if already managing this session
	function isUserBookedToManage($username,$session_id){
		$q = "SELECT session_id FROM ".TBL_SESSION_MANAGER." WHERE session_id='$session_id' AND username='$username' AND deleted='0'";
		$result = mysql_query($q, $this->connection);
		return (mysql_numrows($result) > 0);
	}

	function createBooking($username, $session_id, $booking_date){
		$q = "INSERT INTO ".TBL_SESSION_BOOKING." (session_id, username, booking_date) VALUES ('$session_id', '$username', '$booking_date')";
		return mysql_query($q, $this->connection);
	}

	function deleteBooking($username, $session_id){
		$q = "UPDATE ".TBL_SESSION_BOOKING." SET deleted = '1' WHERE username = '$username' AND session_id = '$session_id'";
		return mysql_query($q, $this->connection);
	}

	function deleteManager($username, $session_id){
		$q = "UPDATE ".TBL_SESSION_MANAGER." SET deleted = '1' WHERE username = '$username' AND session_id = '$session_id'";
		return mysql_query($q, $this->connection);
	}

	function createManager($username, $session_id){
		$q = "INSERT INTO ".TBL_SESSION_MANAGER." (session_id, username) VALUES ('$session_id', '$username')";
		return mysql_query($q, $this->connection);
	}

	/* OUTDATED
	function recordDownload($username, $file){
		$time = date("Y-m-d H:i:s");
		$q = "INSERT INTO ".TBL_DOWNLOADS." (username, file, time) VALUES ('$username', '$file', '$time')";
		return mysql_query($q, $this->connection);
	}

	function addPageLoad($username, $httpref, $phpself, $qry, $ip2){
		//$ip2 used as $ip elsewhere, just being safe
		$time = date("Y-m-d H:i:s");
		$q = "INSERT INTO ".TBL_PAGELOADS." (username, httpref, phpself, query, ip, time) VALUES ('$username', '$httpref', '$phpself', '$qry', '$ip2', '$time')";
		return mysql_query($q, $this->connection);
	}
	*/

	function createMatch($start, $opponent, $location, $places,$match_details){
		$q = "INSERT INTO ".TBL_MATCH_LIST." (match_start, opponent, location, places, match_details) VALUES ('$start', '$opponent', '$location', '$places', '$match_details')";
		return mysql_query($q, $this->connection);
	}

	function editMatch($id, $start, $opponent, $loc, $places,$match_details){
		$q = "UPDATE match_list SET match_start='$start', opponent='$opponent', location='$loc', places='$places', match_details='$match_details' WHERE match_id=$id";
		return mysql_query($q, $this->connection);
	}

	function deleteMatch($mid){
		$q = "DELETE FROM match_list WHERE match_id=$mid";
		return mysql_query($q, $this->connection);
	}

	function getMatchInfo($match_id){
		$q = "SELECT * FROM ".TBL_MATCH_LIST." WHERE match_id = '$match_id'";
		$result = mysql_query($q, $this->connection);
		if(!$result || (mysql_numrows($result) < 1)){ return NULL; }
		$dbarray = mysql_fetch_array($result);
		return $dbarray;
	}

	function getNumMatchQueue($match_id){
		$q = "SELECT match_id FROM ".TBL_MATCH_BOOKING." WHERE match_id = '$match_id' AND deleted = '0' AND status = 'Queue'";
		$result = mysql_query($q, $this->connection);
		return mysql_numrows($result);
	}

	function getNumMatchPlayers($match_id){
		$q = "SELECT match_id FROM ".TBL_MATCH_BOOKING." WHERE match_id = '$match_id' AND deleted = '0' AND status = 'Player'";
		$result = mysql_query($q, $this->connection);
		return mysql_numrows($result);
	}

	function getNumCaptains($match_id){
		$q = "SELECT match_id FROM ".TBL_MATCH_BOOKING." WHERE match_id = '$match_id' AND deleted='0' AND status='Captain'";
		$result = mysql_query($q, $this->connection);
		return mysql_numrows($result);
	}

	function getUserMatchStatus($un, $match_id){
		$q = "SELECT status FROM ".TBL_MATCH_BOOKING." WHERE match_id = '$match_id' AND deleted = '0' AND username = '$un'";
		$result = mysql_query($q, $this->connection);
		if(!$result || mysql_numrows($result)<1){
			return NULL;
		}
		$dbarray = mysql_fetch_array($result)[0];
		return $dbarray;
	}

	function isUserBookedToMatch($un,$match_id){
		$q = "SELECT * FROM ".TBL_MATCH_BOOKING." WHERE match_id='$match_id' AND username='$un' AND deleted='0'";
		$result = mysql_query($q, $this->connection);
		if(mysql_numrows($result)>=1){ return true; }else{ return false; }
	}

	function createQueue($username, $match_id, $booking_date){
		$q = "INSERT INTO ".TBL_MATCH_BOOKING." (match_id, username, booking_date, status) VALUES ('$match_id', '$username', '$booking_date', 'Queue')";
		return mysql_query($q, $this->connection);
	}

	function deleteQueue($username, $match_id){
		$q = "UPDATE ".TBL_MATCH_BOOKING." SET deleted = '1' WHERE username = '$username' AND match_id = '$match_id' AND status = 'Queue'";
		return mysql_query($q, $this->connection);
	}

	function createCaptain($username, $match_id, $booking_date){
		$q = "INSERT INTO ".TBL_MATCH_BOOKING." (match_id, username, booking_date, status) VALUES ('$match_id', '$username', '$booking_date', 'Captain')";
		return mysql_query($q, $this->connection);
	}

	function deleteCaptain($username, $match_id){
		$q = "UPDATE ".TBL_MATCH_BOOKING." SET deleted = '1' WHERE username = '$username' AND match_id = '$match_id' AND status = 'Captain'";
		return mysql_query($q, $this->connection);
	}

	function deletePlayer($username, $match_id){
		$q = "UPDATE ".TBL_MATCH_BOOKING." SET deleted = '1' WHERE username = '$username' AND match_id = '$match_id' AND status = 'Player'";
		return mysql_query($q, $this->connection);
	}

	function updateMatchPlayerStatus($username, $match_id, $status){
		$q = "UPDATE ".TBL_MATCH_BOOKING." SET status = '$status' WHERE username = '$username' AND match_id = '$match_id' AND deleted = '0'";
		return mysql_query($q, $this->connection);
	}

	/*
	function getUserNoshowStatus($un){
		$dbarray = $this->getUserInfo($un);

		$q = "SELECT * FROM ".TBL_NOSHOWS." WHERE usr_name=\"$un\" ORDER BY noshow_id DESC LIMIT 1";
		$result = mysql_query($q, $this->connection);

		if($result || (mysql_numrows($result) >= 1)){
			$noshow_data = mysql_fetch_array($result);
			
			if(time() <= strtotime($noshow_data['session_date']) + NOSHOW_BAN_FREQUENCY){
				return $noshow_data['action'];
			} else {
				return "none";
			}

		} else {
			return "none";
		}
	}
	*/

	/*
	function addNoshow($session_id, $session_date, $un, $firstname, $lastname, $action) {
		$q = "INSERT INTO ".TBL_NOSHOWS." (session_id, session_date, usr_name, firstname, lastname, action) VALUES ('$session_id', '$session_date', '$un', '$firstname', '$lastname', '$action')";
		return mysql_query($q, $this->connection);
	}
	*/

	function updateBanStatus($un){
		//if user is banned and ban was >BAN_PERIOD days ago then unban
		$dbarray = $this->getUserInfo($un);

		if($dbarray['usr_lvl'] < LEVEL_MEMBER){

			$q = "SELECT * FROM ".TBL_NOSHOWS." WHERE usr_name=\"$un\" AND action=\"ban\" ORDER BY noshow_id DESC LIMIT 1";
			$result = mysql_query($q, $this->connection);
			
			if($result || (mysql_numrows($result) >= 1)){
				$noshow_data = mysql_fetch_array($result);
				$unban_time = strtotime($noshow_data['session_date']) + NOSHOW_BAN_PERIOD;

				if(time()>=$unban_time){//now >= unban_time
					$this->updateUserField($un, 'usr_lvl', LEVEL_MEMBER);
				}
			}
		}
	}

	function removeUserFromFutureSessions($un){
		//remove a user from all future sessions that are booked
		$q = "UPDATE ".TBL_SESSION_BOOKING." SET deleted = 1 WHERE username = \"{$un}\"";

		return mysql_query($q, $this->connection);
	}

	// superfluous, but used all over site, so leave-be
	function query($query){
		return mysql_query($query, $this->connection);
	}

};
$database = new MySQLDB; // Create db connection
?>
