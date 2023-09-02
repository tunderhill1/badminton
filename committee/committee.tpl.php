
    <?php   if($session->userlevel >= LEVEL_COMMITTEE){ ?>
        <section id="main" class="wrapper">
    	    <div class="container">
    	        <header class="major special">
    	            <h2>Committee Centre</h2>
    	        </header>
    			<div class="table-wrapper">
                    <table>
                        <tr>
                            <td><b>Sessions</b></td>
                            <td>
                                <ul class="inline">
                                    <li><a href="../admin/session_all_list.php">Session list</a> - all sessions ever</li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td><b>Social Matches</b></td>
                            <td>
                                <ul class="inline">
                                    <li><a href="../admin/create_match.php">Create</a> a match</li>
                                    <li><a href="../admin/edit_match.php">Edit</a> a match</li>
                                    <li><a href="../admin/match_all_list.php">Match list</a> - all matches ever</li>
                                </ul>
                            </td>    
                        </tr>

                        <tr>
                            <td><b>Users</b></td>
                            <td>
                                <ul class="inline">
                                    <li>View <a href="../admin/list_all.php">all members</a></li>
                                    <li>View <a href="../admin/list_comms.php">committee</a> list</li>
                                    <li>View <a href="../admin/list_managers.php">managers </a> list</li>

                                    <?php   if($session->username == "cc2819" || $session->username == "tjc119"){#if admin or president ?>
                                    <li><a href="../admin/manual_add_user.php">Manual add</a> user <b>(only admin and president)</b></li>
                                    <?php  } ?>
                                </ul>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </section>
<?php  
}else{ header("Location: " . SITE_URL); }
?>
