<?php   function most_sessions($loggedin, $uname = null){
	global $database;

	if(isset($uname)){
		$q = "SELECT COUNT(username) AS usercount FROM ".TBL_SESSION_BOOKING." WHERE username= '$uname' AND deleted=0";
	}else{
		$q = "SELECT username, COUNT(username) AS usercount FROM ".TBL_SESSION_BOOKING." WHERE deleted=0 GROUP BY username ORDER BY usercount DESC LIMIT 10";
	}

	$result = $database->query($q);
	$num_rows = mysql_num_rows($result);

	if(!$result || ($num_rows < 0)){
		$output = "Error displaying info.";
		return $output;
	}
	if($num_rows == 0){
		$output = "No results";
		return $output;
	}

	if(isset($uname)){
		$output = mysql_result($result,0,'usercount');
	}else{
		for($i=0; $i<$num_rows; $i++){
			$count  = mysql_result($result,$i,'usercount');
			$username  = mysql_result($result,$i,'username');
			if($loggedin){
				$output .= "<tr><td><a href=\"" . SITE_URL . "/user/info/index.php?user=$username\">$username</a></td><td>$count</td></tr>";
			}else{
				$output .= "<tr><td>$username</td><td>$count</td></tr>";
			}
		}
	}
	return $output;
}

function most_managers($loggedin, $uname = null){
	global $database;

	if(isset($uname)){
		$q = "SELECT COUNT(username) AS usercount FROM ".TBL_SESSION_MANAGER." WHERE username= '$uname' AND deleted=0";
	}else{
		$q = "SELECT username, COUNT(username) AS usercount FROM ".TBL_SESSION_MANAGER." WHERE deleted=0 GROUP BY username ORDER BY usercount DESC LIMIT 5";
	}

	$result = $database->query($q);
	$num_rows = mysql_num_rows($result);

	if(!$result || ($num_rows < 0)){
		$output = "Error displaying info.";
		return $output;
	}
	if($num_rows == 0){
		$output = "No results";
		return $output;
	}

	if(isset($uname)){
		$output  = mysql_result($result,$i,'usercount');
	}else{
		for($i=0; $i<$num_rows; $i++){
			$count  = mysql_result($result,$i,'usercount');
			$username  = mysql_result($result,$i,'username');
			if($loggedin){
				$output .= "<tr><td><a href=\"" . SITE_URL . "/user/info/index.php?user=$username\">$username</a></td><td>$count</td></tr>";
			}else{
				$output .= "<tr><td>$username</td><td>$count</td></tr>";
			}
		}
	}
	return $output;
}

function page_loads(){
	global $database, $myGraph;
	$q1 = "(SELECT DATE_FORMAT(TIME, '%e/%c/%y') AS date, COUNT(username) FROM ".TBL_PAGELOADS." GROUP BY date ORDER BY time DESC LIMIT 21) UNION ".
	"(SELECT DATE_FORMAT(TIME, '%e/%c/%y') AS date, COUNT(username) FROM ".TBL_PAGELOADS." WHERE username != '' GROUP BY date ORDER BY time DESC LIMIT 21)";
	$r = $database->query($q1);
	$num_rows = mysql_num_rows($r);

	if(!$r || ($num_rows < 0)){
		$output = "Error displaying info.";
		return $output;
	}
	if($num_rows == 0){
		$output = "No results";
		return $output;
	}

	$half = $num_rows/2;
	for($i=0; $i<$half; $i++){

	$date = mysql_result($r,$i,'date');
		if( $date = mysql_result($r,$i+$half,'date') ){
			$count_all  = mysql_result($r,$i,'COUNT(username)');
			$count_login = mysql_result($r,$i+$half,'COUNT(username)');

			$myGraph->addBar($date, $count_all, $count_login);
		}else{
			$count_all  = mysql_result($r,$i,'COUNT(username)');

			$myGraph->addBar($date, $count_all, 0);
		}
	}
	return $output;
}

?>
<script type="text/javascript" language="javascript">
function getRank(){
	var pars = "method=3&user=" + $F('searchRank');
	var ajax = new Ajax.Updater('whereami', '<?php   echo SITE_URL; ?>/include/ajax.php', {method: 'GET', parameters: pars});
}
</script>
