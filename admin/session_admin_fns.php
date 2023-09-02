<?php  
/* this is used by:
	session_all_list.php
	edit_session.php
to display session data in the Admin Centre
*/

function displaySessions(){
	global $database, $session;

	$q = "SELECT * FROM ".TBL_SESSION_LIST." ORDER BY session_start DESC";
	$result = $database->query($q);
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		echo "<p>Error displaying info. Please check back later.</p>";
		return;
	}
	if($num_rows == 0){
		echo "<p>There are currently no upcoming sessions. Please check back later.</p>";
		return;
	}

	echo "<table class=\"table table-bordered table-hover\"><thead><tr><th>Date</th><th>Time</th><th>Location</th><th>Manager(s)</th><th>Places</th><th class=\"additional-info\">Additional Info</th><th>Operations</th></tr></thead>";

	/* Display table contents */
	for($i=0; $i<$num_rows; $i++){
		$session_id  = mysql_result($result,$i,"session_id");
		$session_start  = mysql_result($result,$i,"session_start");
		$session_end  = mysql_result($result,$i,"session_end");
		$location  = mysql_result($result,$i,"location");
		$places  = mysql_result($result,$i,"places");
		$training_details = mysql_result($result,$i,"training_details");

		$date = date("D jS M Y", strtotime($session_start));
		$start_time = date("G:i", strtotime($session_start));
		$end_time = date("G:i", strtotime($session_end));

		$locationlink = $location; // no links on this admin page

		$places_booked = $database->getNumSessionBookings($session_id);
		$places_left = $places - $places_booked;
		if($places_left<0){ $places_left=0; }	// don't show -1 spaces etc.

		$session_managers = displaySessionManagers($session_id);

		$opbuttons = "<form action=\"".SITE_URL."/sessions/view/index.php?sid=$session_id\" method=\"POST\"><input type=\"submit\" value=\"View\" alt=\"View session booking button\" class=\"sessionformbutton\" /></form>";

		echo "<tr><td class=\"center\">$date</td><td class=\"center\">$start_time - $end_time</td><td class=\"center\">$locationlink</td><td class=\"center\">$session_managers</td><td class=\"center\">$places_left/$places</td><td class=\"center\">$training_details</td>";
		echo "<td class=\"center\">$opbuttons</td>";
		echo "</tr>";
	}
	echo "</table>";
}


function displaySessionManagers($session_id){
	global $database;

	$q = "SELECT DISTINCT(username) "
	."FROM ".TBL_SESSION_MANAGER." WHERE session_id = '$session_id' AND deleted = '0'";
	$result = $database->query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		$session_managers = "Error displaying info.";
		return $session_managers;
	}
	if($num_rows == 0){
		$session_managers = "Currently no managers.";
		return $session_managers;
	}
	$session_managers = "<ul class=\"inline\">";
	for($i=0; $i<$num_rows; $i++){
		/* Session manager one */
		$username  = mysql_result($result,$i,"username");
		$req_user_info = $database->getUserInfo($username);
		$firstname = $req_user_info['firstname'];
		$secondname = $req_user_info['lastname'];
		$session_managers .= "<li><a href=\"".SITE_URL."/login/userinfo/index.php?user=$username\">$firstname $secondname</a></li>";
	}
	$session_managers .= "</li>";
	return $session_managers;
}


function displayEditSessions(){
	global $database;

	$q = "SELECT * FROM ".TBL_SESSION_LIST." WHERE session_start > NOW() ORDER BY session_start";
	$result = $database->query($q);
	/* Error occurred, return given name by default */
	$num_rows = mysql_numrows($result);
	if(!$result || ($num_rows < 0)){
		echo "<p>Error displaying info. Please check back later.</p>";
		return;
	}
	if($num_rows == 0){
		echo "<p>There are currently no upcoming sessions. Please check back later.</p>";
		return;
	}

	echo "<div class=\"table-wrapper\"><table><thead><tr><th>Date</th><th>Time (24h)</th><th>Location</th><th>Session managers</th><th>Places</th><th>Training Details</th><th>Operations</th></tr></thead><tbody>\n";

	/* Display table contents */
	for($i=0; $i<$num_rows; $i++){
		$session_id  = mysql_result($result,$i,"session_id");
		$session_start  = mysql_result($result,$i,"session_start");
		$session_end  = mysql_result($result,$i,"session_end");
		$location  = mysql_result($result,$i,"location");
		$places  = mysql_result($result,$i,"places");
		$training_details  = mysql_result($result,$i,"training_details");
		if(empty($training_details)){
			$training_details="N/A";
		}


		$date = date("D jS M Y", strtotime($session_start));
		$start_time = date("G:i", strtotime($session_start));
		$end_time = date("G:i", strtotime($session_end));

		$places_booked = $database->getNumSessionBookings($session_id);
		$places_left = $places - $places_booked;

		$session_managers = displaySessionManagers($session_id);

		$opbuttons = "<form action=\"".SITE_URL."/sessions/view/index.php?sid=$session_id\" method=\"POST\"><input type=\"submit\" value=\"View\" class=\"link\" /></form>";

		$opbuttons .= "\n<form action=\"".SITE_URL."/admin/edit_session.php?action=edit&id=$session_id\" method=\"POST\"><input type=\"submit\" value=\"Edit\" class=\"link\" /></form>";

		$opbuttons .= "\n<form action=\"".SITE_URL."/admin/edit_session.php?action=delete&id=$session_id\" method=\"POST\"><input type=\"submit\" value=\"Delete\" class=\"link\" /></form>";

		echo "<tr><td>$date</td><td>$start_time - $end_time</td><td>$location</td><td>$session_managers</td><td><b>$places_left</b> / $places</td><td>$training_details</td><td>$opbuttons</td></tr>\n";
	}
	echo "</tbody></table></div>";
}


