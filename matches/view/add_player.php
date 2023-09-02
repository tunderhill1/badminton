<?php
	include_once("fix_mysql.inc.php");
	include("../../include/database.php");
	include("../../include/mailer.php");

	global $database, $session, $mailer;

	$usernames = $_POST['addplayer'];
	$match_id = $_POST['match_id'];

	$match_info = $database->getMatchInfo($match_id);
	$max_players = $match_info["places"];
	$curr_players = $database->getNumMatchPlayers($match_id);

	$date = $match_info["match_start"];
	$opponent = $match_info["opponent"];
	$location = $match_info["location"];

	if($_POST["Submit"]=="Add Players"){
		$errors = "";

		if(sizeof($usernames)<$max_players-$curr_players){
			for($i=0; $i<sizeof($usernames); $i++){
				$un = $usernames[$i];
				$name_array = $database->getNamefromUsername($un);
				$firstname = $name_array['firstname'];
				$lastname = $name_array['lastname'];
				$email = $database->getEmailfromUsername($un);

				$result = $database->updateMatchPlayerStatus($un, $match_id, "Player");

				if(!$result){
					$errors .= "Was not able to add player ".$firstname." ".$lastname."\n";
				}
				if(!$mailer->sendPlayerAdded($firstname, $email, $date, $opponent, $location)){
					$errors .= "Was not able to email player ".$firstname." at email ".$email." about being added to the match. Please contact admin.\n";
				}
			}
		}else{
			$errors .= "You cannot add more players than there is capacity for.";
		}

		if($errors!=""){
			echo "Your query received the following errors:\n\n".$errors;
		}else{
			header("Location: ".SITE_URL."/matches/view/index.php?mid=".$match_id);
		}
	}
	
?>