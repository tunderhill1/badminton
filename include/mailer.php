<?php
/* The Mailer class is meant to simplify the task of sending
 * emails to users. Note: this email system will not work
 * if your server is not setup to send mail.
 * If you are running Windows and want a mail server, check
 * out this website to see a list of freeware programs:
 * <http://www.snapfiles.com/freeware/server/fwmailserver.html>
 */

class Mailer
{
   // sends the newly generated password to the user's email
   function sendNewPass($user, $email, $pass){
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "ICU Badminton - Password Reset";
      $body = $user.",\n\n"
             ."We've generated a new password for you at your "
             ."request, you can use this new password with your "
             ."username to log in to the Imperial College Badminton Club website.\n\n"
             ."Username: ".$user."\n"
             ."New Password: ".$pass."\n\n"
             ."It is recommended that you change your password "
             ."to something that is easier to remember, which "
             ."can be done by going to your Profile page "
             ."after signing in.";

      return mail($email,$subject,$body,$from);
   }

   function sendContact($name, $email, $message) {
       $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
       $subject = "New Message From {$name}";
       $body = "{$name} at {$email} has left a message:\n\n{$message}";

       return mail("badminton@ic.ac.uk",$subject,$body,$from);
   }

   function sendBookingWarning($name, $email, $session_date){
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "ICU Badminton - Social Session No-Show Warning";
      $body = "Dear ".$name.",\n\n"
                ."You were booked into an ICU Badminton Social Session on "
                .$session_date.".\n"
                ."It was noted that you did not show up to the session, meaning that we are sending you a warning.\n\n"
            		."Due to the demand of sessions in our society, we highly value that members do not book if they know they can't make it. "
                ."Sessions are locked 90 minutes before they start. "
            		."If it's too late to unbook, you can also email the managers before the session starts.\n"
            		."Because we have now given you a warning, if you don't show up to another session within the next month,"
            		." you will be given a ban from booking for 2 weeks.";

      // return true; //for testing
      return mail($email,$subject,$body,$from);
   }

   function sendBookingBan($name, $email, $session_date){
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "ICU Badminton - Social Session Temporary Ban";
      $body = "Dear ".$name.",\n\n"
                ."You were booked into an ICU Badminton Social Session on "
                .$session_date.".\n"
                ."It was noted that you did not show up to the session, and that this is the second incident in the past ".NOSHOW_BAN_FREQUENCY." days. "
                ."Therefore we are temporarily blocking your user from booking sessions for the next ".NOSHOW_BAN_PERIOD/(24*60*60)." days.\n\n"
                ."Due to the demand of sessions in our society, we highly value that members do not book if they know they can't make it. "
                ."Sessions are locked 90 minutes before they start. "
                ."If it's too late to unbook, you can also email the managers before the session starts.\n\n"
                ."To unblock your user, simply log out and back in again after the period has ended. "
                ."You will still be able to log in and see our upcoming sessions. "
                ."If you run into any issues with logging back in again, please contact the web admin at bm4117@ic.ac.uk or the committee at badminton@imperial.ac.uk";

      // return true; //for testing
      return mail($email,$subject,$body,$from);
   }

   function sendPlayerAdded($name, $email, $date, $opponent, $location){
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "ICU Badminton - Social Match Player Added";
      $body = "TBA";

      return true; //for testing
      // return mail($email,$subject,$body,$from);
   }

   function sendPlayerRemoved($name, $email, $date, $opponent){
      $from = "From: ".EMAIL_FROM_NAME." <".EMAIL_FROM_ADDR.">";
      $subject = "ICU Badminton - Social Match Player Removed";
      $body = "TBA";

      return true; //for testing
      // return mail($email,$subject,$body,$from);
   }
};

$mailer = new Mailer; // Initialise mailer object
?>
