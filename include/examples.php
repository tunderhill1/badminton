<!-- Here lie examples that might be useful for future use :) -Zack -->

<!-- 1. making the head of the dropdown clickable -->
<li role="presentation" class="dropdown">
  <a href="<?php   echo SITE_URL; ?>/news" role="button" aria-haspopup="true" aria-expanded="false">
    News <span class="caret"></span>
  </a>
  <ul class="dropdown-menu">
    <li><a href="<?php   echo SITE_URL; ?>/news/club/">Club News</a></li>
    <li><a href="<?php   echo SITE_URL; ?>/news/team/">Team News</a></li>
    <li><a href="http://www.bwfbadminton.org/" class="external">Badminton News</a></li>
  </ul>
</li>

<!-- 2. categorise news items -->
<?php   function getHeadlines($num, $typ, $page){
  $starting_article = ($page - 1) * 10;
	if($typ==0){
		$q2 = "SELECT news_id,title,username,UNIX_TIMESTAMP(`postdate`) as date FROM ".TBL_NEWS." ORDER BY postdate DESC LIMIT $starting_article, $num";
	}else{
		$q2 = "SELECT news_id,title,username,UNIX_TIMESTAMP(`postdate`) as date FROM ".TBL_NEWS." WHERE type=$typ ORDER BY postdate DESC LIMIT $starting_article, $num";
	}
//The news categories are not longer in use because I think only the club news is relevant on the website, especially when there is a link to an external badminton news site.
?>
