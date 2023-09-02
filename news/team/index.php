<?php 
include("../../include/session.php");
$pagetitle = "Team News";
$type_num = 2;
$type_name = "the ICBC Team";
$type_name_short = "Team";
include("../../include/header.tpl.php");
include("../../include/news_process.php");
include("../news.tpl.php");
include("../../include/footer.tpl.php");
?>
