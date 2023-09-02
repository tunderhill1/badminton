<?php

include("../include/session.php");
include("../server_url.php");

if ($session->userlevel < LEVEL_ADMIN) {
    header("Location: " . SITE_URL);
}

$apiKey = "7DFB6264-6287-4666-8320-42F496A92B1A";
$url = "https://eactivities.union.ic.ac.uk/API/csp/003/reports/members?year=";
$opts = array(
    "http" => array(
        "method" => "GET",
        "header" => "X-API-Key:$apiKey\r\n"
    )
);

// $context = stream_context_create($opts);
// $contents = file_get_contents($url, false, $context);
// $members = json_decode($contents);
// var_dump($contents);

$headers = ["X-API-Key:$apiKey\r\n"];
// var_dump($headers);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//for debug only!
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$contents = curl_exec($ch);
curl_close ($ch);

$members = json_decode($contents);
echo "<h2>" . count($members) . " member" . (count($members) == 1 ? "" : "s") . " displayed </h2>";
echo "<i>Consequently this page can be up to 24hrs out of date, and longer on a weekend.</i>";
echo "<br/>";
echo "<br/>";
echo "<table border=\"1\">";
echo "<tr> <th>Username</th> <th>Name</th> <th>Member Type</th> </tr>";

foreach ($members as $member) {
	echo "<tr> <td>".$member->{"Login"}."</td> <td>".$member->{'FirstName'}." ".$member->{'Surname'}."</td> <td>".$member->{'MemberType'}."</td> </tr>";
}

echo "</table>";