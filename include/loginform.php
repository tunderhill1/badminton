<?php   if($session->logged_in){	?>
<ul class="nav navbar-nav navbar-right">
  <li role="presentation" class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
      <?php   if($session->activated==0) { ?>
        <font color="red"> Your account is unactivated! </font><i class="fa fa-user"></i>
      <?php   } else { ?>
        <i class="fa fa-user"></i>
      <?php   } echo $session->username; ?>
      <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
      <li><a href="<?php   echo SITE_URL; ?>/user/info/index.php?user=<?php   echo $session->username; ?>">Profile</a></li>
      <?php   if($session->userlevel>=LEVEL_ADMIN){ ?>
      <li><a href="<?php   echo SITE_URL; ?>/admin/">Admin</a></li>
      <?php   } ?>
      <li><a href="<?php   echo SITE_URL; ?>/include/process.php">Logout</a></li>
    </ul>
  </li>
</ul>
  <?php   }else{  // not logged in, display form... or tried login errors ?>
  <form action="<?php   echo SITE_URL; ?>/include/process.php" method="post" class="navbar-form navbar-right" role="search">
    <div class="form-group">
      <input type="text" class="form-control" name="lguser" placeholder="Username">
    </div>
    <div class="form-group">
      <input type="password" class="form-control" name="lgpass" placeholder="Password">
    </div>
    <div class="btn-group" data-toggle="buttons">
      <label class="btn btn-primary">
      <input type="checkbox" name="remember">Remember Me
      </label>
    </div>
    <button type="submit" class="btn btn-default">Sign In</button>
    <input type="hidden" name="sublogin" value="1">
  </form>
  <?php   } ?>
