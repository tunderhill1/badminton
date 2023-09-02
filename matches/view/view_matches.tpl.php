<?php  
  if($session->userlevel>=LEVEL_GUEST){
	include("display_matches_info.php");
	$match_id = htmlentities(mysql_real_escape_string($_GET['mid']));
?>
<section id="main" class="wrapper">
    <div class="container">
        <header class="major special">
            <h2>Match <?php   echo $match_id ?></h2>
        </header>
<?php  
	displayGivenMatchDetails($match_id);

	$next_match = $match_id + 1;
	$prev_match = $match_id - 1;
	echo "<ul class=\"actions\"><li><a class=\"button alt\" href=\"".SITE_URL."/matches/view/index.php?mid=".$prev_match."\">Previous Match</a></li><li><a class=\"button\" href=\"".SITE_URL."/matches/view/index.php?mid=".$next_match."\">Next Match</a></li><li><a class=\"button special\" href=\"".SITE_URL."/matches/view/print_mode.php?mid=$match_id\">Print List</a></li></ul>";
?>
	<h3>Users booked into the match</h3>
<?php  	displayMatchBookings($match_id);
}else{
	echo "<section id=\"main\" class=\"wrapper\"><div class=\"container\"><div class=\"alert warning\">You must be a member to see this page.</div></div></section>";
}
?>
    </div>
</section>
