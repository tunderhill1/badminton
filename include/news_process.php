<?php 
function displayAllComments($news_id, $checklogin, $username) {

	$q = "SELECT comment_id,username,comment,UNIX_TIMESTAMP(`postdate`) as date FROM ".TBL_COMMENTS." WHERE news_id=$news_id AND deleted=0 ORDER BY postdate";
	$result = mysql_query ($q);

	while ($row = mysql_fetch_assoc ($result)) {

		/* Places table row data into easier to use variables. Also, Here we also make sure no  HTML tags, other than the ones we want are displayed */
		$comment_id = $row['comment_id'];
		$comment_author = $row['username'];
		$comment_day = date("D jS M Y", $row['date']);
		$comment_time = date("g:ia", $row['date']);
		$comment_date = "$comment_day at $comment_time";
		$comment = stripslashes($row['comment']);

		/* Start the comment item*/
		echo "<div class=\"comment-item\">";
		/* Display the comment meta */
		echo "<div class=\"comment-meta\"><a href=\"".SITE_URL."/user/info/index.php?user=$comment_author\">$comment_author</a><small> on $comment_date said</small></div>";
		/* Display the comment content */
		echo "<div class=\"comment-content\">$comment</div>";

		if($comment_author == $username){
			/* Display the comment options */
			echo "<a href=\"".SITE_URL."/news/index.php?action=edit&#38;commentid=$comment_id\" class=\"btn btn-default btn-xs\">Edit</a>";
			echo " <a href=\"".SITE_URL."/news/index.php?action=delete&#38;newsid=$news_id&#38;commentid=$comment_id\" class=\"btn btn-danger btn-xs\">Delete</a>";
		}
		echo "</div>";
	}
}


function displayXComments($x, $count, $news_id, $username){
	// displays X comments, but $count is total comments for $news_id, to account for if X<$count
	// i.e. add a line saying "there are more comments [link]"

	$q1 = "SELECT comment_id,username,comment,UNIX_TIMESTAMP(`postdate`) as date FROM ".TBL_COMMENTS." WHERE news_id=$news_id AND deleted=0 ORDER BY postdate DESC LIMIT $x";
	$q2 = "SELECT * FROM ($q1) AS inner ORDER BY postdate ASC";
	$result = mysql_query($q1);

	// warn if extra hidden comments
	if($count > $x){
		$extra = $count - $x;
		echo "<div class=\"commentitem\">";
		echo "<div class=\"commentbar\">";
		echo "<div class=\"editcomment\">";
		if($extra==1){
			echo "<a href=\"".SITE_URL."/news/index.php?action=show&#38;newsid=$news_id#comments\">View 1 older comment</a>";
		}else{
			echo "<a href=\"".SITE_URL."/news/index.php?action=show&#38;newsid=$news_id#comments\">View $extra older comments</a>";
		}
		echo "</div></div></div>";
	}

	// db query returns with date-DESC (so that LIMIT works)
	// now reverse that array, to have most recent last
	$comments = array(); // make empty array
	while($row = mysql_fetch_assoc($result)){
		$comments[] = $row; // fill up new array with results
	}
	$comments = array_reverse($comments); // reverse array

	foreach($comments as $comm) {

		$comment_id = $comm['comment_id'];
		$comment_author = $comm['username'];
		$comment_day = date("D jS M Y", $comm['date']);
		$comment_time = date("g:ia", $comm['date']);
		$comment_date = "$comment_day at $comment_time";
		$comment = stripslashes($comm['comment']);

		echo "<div class=\"commentitem\">";	// Start the comment item

		// display title bar - ignore quick-hack using "(edit/delete)comment" classes!
		echo "<div class=\"commentbar\">";
		echo "<div class=\"editcomment\"><a href=\"".SITE_URL."/user/info/index.php?user=$comment_author\">$comment_author</a></div>";
		echo "<div class=\"deletecomment\">$comment_date";

		if($comment_author == $username)
		{	/* Display edit and delete options if it's your post */
			echo " -- <a href=\"".SITE_URL."/news/index.php?action=edit&#38;commentid=$comment_id\">Edit</a> or ";
			echo "<a href=\"".SITE_URL."/news/index.php?action=delete&#38;newsid=$news_id&#38;commentid=$comment_id\">Delete</a>";
		}

		echo "</div>";
		echo "</div>";
		echo "<div class=\"commentcontent\">$comment</div>"; // comment content
		echo "</div>";	 // finish the comment item

	}	// end while loop

	if($session->logged_in){	// Add form to enter new comments
		echo "<div align=\"center\">";
		echo "<form action=\"".SITE_URL."/include/process.php\" id=\"addcomment-form\" method=\"post\"><fieldset>";
		echo "<p>Comment:</p>";
		echo "<textarea name=\"comment\" rows=\"2\" cols=\"50\" style=\"overflow: visible\"></textarea>";
		echo "<input type=\"hidden\" name=\"username\" value=\"$session->username\" />";
		echo "<input type=\"hidden\" name=\"newsid\" value=\"$news_id\" />";
		echo "<input type=\"hidden\" name=\"addcomment\" value=\"1\" />";
		echo "<br /><input type=\"submit\" name=\"submit\" value=\"Add Comment\" />";
		echo "</fieldset></form>";
		echo "</div>";
	}
}


