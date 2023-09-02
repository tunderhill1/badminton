<?php  
include("../include/session.php");

if($session->userlevel >= LEVEL_ADMIN){
  $pagetitle = "News Help";
  include("../include/header.tpl.php");
  ?>
  <div class="content container">
    <h2>Creating and Editing News Items</h2>
    <p>This cannot be done through this website.</p>
    <p>This is due to the server running mod_security for us, this software scans POST data for things like SQL injections and has some objection to HTML tags. As News Items must contain HTML tags, they cannot be submitted as mod_sec will stop them. mod_sec could be disabled, however this would decrease site security and has been decided against.</p>
    <p>The web admin can create and edit news items by directly accessing the database.</p>
    <p>If you want colourfull etc. formatting, please draft any News Item in HTML or in Word (the web admin can then convert Word to HTML automatically using the many free online converters*)</p>
    <p><i>*(web admin should ensure that this auto converter does not use "div" tags, as these will mis-format the page)</i></p>
  </div>
  <?php   include("../include/footer.tpl.php"); ?>
<?php  
}else{ header("Location: " . SITE_URL); }
?>
