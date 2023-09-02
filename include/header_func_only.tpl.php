<?php   // tracking code. must escape - can easily be forged
  // $database->addPageLoad($session->username,
  // 			mysql_real_escape_string($_SERVER['HTTP_REFERER']),
  // 			mysql_real_escape_string(substr($_SERVER['PHP_SELF'],14)),
  // 			mysql_real_escape_string(getenv(QUERY_STRING)),
  // 			mysql_real_escape_string($_SERVER['REMOTE_ADDR'])
  // 			);

  // check fake is_admin cookie (see session.php)
  if( isset($_COOKIE['is_admin']) && $_COOKIE['is_admin']!=0){ header("Location: " . SITE_URL); }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ICU Badminton<?php   if (isset($pagetitle)) { echo " | " . $pagetitle; } ?></title>
        <link rel="stylesheet" href="<?php   echo SITE_URL; ?>/css/main.css">
        <link rel="stylesheet" href="<?php   echo SITE_URL; ?>/css/custom.css">
    </head>

<?php   if (isset($banner)): ?>
    <body class="landing">
<?php   else: ?>
    <body>
<?php   endif ?>
