<?php 
echo "PAGE NOT CURRENTLY IN USE!";
/*

include("../server_url.php");
include("../include/session.php");
global $database;

	$un = $_GET['unx']; // username

	// if user does not exist, create it
	if( ! $database->userExists($un) ){
		$database->autoCreateUser($un);
	}

	// activate user (just created, or already existed)
	$q2 = "SELECT active, allow_active, usr_lvl FROM ".TBL_USERS." WHERE usr_name='$un'";
	$r2 = $database->query($q2);
	$array = mysql_fetch_array($r2);
	$allow = $array['allow_active'];
	$active = $array['active'];
	$level = $array['usr_lvl'];

	if($allow && !$active){
		$database->updateUserField($un, 'active', '1');
		if($level==1){
			$database->updateUserField($un, 'usr_lvl', '2');
		}
	}
*/
?>
