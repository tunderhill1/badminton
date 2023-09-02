<?php  
$pagetitle = "Edit Session";
include("../include/session.php");
include("session_admin_fns.php");

if($session->userlevel >= LEVEL_ADMIN){
    include("../include/header.tpl.php");
    ?>
    <section id="main" class="wrapper">
        <div class="container">
            <header class="major special">
                <h2>Edit Session</h2>
            </header>
        <?php  
        switch(mysql_real_escape_string($_GET['action'])){
            case 'edit':
                $sess_id = $_GET['id'];
                if((string)$sess_id===(string)(int)$sess_id){
                    getForEdit($sess_id);
                }
                break;
            case 'delete':
                $sess_id = $_GET['id'];
                if((string)$sess_id===(string)(int)$sess_id){
                    getForDelete($sess_id);
                }
                break;
            default:
                displayEditSessions();
        }
        ?>
    </div>
</section>
    <?php   include("../include/footer.tpl.php");
}else{ header("Location: " . SITE_URL); }
?>
