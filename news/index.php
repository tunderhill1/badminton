<?php 
include("../include/session.php");
$pagetitle = "News";
$type_num = 0;
$type_name = "everything";
$type_name_short = "All";
include("../include/header.tpl.php");
include("../include/news_process.php");
include("news.tpl.php");
include("../include/footer.tpl.php");
?>
