<?php 
/******
THIS SCRIPT IS RUN AUTO BY CRON JOB, thanks to Union sysadmin (club.web.enquiries@ic.ac.uk)
******/
$apiKey = "7DFB6264-6287-4666-8320-42F496A92B1A";
$url = "https://eactivities.union.ic.ac.uk/API/csp/003/products/44431/sales";
$opts = [
    "http" => [
        "method" => "GET",
        "header" => "X-API-Key: $apiKey\r\n"
    ]
];

$headers = ["X-API-Key:$apiKey\r\n"];
// var_dump($headers);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//for debug only!
//等上面fix ssl issue后下面两句删掉
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$contents = curl_exec($ch);
curl_close ($ch);

$members = json_decode($contents);

include("../server_url.php");
include("../include/session.php");
global $database;

foreach($members as $member){
    $member = $member->{"Customer"};
	$un = $member->{"Login"}; // username
	// if user does not exist, create it
	if( ! $database->userExists($un) ){
		$database->autoCreateUser($un);
		echo "new user ".$member->{'FirstName'}." ".$member->{'Surname'}."\r\n";
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
			$database->updateUserField($un, 'usr_lvl', LEVEL_MEMBER);
		}
	}
}

echo "Done!";
