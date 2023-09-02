<?php  
  if($session->userlevel>=LEVEL_GUEST){
	include("display_session_info.php");
	$session_id = htmlentities(mysql_real_escape_string($_GET['sid']));
?>
<section id="main" class="wrapper">
    <div class="container">
        <header class="major special">
            <h2>Session <?php   echo $session_id ?></h2>
        </header>
<?php  
	displayGivenSessionDetails($session_id);

	$next_session = $session_id + 1;
	$prev_session = $session_id - 1;
	echo "<ul class=\"actions\"><li><a class=\"button alt\" href=\"".SITE_URL."/sessions/view/index.php?sid=".$prev_session."\">Previous Session</a></li><li><a class=\"button\" href=\"".SITE_URL."/sessions/view/index.php?sid=".$next_session."\">Next Session</a></li><li><a class=\"button special\" href=\"".SITE_URL."/sessions/view/print_mode.php?sid=$session_id\">Print List</a></li></ul>";
?>
	<h3>Users booked into session</h3>

<?php  	
	displaySessionBookings($session_id);
}else{
	echo "<section id=\"main\" class=\"wrapper\"><div class=\"container\"><div class=\"alert warning\">You must be a member to see this page.</div></div></section>";
}
?>
    </div>
</section>
