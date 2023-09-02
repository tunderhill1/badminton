<?php 

date_default_timezone_set('Europe/London');

/* This file is intended to group all constants to
* make it easier for the site administrator to tweak
* the login script. */

/**
* This constant defines the site url and is used to
* help maintain the links by making it eaiser to
* create them.
*/
define("SITE_URL", "/acc/badminton");


/**
* Database Constants - these constants are required
* in order for there to be a successful connection
* to the MySQL database. Make sure the information is
* correct.
*/
define("DB_SERVER", "localhost");
define("DB_USER", "acc_badminton");
define("DB_PASS", "f6PAbfR6ok32ba0iJJp4iD5oXpQ");
define("DB_NAME", "acc_badminton");

/**
* Database Table Constants - these constants
* hold the names of all the database tables used
* in the script.
*/
define("TBL_USERS", "usr");
define("TBL_NEWS", "news");
define("TBL_COMMENTS", "comments");
define("TBL_SESSION_LIST", "session_list");
define("TBL_SESSION_BOOKING", "session_booking");
define("TBL_SESSION_MANAGER", "session_manager");
define("TBL_MATCH_LIST", "match_list");
define("TBL_MATCH_BOOKING", "match_booking");
define("TBL_DOWNLOADS", "downloads");
define("TBL_PAGELOADS", "page_loads");

define("TBL_NOSHOWS", "noshows");

//Ryan was here  DEPRECATED
define("TBL_SEESION_PLAYER", "session_player"); //you misspelled "SESSION". 0/10.

/**
* Special Names and Level Constants - the admin
* page will only be accessible to the user with
* the admin name and also to those users at the
* admin user level. Feel free to change the names
* and level constants as you see fit, you may
* also add additional level specifications.
* Levels must be digits between 0-9.
*/

# User level for the web editor
define("LEVEL_ADMIN", 4);
define("LEVEL_COMMITTEE", 3);
define("LEVEL_MANAGER", 2);
define("LEVEL_MEMBER",  1);
define("LEVEL_GUEST", 0);
define("LEVEL_NULL", NULL);

/**
* Cookie Constants - these are the parameters
* to the setcookie function call, change them
* if necessary to fit your website. If you need
* help, visit www.php.net for more info.
* <http://www.php.net/manual/en/function.setcookie.php>
*/
define("COOKIE_EXPIRE", 60*60*24*30);  // 30 days
define("COOKIE_PATH", "/acc/badminton/"); // path where cookie is available

/**
* Email Constants - these specify what goes in
* the from field in the emails that the script
* sends to users, and whether to send a
* welcome email to newly registered users.
*/
define("EMAIL_FROM_NAME", "ICU Badminton");
define("EMAIL_FROM_ADDR", "badminton@imperial.ac.uk");
define("EMAIL_ACTIVATION", true);

/**
* The locking_time constant is the time before
* the session start the session is locked, in seconds.
*/
define("SESSION_LOCKING_TIME", 90*60);
/**
 * Training locking time is the time before training session start the session is locked. This is set to 12 hours before.
 */
define("TRAINING_LOCKING_TIME", 12*60*60);

/** The maximum amount of time a session can be booked
* in advance, in seconds. Currently: 21 days
*/
define("SESSION_BOOKING_ADVANCE", 4*7*24*60*60);

/**
* The maximum number of days between two
* consecutive noshows in order to issue a 
* temporary ban from booking. Currently: 30 days
*/
define("NOSHOW_BAN_FREQUENCY", 30);

/**
* The period over which a player is banned from
* booking social sessions, if they have not shown
* up multiple times. Currently: 14 days
*/
define("NOSHOW_BAN_PERIOD", 14*24*60*60);
?>

