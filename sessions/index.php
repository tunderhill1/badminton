<?php 
include("../server_url.php");
  include("../include/session.php");

  $pagetitle = "Book A Session";
  $session->referrer = $server_url."/sessions/";
  include("../include/header.tpl.php");
  include("sessions.tpl.php");
  include("../include/footer.tpl.php");
?>