function getForEdit($sess_id) {
	global $session, $database, $form;
	$q1 = "SELECT * FROM session_list WHERE session_id='$sess_id'";
	$result1 = mysql_query($q1, $database->connection);

	if( mysql_numrows($result1)<1) {
		echo "<p>Session not found</p>";
		return;
	}

	$r5 = mysql_fetch_assoc($result1);
	$s_id  = $r5['session_id'];
	$s_start  = $r5['session_start'];
	$s_end  = $r5['session_end'];
	$loc  = $r5['location'];
	$plac  = $r5['places'];
	$mngrs  = $r5['managers'];
	$training_details = $r5['training_details'];
	?>

	<p><strong>Be careful to keep the format precisely correct</strong></p>
	<form action="adminprocess.php" method="POST">
		<div class="row uniform">
        	<div class="12u$">
                <input type="text" name="s_start" value="<?php   if($form->value("s_start") == ""){echo $s_start;} else{echo $form->value("s_start");} ?>" />
                <span class="help-block">
                    <strong>YYYY-MM-DD HH:MM:ss</strong>
                </span>
            </div>
        </div>
		<div class="row uniform">
        	<div class="12u$">
                <input type="text" name="s_end" value="<?php   if($form->value("s_end") == ""){echo $s_end;} else{echo $form->value("s_end");} ?>" />
                <span class="help-block">
                    <strong>YYYY-MM-DD HH:MM:ss</strong>
                </span>
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
                <input type="text" maxlength="2" name="managers" value="<?php   if($form->value("mngrs") == ""){echo $mngrs;} else{echo $form->value("mngrs");} ?>" />
                <span class="help-block">
                    <strong>Managers</strong>
                </span>
            </div>
        	<div class="3u">
                <input type="text" name="trad" value="<?php   if($form->value("training_details") == ""){echo $training_details;} else{echo $form->value("training_details");} ?>" />
                <span class="help-block">
                    <strong>Notes</strong>
                </span>
            </div>
        </div>

	<input type="hidden" name="s_id" value="<?php   if($form->value("s_id") == ""){echo $s_id;} else{echo $form->value("s_id");} ?>" />
	<input type="hidden" name="subeditsession" value="1" />
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


function getForDelete($sess_id){
	global $session, $database, $form;
	$q2 = "SELECT * FROM session_list WHERE session_id='$sess_id'";
	$result = mysql_query($q2, $database->connection);

	if( mysql_numrows($result)<1) {
		echo "<p>Session not found</p>";
		return; }

	$r4 = mysql_fetch_assoc($result);
	$sid  = $r4['session_id'];
	$sstart  = $r4['session_start'];
	$send  = $r4['session_end'];
	$loca  = $r4['location'];
	$plc  = $r4['places'];
	$mngrs  = $r5['managers'];
	?>
	<p>Are you sure you want to delete this session?</p>
	<form action="adminprocess.php" method="POST">
		<div class="row uniform">
        	<div class="12u$">
                <input type="text" disabled value="<?php   echo $sstart ?>" />
                <span class="help-block">
                    <strong>Session Start</strong>
                </span>
            </div>
        </div>
		<div class="row uniform">
        	<div class="12u$">
                <input type="text" disabled value="<?php   echo $send ?>" />
                <span class="help-block">
                    <strong>Session End</strong>
                </span>
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
                <input type="text" disabled value="<?php   if ($mngrs == 0) {echo "0";} else {echo $mngrs;} ?>" />
                <span class="help-block">
                    <strong>Managers</strong>
                </span>
            </div>
        </div>
	<input type="hidden" name="sid" value="<?php   if($form->value("sid") == ""){echo $sid;} else{echo $form->value("sid");} ?>" /></td>
	<input type="hidden" name="subdelsession" value="1" />
	<div class="row uniform">
    	<div class="12u$(small)">
            <ul class="actions">
                <li><input type="submit" value="Delete"></li>
            </ul>
        </div>
    </div>
	</form>

	<?php   } ?>
