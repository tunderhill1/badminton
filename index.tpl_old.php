<section id="banner">
    <img class="icon" id="logo" src="<?php   echo SITE_URL; ?>/images/crest.png">
    <h2>Imperial College Badminton Club</h2>
    

    <!--<p><i>Insert Sub-Header</i></p>-->
    <!-- <p class="long"><span>Now in association with</span></p>
    <p class="long"><a href="<?php   echo SITE_URL; ?>/about/sponsors/"><img src="<?php   echo SITE_URL; ?>/images/chao-pai.png" class="brand"></a><span> and </span><a href="<?php   echo SITE_URL; ?>/about/sponsors/"><img src="<?php   echo SITE_URL; ?>/images/ibadds.png" class="brand"></a></p>
    <ul class="actions">
        <?php   if ($session->logged_in): ?>
            <li><a href="<?php   echo SITE_URL; ?>/sessions/" class="button big special">Book Sessions</a></li>
        <?php   else: ?>
            <li><a href="<?php   echo SITE_URL; ?>/about/membership/" class="button big special">Join Us</a></li>
        <?php   endif ?>
    </ul> -->

<?php  
// already logged in
if($session->logged_in){ ?>
    <header class="major special">
        <p>You are now logged in!</p>
        <ul class="actions">
            <li><a href="<?php   echo SITE_URL; ?>/sessions/" class="button big special">Book Sessions</a></li>
        </ul>
    </header>
<?php
} else{ ?>

    <header class="major special">
        <p>Sign in to your account to book sessions.</p>
    </header>

    <form action="<?php   echo SITE_URL; ?>/include/process.php" method="POST">
        <div class="row uniform flex">
            <div class="3u 12u$(small)">
                <input type="text" name="lguser" value="<?php   echo $form->value("lguser") ?>" placeholder="Enter your username" required autofocus>
            </div>
        </div>

        <div class="row uniform flex" >
            <div class="3u 12u$(small)">
                <input type="password" name="lgpass" placeholder="Enter your password" required>
                <?php   if ($form->error("login")): ?>
                    <span class="help-block">
                        <strong><?php   echo $form->error("login") ?></strong>
                    </span>
                <?php   endif ?>
            </div>
        </div>
        <input type="hidden" name="sublogin" value="1">
        <div class="row uniform">
            <!-- <div class="10u 12u$(small)"> -->
            <div class="12u$">
                <ul class="actions">
                    <li><input type="submit" value="Login"></li>
                    <li><a class="button alt" href="<?php   echo SITE_URL; ?>/user/forgotpass/">Forgot Your Password?</a></li>
                </ul>
            </div>
            <div class="12u$">
                <ul class="actions">
                    <li>
                    <input type="checkbox" name="remember" id="remember" checked>
                    <label for="remember">Remember me</label>
                    </li>
                </ul>
            </div>
        </div>
    </form>
    <?php   } ?>

    <header class="major special">
        <p>We are also on social media!</p>
        <ul class="icons">
            <li>
                <a href="https://www.facebook.com/groups/212736565421688" class="icon fa-facebook">
                    <span class="label">Facebook</span>
                </a>
            </li>
            <li>
                <a href="https://www.instagram.com/icbadminton" class="icon fa-instagram">
                    <span class="label">Instagram</span>
                </a>
            </li>
            <li>
                <a href="mailto:badminton@imperial.ac.uk" class="icon fa-envelope">
                    <span class="label">Email</span>
                </a>
            </li>
        </ul>
    </header>
</section>
<section class="wrapper gray with-top-alert">
    <div class="new before"></div>
    <div class="alert info new">
        <h3>What's New?</h3>
        <ol>
            <li>New <a href="<?php   echo SITE_URL ?>/about/comm/">Committee</a> - say hello!</li>
            <li>Even more <a href="<?php   echo SITE_URL ?>/sessions/">Club Sessions</a></li>
            <li>Changes due to Covid-19 (coming soon)</li>
        </ol>
    </div>
    <div class="inner">
        <header class="major">
            <h2>Why Join Us?</h2>
            <p class="long"> As one of the largest clubs at Imperial, we guarantee that you'll find new friends to play badminton with!</p>
        </header>
        <article class="feature left">
            <span class="image"><img src="<?php   echo SITE_URL; ?>/images/teams.jpg"></span>
            <div class="content">
                <h2>Teams</h2>
                <p>We have <a href="<?php   echo SITE_URL; ?>/about/">4 teams</a> for experienced players, playing in the BUCS and LUSL leages.</p>
                <p>Social players are also able to play for the social team against other universities. <a href="<?php   echo SITE_URL; ?>/matches/">See our upcoming matches</a>.</p>
            </div>
        </article>
        <article class="feature right">
            <span class="image"><img src="<?php   echo SITE_URL; ?>/images/ethos-play.jpg"></span>
            <div class="content">
                <h2>Social Sessions</h2>
                <p>We host multiple weekly sessions at <a target="_blank" href="https://goo.gl/maps/3zAfCrBWMD7s7AyPA">Ethos Sports Centre</a> for all members, whether you're an experienced player or complete beginner.</p>
                <ul class="actions">
                    <li><a href="<?php   echo SITE_URL ?>/sessions/" class="button alt">Upcoming Sessions</a></li>
                </ul>
            </div>
        </article>
        <!-- <article class="feature right">
            <span class="image"><img src="<?php   echo SITE_URL; ?>/images/racket-shuttle.jpg"></span>
            <div class="content">
                <h2>Rackets and Shuttles</h2>
                <p>We have rackets available and provide shuttles in all of our club sessions. No need to prepare - you can just come and play!</p>
                <p>We, of course, encourage you to bring along your own racket if you have one. Be sure to check out our <a href="<?php   echo SITE_URL; ?>/about/sponsors/">sponsors</a>.</p>
            </div>
        </article> -->
        <article class="feature left">
            <span class="image"><img src="<?php   echo SITE_URL; ?>/images/coaching.jpg"></span>
            <div class="content">
                <h2>Coaching</h2>
                <p>We offer the option of attending <a href="<?php   echo SITE_URL ?>/sessions/">coaching sessions</a> for social members.</p>
                <p>Your badminton skills will be sharpened with the help of our team members.</p>
            </div>
        </article>
        <article class="feature right">
            <span class="image"><img src="<?php   echo SITE_URL; ?>/images/social.jpg"></span>
            <div class="content">
                <h2>Social Events</h2>
                <p>From our Welcome Dinner to our club favourite Karaoke, we hold social events throughout the year!</p>
                <p>Join our <a href="https://www.facebook.com/groups/212736565421688">Facebook group</a> to be updated about any upcoming events.</p>
            </div>
        </article>
    </div>
</section>
