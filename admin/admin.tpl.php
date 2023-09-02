
    <?php   if($session->userlevel >= LEVEL_ADMIN){ ?>
        <section id="main" class="wrapper">
    	    <div class="container">
    	        <header class="major special">
    	            <h2>Admin Centre</h2>
    	        </header>
    			<div class="table-wrapper">
                    <table>
                        <tr>
                            <td><b>News</b></td>
                            <td>
                                <a href="news.php">More info</a>
                            </td>
                        </tr>

                        <tr>
                            <td><b>Sessions</b></td>
                            <td>
                                <ul class="inline">
                                    <li><a href="create_session.php">Create</a> a session</li>
                                    <li><a href="edit_session.php">Edit</a> a session</li>
                                    <li><a href="session_all_list.php">Session list</a> - all sessions ever</li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td><b>Social Matches</b></td>
                            <td>
                                <ul class="inline">
                                    <li><a href="create_match.php">Create</a> a match</li>
                                    <li><a href="edit_match.php">Edit</a> a match</li>
                                    <li><a href="match_all_list.php">Match list</a> - all matches ever</li>
                                </ul>
                            </td>    
                        </tr>

                        <tr>
                            <td><b>Users</b></td>
                            <td>
                                <ul class="inline">
                                    <li>View <a href="list_all.php">all members</a></li>
                                    <li>View <a href="auto_activate_accounts_view.php">auto-activated</a> list</li>
                                    <li>View <a href="list_unactivated.php">un-activated user list</a></li>
                                    <li>View <a href="list_comms.php">committee</a> list</li>
                                    <li>View <a href="list_managers.php">managers</a> list</li>
                                    <li>View activated <a href="activated_emails.php">emails</a></li>
                                    <li>Force auto-activate <a href="auto_activate_accounts_do.php">now</a></li>
                                    <li><a href="activate_user.php">Activate</a> a user</li>
                                    <li><a href="deactivate_user.php">De-activate</a> a user</li>
                                    <li><a href="change_user_level.php">Change user level</a></li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>
<?php  
}else if($session->userlevel >= COMMITTEE){
    header("Location: ". SITE_URL ."/committee/");
} else { header("Location: " . SITE_URL); }
?>
