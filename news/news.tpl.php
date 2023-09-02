<?php 

$checklogin = $session->logged_in;
$username = $session->username;

$news_id = htmlentities(mysql_real_escape_string($_GET['newsid']));
$comment_id = htmlentities(mysql_real_escape_string($_GET['commentid']));

if(isset($_GET['newsid'])){
	if((string)$news_id === (string)(int)$news_id){/**/}else{ echo "invalid news_id. haha. die;"; die; }
}

if(isset($_GET['commentid'])){
	if((string)$comment_id === (string)(int)$comment_id){/**/}else{ echo "invalid comment_id. bye bye..."; die; }
}

$to_do = htmlentities(mysql_real_escape_string($_GET['action']));

switch($to_do){
	case 'show':
    echo "<div class=\"content\">"
    ."<div class=\"container\">"
    ."<div class=\"row\">"
    ."<div class=\"col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1\">";
   		displayOneItem($news_id, $checklogin, $username);
    echo "</div></div></div></div>";
    break;

	case 'edit':
    echo "<div class=\"content\">"
    ."<div class=\"container\">"
    ."<div class=\"row\">"
    ."<div class=\"col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1\">";
		editCommentPage($comment_id, $checklogin, $username);
		echo "</div></div></div></div>";
    break;

	case 'delete':
    echo "<div class=\"content\">"
    ."<div class=\"container\">"
    ."<div class=\"row\">"
    ."<div class=\"col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1\">";
    	deleteCommentPage($comment_id, $news_id, $checklogin, $username);
		echo "</div></div></div></div>";
    break;

	default:
    echo"<div class=\"content\">"
    ."<div class=\"jumbotron\">"
    ."<div class=\"container\">"
    ."<h1>News Archive</h1>"
    ."<p>All our news items in one place.</p>"
    ."</div>"
    ."</div>"
    ."<div class=\"container\">";
	  getHeadlines(999,0);
    echo "</div></div>";
	}
?>