function displayRecentComments($num) {

	$q1 = "SELECT comment_id,username,comment,news_id,UNIX_TIMESTAMP(`postdate`) as date FROM ".TBL_COMMENTS." WHERE deleted=0 ORDER BY postdate DESC LIMIT $num";
	$r1 = mysql_query ($q1);

	while($comm = mysql_fetch_assoc($r1)){

		$comment_id = $comm['comment_id'];
		$comment_day = date("jS M", $comm['date']);
		$comment_time = date("g:ia", $comm['date']);
		$comment_date = "$comment_day at $comment_time";
		$comment = stripslashes($comm['comment']);
		$comment_author = $comm['username'];

		$news_id = $comm['news_id'];
		$q2 = "SELECT title FROM ".TBL_NEWS." WHERE news_id = $news_id";
		$r2 = mysql_query ($q2);
		$news = mysql_fetch_assoc($r2);
		$news_title = $news['title'];

		echo "<p><a href=\"".SITE_URL."/user/info/index.php?user=$comment_author\">$comment_author</a> said (<a href=\"".SITE_URL."/news/index.php?action=show&newsid=$news_id\">$news_title</a>, $comment_date):<br>"
    ."\"$comment\"</p><hr>";
	}
}


function displayNews($max_items, $type){
  $q = "SELECT news_id,title,username,article,UNIX_TIMESTAMP(`postdate`) as date FROM ".TBL_NEWS." ORDER BY postdate DESC LIMIT $max_items";
	$result = mysql_query ($q);
	while ($row = mysql_fetch_assoc ($result)) {

		$news_id = $row['news_id'];
		$news_title = $row['title'];
		$news_day = date("D jS M Y", $row['date']);
		$news_time = date("g:ia", $row['date']);
		$news_date = "$news_day at $news_time";
		$news = $row['article'];
		$news_author = $row['username'];

		echo "<div class=\"newsitem\">"; // Start the news item

		/* Display the title */
		echo "<h3><a href=\"".SITE_URL."/news/index.php?action=show&#38;newsid=$news_id\">$news_title</a></h3>";
		/* Display the post date */
		echo "<div class=\"newsdate\">$news_date</div>";
		/* Display the news content */
		echo "<div class=\"newscontent\">$news</div>";

		$comment_query = "SELECT count(*) FROM ".TBL_COMMENTS." WHERE news_id=$news_id AND deleted=0";
		$comment_result = mysql_query ($comment_query);
		$comment_row = mysql_fetch_row($comment_result); // total number of comments

		displayXComments(5, $comment_row[0], $news_id, $news_author);

		echo "<div class=\"commentbar\">";
		/* Display number of comments with link */
		echo "<div class=\"commentnumber\"><a href=\"".SITE_URL."/news/index.php?action=show&#38;newsid=$news_id#comments\">Comments&#58; {$comment_row[0]}</a></div>";
		/* Display post author */
		echo "<div class=\"newsauthor\">Posted by <a href=\"".SITE_URL."/user/info/index.php?user=$news_author\">$news_author</a></div>";
		echo "</div>";

		/* Finish the news item*/
		echo "</div>";
	}
}


