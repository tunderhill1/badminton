<?php 
include("../include/session.php");
include("match_admin_fns.php");

if($session->userlevel >= LEVEL_COMMITTEE){
	include("../include/header.tpl.php");
?>
<section id="main" class="wrapper">
    <div class="content container" align="center">
        <header class="major special">
            <h2>All matches</h2>
        </header>
<?php 
	displayMatches();
?>
	</div>
</section>
<?php 
include("../include/footer.tpl.php");

}else{ header("Location: " . SITE_URL); } // user not committee member
?>