<section id="main" class="wrapper">
    <div class="container">
        <header class="major special">
            <h2>Club Sessions</h2>
            <p>If you book a session, please ensure you show up.</p>
        </header>
        <div class="row uniform">
            <div class="6u 12u$(small)">
                <ul>
                    <li>You can book 1 Monday <b>or</b> Tuesday session at a time.</li>
                    <li>You can also book 1 Thursday (coaching) session.</li>
                    <li>sessions are locked <b><?php   echo SESSION_LOCKING_TIME / 60; ?> minutes</b> before social sessions and <b><?php   echo TRAINING_LOCKING_TIME / 60 / 60; ?> hours</b> before coaching sessions start.</li>
                    <li>There is a <a href="#" onclick="openPenaltyModal()">penalty system</a> in place!</li>
                </ul>
                <hr>
                <p>
                    Our Thursday coaching sessions will be run by team members to help you improve your game. These are also a great way to see what the <a href="<?php   echo SITE_URL; ?>/about/">Social Team</a> could be like.
                    <li>The cost to attend training sessions is £6.0.</li>
                </p>
                <h4>To pay for coaching sessions, go to our <a href="https://www.imperialcollegeunion.org/shop/student-groups/382" target="_blank">union shop</a></h4>
                </hr>
                <p>

                </p>
            </div>
            <div class="1u 0u(medium)"></div>
            <div class="5u$ 12u$(small)">
                <h4 class="table-toggle" id="term-1-toggle" onclick="toggle(1)">Term 1 Details</h4>
                <div class="table-wrapper" id="term-1" style="display: none">
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>No. of Courts</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Monday</td>
                                <td>16:00 - 18:00</td>
                                <td>4</td>
                            </tr>
                            <tr>
                                <td>Tuesday</td>
                                <td>18:00 - 20:00</td>
                                <td>4</td>
                            </tr>
                            <tr>
                                <td>Thursday</td>
                                <td>18:00 - 20:00</td>
                                <td>4</td>
                            </tr>
                        </tbody>
                        <tfoot><tr></tr></tfoot>
                    </table>
                </div>
                <h4 class="table-toggle" id="term-2-toggle" onclick="toggle(2)">Term 2 Details</h4>
                <div class="table-wrapper" id="term-2" style="display: none">
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>No. of Courts</th>
                            </tr>
                        </thead>
                        <!--
                        <tbody>
                            <tr>
                                <td>Monday</td>
                                <td>16:00 - 18:00</td>
                                <td>4</td>
                            </tr>
                            <tr>
                                <td>Tuesday</td>
                                <td>18:00 - 20:00</td>
                                <td>4</td>
                            </tr>
                            <tr>
                                <td>Sunday</td>
                                <td>08:00 - 10:00</td>
                                <td>4</td>
                            </tr>
                        </tbody>
                        -->
                        <tfoot>
                        <tr>
                        TBC
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <h4 class="table-toggle" id="term-3-toggle" onclick="toggle(3)">Term 3 Details</h4>
                <div class="table-wrapper" id="term-3" style="display: none">
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>No. of Courts</th>
                            </tr>
                        </thead>
                        <tbody>
                            TBC
                        </tbody>
                        <tfoot><tr></tr></tfoot>
                    </table>
                </div>
            </div>
        </div>

