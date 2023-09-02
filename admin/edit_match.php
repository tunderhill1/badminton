<?php  
$pagetitle = "Edit Match";
include("../include/session.php");
include("match_admin_fns.php");

if($session->userlevel >= LEVEL_COMMITTEE){
    include("../include/header.tpl.php");
    ?>
    <section id="main" class="wrapper">
        <div class="container">
            <header class="major special">
                <h2>Edit Match</h2>
            </header>
        <?php  
        switch(mysql_real_escape_string($_GET['action'])){
            case 'edit':
                $match_id = $_GET['id'];
                if((string)$match_id===(string)(int)$match_id){
                    getForEdit($match_id);
                }
                break;
            case 'delete':
                $match_id = $_GET['id'];
                if((string)$match_id===(string)(int)$match_id){
                    getForDelete($match_id);
                }
                break;
            default:
                displayEditMatches();
        }
        ?>
    </div>
</section>
    <?php   include("../include/footer.tpl.php");
}else{ header("Location: " . SITE_URL); }
?>
