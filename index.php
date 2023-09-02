<?php 
  include_once('fix_mysql.inc.php');
  include("include/session.php");
  include("server_url.php");
  $pagetitle = "Hello";
  $banner = true;
  include("include/header.tpl.php");
  include("index.tpl.php");
  include("include/footer.tpl.php");
?>
