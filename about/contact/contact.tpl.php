<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<section id="main" class="wrapper">
    <div class="container">
        <header class="major special">
            <h2>Contact Us</h2>
            <p>Feel free to ask questions or give feedback!</p>
        </header>

        <?php   if (isset($_SESSION['contactus'])) {
                if ($_SESSION['contactus']) {
                    echo "<div class=\"alert info\">Thanks for contacting us. We'll be in touch soon.</div>";
                } else {
                    echo "<div class=\"alert warning\">Sorry, an error has occurred, please try again later.</div>";
                }
                unset($_SESSION['contactus']);
            }
        ?>

        <form method="POST" action="<?php   echo SITE_URL; ?>/include/process.php">
            <div class="row uniform 50%">
                <div class="6u 12u$(xsmall)">
                    <input type="text" name="name" placeholder="Name" value="<?php   echo $form->value("name") ?>">
                    <?php   if ($form->error("name")): ?>
                        <span class="help-block">
                            <strong><?php   echo $form->error("name") ?></strong>
                        </span>
                    <?php   endif ?>
                </div>
                <div class="6u$ 12u$(xsmall)">
                    <input type="text" name="email" placeholder="Email" value="<?php   echo $form->value("email") ?>">
                    <?php   if ($form->error("email")): ?>
                        <span class="help-block">
                            <strong><?php   echo $form->error("email") ?></strong>
                        </span>
                    <?php   endif ?>
                </div>
                <div class="12u$">
                    <textarea name="message" placeholder="Message" rows="4"><?php   echo $form->value("message") ?></textarea>
                    <?php   if ($form->error("message")): ?>
                        <span class="help-block">
                            <strong><?php   echo $form->error("message") ?></strong>
                        </span>
                    <?php   endif ?>
                </div>
                <input type="hidden" name="contactus" value="1">
                <div class="row uniform">
                    <div class="g-recaptcha" data-sitekey="6Lc3jRQTAAAAAB5vDMM4aln38oHmEXYwaKEiqJNG"></div>
                    <?php   if ($form->error("captcha")): ?>
                        <span class="help-block">
                            <strong><?php   echo $form->error("captcha") ?></strong>
                        </span>
                    <?php   endif ?>
                </div>
                <div class="12u$">
                    <ul class="actions">
                        <li><input type="submit" value="Submit"></li>
                        <li><input type="reset" class="alt" value="Reset"></li>
                    </ul>
                </div>
            </div>
        </form>
    </div>
</section>
