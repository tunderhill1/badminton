<section id="main" class="wrapper">
    <div class="container">
<?php  
  function userPosition($userlevel){
    if($userlevel == LEVEL_ADMIN){ return "Site Admin"; }
    if($userlevel == LEVEL_COMMITTEE){ return "Committee Member"; }
    if($userlevel == LEVEL_MANAGER){ return "Session Manager"; }
    if($userlevel == LEVEL_MEMBER){ return "Club Member"; }
    if($userlevel == LEVEL_GUEST){ return "Non-member"; }
    // else
    return "Guest";
  }

  if($session->userlevel>=LEVEL_MEMBER || $session->logged_in){

    $req_user = htmlentities(mysql_real_escape_string($_GET['user']));

    if($database->userExists($req_user)){

      $name_array = $database->getNamefromUsername($req_user);
      $name = $name_array['firstname']." ".$name_array['lastname'];
      if($name_array['nickname']!=""){
        $name = $name." (".$name_array['nickname'].")";
      }

      $usr_data = ldap_get_info($req_user);
      $userinfo = $database->getUserInfo($req_user);
?>
        <header class="major special">
            <h2><?php   echo $name ?></h2>
        </header>
      <div class="table-wrapper">
          <table>
              <tr><th>Name</th><td><?php   echo $name; ?></td></tr>
              <tr><th>Position</th><td><?php   echo userPosition($userinfo['usr_lvl']) ?></td></tr>
              <tr><th>Course</th><td><?php   echo $usr_data[0]; ?></td></tr>
              <tr><th>Level</th><td><?php   echo $usr_data[1]; ?></td></tr>
              <tr><th>Department</th><td><?php   echo $usr_data[2]; ?></td></tr>
              <tr><th>Campus</th><td><?php   echo $usr_data[4]; ?></td></tr>
              <tr><th>E-mail</th><td><?php   echo $database->getEmailfromUsername($req_user) ?></td></tr>
          </table>
      </div>
      <?php   if($session->username==$req_user){ ?>
          <ul class="actions">
              <li><a href="<?php   echo SITE_URL; ?>/user/accountedit/" class="button">Edit my account</a></li>
          </ul>
      <?php   } ?>
		<p><i>(Course details only available for current Imperial students)</i></p>
	<?php  
}else{ echo "<div class=\"alert warning\">Username does not exist.</div>"; }

}else{ echo "<div class=\"alert warning\">Only club members can view this page.</div>"; }
?>

  </div>
</section>
