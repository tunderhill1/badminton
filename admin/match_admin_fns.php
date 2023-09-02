<?php  
/* this is used by:
	session_all_list.php
	edit_session.php
to display session data in the Admin Centre
*/

function displayMatches(){
	global $database, $session;

	$q = "SELECT * FROM ".TBL_MATCH_LIST." ORDER BY match_start DESC";
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		echo "<p>Error displaying info. Please check back later.</p>";
		return;
	}
	if($num_rows == 0){
		echo "<p>There are currently no upcoming matches. Please check back later.</p>";
		return;
	}

	echo "<table class=\"table table-bordered table-hover\"><thead><tr><th>Date</th><th>Time</th><th>Opponent</th><th>Location</th><th>Captain(s)</th><th>Players</th><th>Queue</th><th class=\"additional-info\">Additional Info</th><th>Operations</th></tr></thead>";

	/* Display table contents */
	for($i=0; $i<$num_rows; $i++){
		$match_id  = mysql_result($result,$i,"match_id");
		$match_start  = mysql_result($result,$i,"match_start");
		$opponent = mysql_result($result,$i,"opponent");
		$location  = mysql_result($result,$i,"location");
		$places  = mysql_result($result,$i,"places");
		$match_details  = mysql_result($result,$i,"match_details");
		if(empty($match_details)){
			$match_details="N/A";
		}

		$date = date("D jS M Y", strtotime($match_start));
		$start_time = date("G:i", strtotime($match_start));

		$locationlink = $location; // no links on this admin page

		$places_playing = $database->getNumMatchPlayers($match_id);
		$places_left = $places - $places_playing;
		$places_queue = $database->getNumMatchQueue($match_id);

		$match_captains = displayMatchCaptains($match_id);

		$opbuttons = "<form action=\"".SITE_URL."/matches/view/index.php?mid=$match_id\" method=\"POST\"><input type=\"submit\" value=\"View\" alt=\"View match booking button\" class=\"matchformbutton\" /></form>";

		echo "<tr><td>$date</td><td>$start_time</td><td>$opponent</td><td>$locationlink</td><td>$match_captains</td><td>$places_playing/$places</td><td>$places_queue</td><td>$match_details</td>";
		echo "<td class=\"center\">$opbuttons</td>";
		echo "</tr>";
	}
	echo "</table>";
}

function displayMatchCaptains($match_id){
	global $database;

	$q = "SELECT username "
	."FROM ".TBL_MATCH_BOOKING." WHERE match_id = '$match_id' AND deleted = '0' AND status = 'Captain'";
	$result = $database->query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		$match_captains = "Error displaying info.";
		return $match_captains;
	}
	if($num_rows == 0){
		$match_captains = "Currently no captains.";
		return $match_captains;
	}
	$match_captains = "<ul class=\"inline\">";
	for($i=0; $i<$num_rows; $i++){
		/* Session manager one */
		$username  = mysql_result($result,$i,"username");
		$req_user_info = $database->getUserInfo($username);
		$firstname = $req_user_info['firstname'];
		$secondname = $req_user_info['secondname'];
		$match_captains .= "<li><a href=\"".SITE_URL."/login/userinfo/index.php?user=$username\">$firstname $secondname</a></li>";
	}
	$match_captains .= "</li>";
	return $match_captains;
}

function displayEditMatches(){
	global $database;

	$q = "SELECT * FROM ".TBL_MATCH_LIST." WHERE match_start > NOW() ORDER BY match_start";
	$result = $database->query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		echo "<p>Error displaying info. Please check back later.</p>";
		return;
	}
	if($num_rows == 0){
		echo "<p>There are currently no upcoming matches. Please check back later.</p>";
		return;
	}

	echo "<div class=\"table-wrapper\"><table><thead><tr><th>Date</th><th>Time (24h)</th><th>Opponent</th><th>Location</th><th>Captains</th><th>Players</th><th>Queue</th><th>Match Details</th><th>Operations</th></tr></thead><tbody>\n";

	/* Display table contents */
	for($i=0; $i<$num_rows; $i++){
		$match_id  = mysql_result($result,$i,"match_id");
		$match_start  = mysql_result($result,$i,"match_start");
		$opponent = mysql_result($result,$i,"opponent");
		$location  = mysql_result($result,$i,"location");
		$places  = mysql_result($result,$i,"places");
		$match_details  = mysql_result($result,$i,"match_details");
		if(empty($match_details)){
			$match_details="N/A";
		}

		$date = date("D jS M Y", strtotime($match_start));
		$start_time = date("G:i", strtotime($match_start));

		$locationlink = $location; // no links on this admin page

		$places_playing = $database->getNumMatchPlayers($match_id);
		$places_left = $places - $places_playing;
		$places_queue = $database->getNumMatchQueue($match_id);

		$match_captains = displayMatchCaptains($match_id);

		$opbuttons = "<form action=\"".SITE_URL."/matches/view/index.php?mid=$match_id\" method=\"POST\"><input type=\"submit\" value=\"View\" class=\"link\" /></form>";

		$opbuttons .= "\n<form action=\"".SITE_URL."/admin/edit_match.php?action=edit&id=$match_id\" method=\"POST\"><input type=\"submit\" value=\"Edit\" class=\"link\" /></form>";

		$opbuttons .= "\n<form action=\"".SITE_URL."/admin/edit_match.php?action=delete&id=$match_id\" method=\"POST\"><input type=\"submit\" value=\"Delete\" class=\"link\" /></form>";

		echo "<tr><td>$date</td><td>$start_time</td><td>$opponent</td><td>$locationlink</td><td>$match_captains</td><td>$places_playing/$places</td><td>$places_queue</td><td>$match_details</td><td>$opbuttons</td>\n";
	}
	echo "</tbody></table></div>";
}