function getHeadlines($num, $typ/*, $page*/){
  $starting_article = 0;
	$q2 = "SELECT news_id,title,username,UNIX_TIMESTAMP(`postdate`) as date FROM ".TBL_NEWS." ORDER BY postdate DESC LIMIT $starting_article, $num";
	$r2 = mysql_query($q2);

	while($res2 = mysql_fetch_assoc($r2)){

		$news_id = $res2['news_id'];
		$news_title = $res2['title'];
		$news_day = date("D jS M Y", $res2['date']);
		$news_author = $res2['username'];

    echo "<div class=\"post-preview\">";
		echo "<a href=\"".SITE_URL."/news/index.php?action=show&#38;newsid=$news_id\"><h2 class=\"post-title\">$news_title</h2></a>"; // title
		echo "<p class=\"post-meta\">Posted by <a href=\"".SITE_URL."/user/info/index.php?user=$news_author\">$news_author</a>, "; // author
		echo "on $news_day, "; // date

		$commentq = "SELECT count(*) FROM ".TBL_COMMENTS." WHERE news_id=$news_id AND deleted=0";
		$commentr = mysql_query ($commentq);
		$commentcount = mysql_fetch_row($commentr);

		/* Display number of comments with link */
		echo "<a href=\"".SITE_URL."/news/index.php?action=show&#38;newsid=$news_id#comments\">{$commentcount[0]} comments</a>.</p>";
    echo "</div>";
	}
}

function displayOneItem($news_id, $checklogin, $username) {

	/* query for item */
	$q = "SELECT news_id,title,username,article,UNIX_TIMESTAMP(`postdate`) as date FROM ".TBL_NEWS." WHERE news_id=$news_id";
	$result = mysql_query ($q);

	/* if we get no results back, error out */
	if (mysql_num_rows ($result) == 0) {
		echo "<p>Bad news ID</p>";
		return;
	}

	$row = mysql_fetch_assoc($result);
	/* Places table row data into easier to use variables.*/
	$news_id = $row['news_id'];
	$news_title = $row['title'];
	$news_day = date("D jS M Y", $row['date']);
	$news_time = date("g:ia", $row['date']);
	$news_date = "$news_day at $news_time";
	$news = $row['article'];
	$news_author = $row['username'];

	/* Display the title */
	echo "<h1 class=\"post-title\">$news_title $postdate</h1>";
	/* Display the post meta */
	echo "<div class=\"post-meta\">Posted by <a href=\"".SITE_URL."/user/info/index.php?user=$news_author\">$news_author</a> on $news_date</div><hr>";
	/* Display the post content */
	echo "<div class=\"post-content\">$news</div><hr>";

  /* Depending on whether the user is logged in or not, a comment box or a notice is displayed */
	if($checklogin){
		/* Add a form where users can enter new comments */
		echo "<div class=\"well\">";
    echo "<h4>Leave a Comment</h4>";
		echo "<form role=\"form\" action=\"".SITE_URL."/include/process.php\" id=\"addcomment-form\" method=\"post\"><div class=\"form-group\">";
		echo "<textarea name=\"comment\" class=\"form-control\" rows=\"3\"></textarea></div>";
		echo "<input type=\"hidden\" name=\"username\" value=\"$username\">";
		echo "<input type=\"hidden\" name=\"newsid\" value=\"$news_id\">";
		echo "<input type=\"hidden\" name=\"addcomment\" value=\"1\">";
		echo "<input type=\"submit\" class=\"btn btn-primary\" name=\"submit\" value=\"Submit\">";
		echo "</form></div>";
	}
	/* Displays a notice  asking the user to log in if they want to add a comment */
	else{
		echo "<div class=\"alert alert-warning\">"
		."<strong>Note:</strong>"
		." To add a comment to this thread you must be logged in. Please log in using the form at the top of the page."
		."</div>";
	}

  /* Get number of comments */
	$comment_query = "SELECT count(*) FROM ".TBL_COMMENTS." WHERE news_id=$news_id AND deleted=0";
	$comment_result = mysql_query ($comment_query);
	$comment_row = mysql_fetch_row($comment_result);

	/* now show the comments */
	echo "<div id=\"comments\">";
	displayAllComments($news_id, $checklogin, $username);
	echo "</div>";
}


