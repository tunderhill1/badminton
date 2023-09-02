<?php   include("header_func_only.tpl.php") ?>
<?php   if (isset($banner)): ?>
<header id="header" class="alt">
<?php   else: ?>
<header id="header">
<?php   endif ?>
    <!-- <h1><a href="<?php   #echo SITE_URL; ?>">ICU Badminton</a></h1> -->
    <h1><a href="<?php   echo SITE_URL; ?>"><img src="<?php   echo SITE_URL; ?>/images/logo.png" alt="ICU Badminton"></a></h1>
    <?php   if (!$session->logged_in): ?>
        <a href="<?php   echo SITE_URL ?>/user/login/" id="login-nav">Login</a>
    <?php   endif ?>
    <a href="#nav">Menu</a>
</header>
<nav id="nav">
    <ul class="links">
        <li><a href="<?php   echo SITE_URL; ?>">Home</a></li>
        <li><a href="<?php   echo SITE_URL; ?>/about/">About</a></li>
        <li><a href="<?php   echo SITE_URL; ?>/sessions/">Club Sessions</a></li>
        <li><a href="<?php   echo SITE_URL; ?>/matches/">Social Matches</a></li>

        <?php   if (!$session->logged_in): ?>
            <li><a href="<?php   echo SITE_URL; ?>/user/login/" id="login-menu">Login</a></li>
            <li><a href="<?php   echo SITE_URL; ?>/about/membership/">Join Us</a></li>
        <?php   endif ?>

        <!-- <li><a href="<?php   echo SITE_URL; ?>/about/tournament/">Tournament</a></li> -->
        <li><a href="<?php   echo SITE_URL; ?>/about/sponsors/">Sponsors</a></li>
        <li><a href="<?php   echo SITE_URL; ?>/about/comm/">Committee</a></li>
        <li><a href="<?php   echo SITE_URL; ?>/about/freshers/">Freshers Week 21-22</a></li>
        <li><a href="<?php   echo SITE_URL; ?>/about/contact/">Contact Us</a></li>

        <?php   if ($session->logged_in): ?>
            <hr>
            <?php   if ($session->userlevel >= LEVEL_COMMITTEE): ?>
                <li><a href="<?php   echo SITE_URL; ?>/committee/">Committee Centre</a></li>
            <?php   endif ?>

            <?php   if ($session->userlevel >= LEVEL_ADMIN): ?>
                <li><a href="<?php   echo SITE_URL; ?>/admin/">Admin Centre</a></li>
            <?php   endif ?>

            <li><a href="<?php   echo SITE_URL; ?>/user/info/index.php?user=<?php   echo $session->username; ?>">My Account</a></li>
            <li><a href="<?php   echo SITE_URL; ?>/include/process.php">Logout</a></li>
        <?php   endif ?>
    </ul>
</nav>
