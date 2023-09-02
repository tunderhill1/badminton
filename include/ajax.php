<?php 
// $method is type int for security (cant inject using just ints)

include("session.php");
$method = htmlentities(mysql_real_escape_string($_GET["method"]));
if((string)$method === (string)(int)$method){

	switch($method){

	// method getRank from /about/stats/ uses '3' arbitrarily
	case "3":
		if($session->logged_in && ISSET($_GET["user"])){
			include("../about/stats/functions.php");
			$user = htmlentities(mysql_real_escape_string($_GET["user"]));
			$name_array = $database->getNamefromUsername($user);
			$name = $name_array['firstname'];
			if($name_array['nickname']!=""){
				$name = $name." (".$name_array['nickname'].")";
			}
			$sessions = most_sessions($session->logged_in,$user);
			$manages = most_managers($session->logged_in,$user);
			echo "<p>$name ($user) has booked into $sessions sessions, and has managed $manages sessions.";
			if($manages!=0){
				$tot = $sessions + $manages;
				echo " Total: $tot sessions.";
			}
			echo "</p>";
		}
		break;

	default:
		header("Location: javascript: history.go(-1)");
	}
}
?>
