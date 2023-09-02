<?php 
include("../../include/session.php");
$pagetitle = "Club News";
$type_num = 1;
$type_name = "our club";
$type_name_short = "Club";
include("../../include/header.tpl.php");
include("../../include/news_process.php");
include("../news.tpl.php");
include("../../include/footer.tpl.php");
?>
