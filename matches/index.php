<?php
include("../server_url.php");
include("../include/session.php");

$pagetitle = "Social Match Booking";
$session->referrer = $server_url."/mathces/";
include("../include/header.tpl.php");
include("matches.tpl.php");
include("../include/footer.tpl.php");
?>