<?php 
/******
THIS SCRIPT IS RUN AUTO BY CRON JOB, thanks to Union sysadmin (club.web.enquiries@ic.ac.uk)
******/

// details provided by Union sysadmin (Alistair Cott at present)
define("DB_SRVR", "icsqln.cc.ic.ac.uk");
define("DB_USR", "su_badmintonlink");
define("DB_PWD", "jekl6yb54nhy");
define("DB_NME", "SU_NEWERPOL");

$connection = mssql_connect(DB_SRVR, DB_USR, DB_PWD) or die(mssql_error());
mssql_select_db(DB_NME, $connection) or die(mssql_error());

$q = "SELECT * FROM BadmintonMembers";
$r = mssql_query($q);

include("../server_url.php");
include("../include/session.php");
global $database;

while($x = mssql_fetch_array($r)){ // for each user

	$un = $x[0]; // username

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
		if($level==LEVEL_GUEST){
			$database->updateUserField($un, 'usr_lvl', LEVEL_ADMIN);
		}
	}
}

mssql_close($connection);
?>
