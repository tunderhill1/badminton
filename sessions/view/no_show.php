<?php
	include_once("fix_mysql.inc.php");
	include("../../include/database.php");
	include("../../include/mailer.php");
	
	global $database, $session, $mailer;

	$usernames = $_POST['noshow'];
	$session_id = $_POST['noshow_session_id'];
	$session_date = $database->getSessionDatefromID($session_id);

	if($_POST["Submit"]=="Submit Noshows"){
		for($i=0; $i<sizeof($usernames); $i++){
			$un = $usernames[$i];
			$name_array = $database->getNamefromUsername($un);
			$email = $database->getEmailfromUsername($un);

			$result = mysql_query("SELECT * FROM noshows WHERE usr_name=\"$un\" ORDER BY noshow_id DESC LIMIT 1");

			$query = "INSERT INTO ".TBL_NOSHOWS." (session_id, session_date, usr_name, firstname, lastname, action) VALUES ({$session_id},\"{$session_date}\",\"{$un}\",\"{$name_array['firstname']}\",\"{$name_array['lastname']}\",";

			if(!$result || mysql_numrows($result) < 1){
				$query .= "\"warning\")";

				if(!$mailer->sendBookingWarning($name_array['firstname'],$email,$session_date)){
					echo "Was not able to email user ".$un." about warning at email ".$email.". Please contact admin.\n";
				}
				
			} else {
				$last_noshow_data = mysql_fetch_array($result);
				$datediff = date_diff(date_create($session_date),date_create($last_noshow_data['session_date']),TRUE);
			
				if((int)$datediff->format("%a") == 0){

				} elseif((int)$datediff->format("%a") <= NOSHOW_BAN_FREQUENCY && $last_noshow_data['action'] == "warning"){
					//if last noshow entry was < 30 days ago, deactivate account (ban) for 2 weeks and remove from sessions

					$query .= "\"ban\")";
					
					if(!$mailer->sendBookingBan($name_array['firstname'],$email,$session_date)){
						echo "Was not able to email user ".$un." about ban at email ".$email.". Please contact admin.\n";
					}

					$database->updateUserField($un, 'usr_lvl', LEVEL_GUEST);
					$database->removeUserFromFutureSessions($un);

				} else { //send warning
					$query .= "\"warning\")";

					if(!$mailer->sendBookingWarning($un,$email,$session_date)){
						echo "Was not able to email user ".$un." about warning at email ".$email.". Please contact admin.\n";
					}
				}
			}
			mysql_query($query) or die(mysql_error());
		}
	echo "You have registered ".sizeof($usernames)." noshows.";
	}
?>