function getForEdit($match_id) {
	global $session, $database, $form;
	$q1 = "SELECT * FROM match_list WHERE match_id='$match_id'";
	$result1 = mysql_query($q1, $database->connection);

	if( mysql_numrows($result1)<1) {
		echo "<p>Match not found</p>";
		return;
	}

	$r5 = mysql_fetch_assoc($result1);
	$m_id  = $r5['match_id'];
	$m_start  = $r5['match_start'];
	$opp = $r5['opponent'];
	$loc  = $r5['location'];
	$plac  = $r5['places'];
	$match_details = $r5['match_details'];
	?>

	<p><strong>Be careful to keep the format precisely correct</strong></p>
	<form action="adminprocess.php" method="POST">
		<div class="row uniform">
        	<div class="12u$">
                <input type="text" name="m_start" value="<?php   if($form->value("m_start") == ""){echo $m_start;} else{echo $form->value("m_start");} ?>" />
                <span class="help-block">
                    <strong>YYYY-MM-DD HH:MM:ss</strong>
                </span>
            </div>
        </div>
		<div class="row uniform">
        	<div class="12u$">
                <!-- was insert endtime -->
            </div>
        </div>
		<div class="row uniform">
        	<div class="3u">
                <input type="text" name="loc" value="<?php   if($form->value("loc") == ""){echo $loc;} else{echo $form->value("loc");} ?>" />
                <span class="help-block">
                    <strong>Location</strong>
                </span>
            </div>
        	<div class="3u">
                <input type="text" maxlength="2" name="plac" value="<?php   if($form->value("plac") == ""){echo $plac;} else{echo $form->value("plac");} ?>" />
                <span class="help-block">
                    <strong>Places</strong>
                </span>
            </div>
        	<div class="3u">
                <input type="text" name="opp" value="<?php   if($form->value("opp") == ""){echo $opp;} else{echo $form->value("opp");} ?>" />
                <span class="help-block">
                    <strong>Opponent</strong>
                </span>
            </div>
        	<div class="3u">
                <input type="text" name="matd" value="<?php   if($form->value("match_details") == ""){echo $match_details;} else{echo $form->value("match_details");} ?>" />
                <span class="help-block">
                    <strong>Notes</strong>
                </span>
            </div>
        </div>

	<input type="hidden" name="m_id" value="<?php   if($form->value("m_id") == ""){echo $m_id;} else{echo $form->value("m_id");} ?>" />
	<input type="hidden" name="subeditmatch" value="1" />
	<div class="row uniform">
    	<div class="12u$(small)">
            <ul class="actions">
                <li><input type="submit" value="Save"></li>
                <li><input type="reset" class="alt" value="Reset"></li>
            </ul>
        </div>
    </div>
	</form>

	<?php   }


function getForDelete($match_id){
	global $session, $database, $form;
	$q2 = "SELECT * FROM ".TBL_MATCH_LIST." WHERE match_id='$match_id'";
	$result = mysql_query($q2, $database->connection);

	if( mysql_numrows($result)<1) {
		echo "<p>Match not found</p>";
		return; }

	$r4 = mysql_fetch_assoc($result);
	$mid  = $r4['match_id'];
	$mstart  = $r4['match_start'];
	$opp = $r4['opponent'];
	$loca  = $r4['location'];
	$plc  = $r4['places'];
	?>
	<p>Are you sure you want to delete this match?</p>
	<form action="adminprocess.php" method="POST">
		<div class="row uniform">
        	<div class="12u$">
                <input type="text" disabled value="<?php   echo $mstart ?>" />
                <span class="help-block">
                    <strong>Match Start</strong>
                </span>
            </div>
        </div>
		<div class="row uniform">
        	<div class="12u$">
                <!-- was for match end -->
            </div>
        </div>
		<div class="row uniform">
        	<div class="3u">
                <input type="text" disabled value="<?php   echo $loca ?>" />
                <span class="help-block">
                    <strong>Location</strong>
                </span>
            </div>
        	<div class="3u">
                <input type="text" disabled value="<?php   echo $plc ?>" />
                <span class="help-block">
                    <strong>Places</strong>
                </span>
            </div>
        	<div class="3u">
                <input type="text" disabled value="<?php   echo $opp ?>" />
                <span class="help-block">
                    <strong>Opponent</strong>
                </span>
            </div>
        </div>
	<input type="hidden" name="mid" value="<?php   if($form->value("mid") == ""){echo $mid;} else{echo $form->value("mid");} ?>" /></td>
	<input type="hidden" name="subdelmatch" value="1" />
	<div class="row uniform">
    	<div class="12u$(small)">
            <ul class="actions">
                <li><input type="submit" value="Delete"></li>
            </ul>
        </div>
    </div>
	</form>

	<?php   } ?>