function editCommentPage($comment_id, $checklogin, $username){
	if($checklogin){

		/* query for item */
		$q = "SELECT * FROM ".TBL_COMMENTS." WHERE comment_id=$comment_id";
		$result = mysql_query ($q);

		/* if we get no results back, error out */
		if (mysql_num_rows ($result) == 0) {
			echo "<p>This comment was either deleted or does not exist.</p>";
			return;
		}
		$row = mysql_fetch_assoc($result);
		$comment = $row['comment'];
		$comment_author = $row['username'];

		if ($comment_author == $username){
			/* add a form where users can enter new comments */
      echo "<h3>emm... maybe I should have worded it differently</h3>";
			echo "<form action=\"".SITE_URL."/include/process.php\" method=\"post\">";
			echo "<textarea name=\"comment\" class=\"form-control\" rows=\"3\">$comment</textarea>";
			echo "<input type=\"hidden\" name=\"commentid\" value=\"$comment_id\" />";
			echo "<input type=\"hidden\" name=\"editcomment\" value=\"1\">";
			echo "<input type=\"submit\" name=\"submit\" value=\"Edit Comment\" class=\"btn btn-primary\">";
			echo "</form>";
		}
		else{
			echo "<p>You are unable to edit this comment as you did not originally post it.</p>";
		}
	}
}


function deleteCommentPage($comment_id, $news_id, $checklogin, $username){
	if($checklogin){
		$q = "SELECT * FROM ".TBL_COMMENTS." WHERE comment_id=$comment_id";
		$result = mysql_query ($q);

		/* if we get no results back, error out */
		if (mysql_num_rows ($result) == 0) {
			echo "<p>This comment was either deleted or does not exist.</p>";
			return;
		}
		$row = mysql_fetch_assoc($result);
		$comment = $row['comment'];
		$comment_author = $row['username'];

		if ($comment_author == $username){
			echo "<h3>Are you sure you want to delete this comment?</h3>";
			echo "<div class=\"rows\">";
      echo "<div class=\"col-md-1\">";
			echo "<form action=\"".SITE_URL."/include/process.php\" method=\"post\">";
			echo "<input type=\"hidden\" name=\"commentid\" value=\"$comment_id\">";
			echo "<input type=\"hidden\" name=\"deletecomment\" value=\"1\">";
			echo "<input type=\"submit\" name=\"submit\" value=\"Yes\" class=\"btn btn-danger btn-lg\">";
			echo "</form>";
      echo "</div>";

      echo "<div class=\"col-md-1\">";
			echo "<form action=\"".SITE_URL."/news/index.php?action=show&#38;newsid=$news_id\" method=\"post\">";
			echo "<input type=\"submit\" name=\"submit\" value=\"No\" class=\"btn btn-default btn-lg\">";
			echo "</form>";
      echo "</div>";
      echo "</div>";
		}else{
			echo "<p>You are unable to delete this comment as you did not originally post it.</p>";
		}
	}
}
?>