<?php 
    if($session->logged_in){
    	if(!$session->activated){
            // non-activated account warning
    		echo "<div class=\"alert warning\">Your user account has not been activated. Activation is required to book sessions. To become activated, you must purchase the membership for the Imperial College Badminton Club. Click <a href=\"".SITE_URL."/about/membership/\">here</a> to find out more information.</div>";
    	}
    } else {
        // not logged in
    	echo "<div class=\"alert warning\">To book a session you must be logged in.</div>";
    }

    if(isset($_SESSION['addbookingsuccess'])){
    	/* Registration was successful */
    	if($_SESSION['addbookingsuccess']){
    		echo "<div class=\"alert info\">Session booked! Only book a session if you can attend.</div>";
    	}
    	/* Registration failed */
    	else{
    		echo "<div class=\"alert warning\">Sorry, an error has occurred and your booking request could not be completed. Please try again at a later time.</div>";
    	}
    	unset($_SESSION['addbookingsuccess']);
    }

    if(isset($_SESSION['addmanagersuccess'])){
    	/* Registration was successful */
    	if($_SESSION['addmanagersuccess']){
    		echo "<div class=\"alert info\">You are now managing!</div>";
    	}
    	/* Registration failed */
    	else{
    		echo "<div class=\"alert warning\">Sorry, an error has occurred and your booking request could not be completed. Please try again at a later time.</div>";
    	}
    	unset($_SESSION['addmanagersuccess']);
    }

    if(isset($_SESSION['removebookingsuccess'])){
    	/* Registration was successful */
    	if($_SESSION['removebookingsuccess']){
    		echo "<div class=\"alert info\">Session booking removed! You can now book another session</div>";
    	}
    	/* Registration failed */
    	else{
    		echo "<div class=\"alert warning\">Sorry, an error has occurred and your booking request could not be completed. Please try again at a later time.</div>";
    	}
    	unset($_SESSION['removebookingsuccess']);
    }

    if(isset($_SESSION['removemanagesuccess'])){
    	/* Registration was successful */
    	if($_SESSION['removemanagesuccess']){
    		echo "<div class=\"alert alert-success\"><strong>Manager removed!</strong></div>";
    	}else{
    		echo "<div class=\"alert alert-danger\"><strong>Manager removal failed!</strong>"
    		." We're sorry, but an error has occurred and your booking request could not be completed. Please try again at a later time.</div>";
    	}
    	unset($_SESSION['removemanagesuccess']);
    }

    // display errors set in '/include/session.php' e.g. no places left
    if($form->num_errors > 0){
    	if($form->error("username")){
    		echo "<div class=\"alert warning\">".$form->error("username")."</div>";
    	}
    }

    $session->getNextSession(); // display user's next session

    // show upcoming sessions
    include("view/display_session_info.php");
    displaySessions(12,1); // 12 sessions, 1 = in the future
?>
    </div>
</section>
<div class="modal" id="penalty">
    <div class="modal-content">
        <h4>Penalty System</h4>
        We are introducing a penalty system <b>(2 no-shows, 2 weeks ban!)</b> for those who book into sessions but do not turn up. You will receive an email notification if you don't show up to a session. If this happens twice within 30 days, your account will be blocked for 2 weeks, and you won't be able to book.
        <br><br>
        Remember that you can edit your booking up to 1.5 hours before the session starts. If you can't unbook, please contact the session managers.
        <hr>
        <ul style="text-align: left">
            <li><b>1st no-show</b>: you will get a warning</li>
            <li><b>2nd no-show within 30 days</b>: you will be banned from playing for 2 weeks, starting from (and including) the day you missed.
        </ul>
        <a href="#" class="close" onclick="closePenaltyModal()">&#10060;</a>
    </div>
</div>
<script>
    function toggle(termNumber) {
        const table = document.getElementById("term-" + termNumber);
        const toggle = document.getElementById("term-" + termNumber + "-toggle");

        if (table.style.display != "block") {
            console.log(table.style.display);
            table.style.display = "block";
            toggle.classList.add("on");
        } else {
            console.log("hiding " + termNumber);
            table.style.display = "none";
            toggle.classList.remove("on");
        }
    }

    document.getElementById('penalty').addEventListener('click', function(e) {
        e = window.event || e;
        if (this === e.target) {
            closePenaltyModal();
        }
    });

    function openPenaltyModal() {
        document.getElementById('penalty').classList.add('open');
    }

    function closePenaltyModal() {
        document.getElementById('penalty').classList.remove('open');
    }
</script>