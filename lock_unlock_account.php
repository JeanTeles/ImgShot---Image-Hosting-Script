<?php
/**
 * Created by Zamfi
 * Image Hosting Script
 * more informations about this script on
 * http://imagehost.iuhu.org
 * Copyright of Zamfirescu Alexandru Costin - © Iuhu 2012 - All rights reserved
 * Copyright notice - Zamfi Image Hosting Script

 * This script and its content is copyright of Zamfirescu Alexandru Costin - © Iuhu 2012. All rights reserved.
 * Any redistribution or reproduction of part or all of the contents in any form is prohibited other than the following:
 * This script is for personal and comercial use only.
 * You may not, except with our express written permission, distribute or commercially exploit the content.
 * You may you transmit it or store it in any other website or other form of electronic retrieval system.
 **/


require_once('config.php');
$dbconnect = new db();
$dbconnect->connect();

$configs = new configs();
$configsarr = $configs->fetch();

$login = new login();
$login->page_protect();

$lockedStatus = $configs->isLocked($_SESSION['user_id']);

$q = "SELECT user_email, user_name, unlock_code, full_name FROM users WHERE id = {$_SESSION['user_id']}";
$result = mysql_query($q);
$rowUser = mysql_fetch_assoc($result);

if(isset($_POST['submit_unlock_code']) && ctype_alnum($_POST['submit_unlock_code']) && strlen($_POST['unlock_code']) > 3) {
    $unlock_code_entered = filter($_POST['unlock_code']);
    if($rowUser['unlock_code'] == $unlock_code_entered) {
        mysql_query("UPDATE users SET unlock_code = '', locked = '0' WHERE id = {$_SESSION['user_id']}");
        header('Location: lock_unlock_account.php');
    }
}

$unlock_code = uniqid();

if(isset($_POST['lock'])) {
    mysql_query("UPDATE users SET unlock_code = '', locked = '1' WHERE id = {$_SESSION['user_id']}");
    header('Location: lock_unlock_account.php');

} elseif(isset($_POST['unlock'])) {

    require_once('recaptchalib.php');

    $resp = recaptcha_check_answer ($privatekey,
        $_SERVER["REMOTE_ADDR"],
        $_POST["recaptcha_challenge_field"],
        $_POST["recaptcha_response_field"]);

    if (!$resp->is_valid) {
        $invalid_captcha = true;
    } else {
        $correct_captcha = true;
    }


    if(isset($correct_captcha)) {

        $q = "UPDATE users SET unlock_code = '{$unlock_code}' WHERE id = {$_SESSION['user_id']}";
        $result = mysql_query($q);
        if($result){
        $message =
            "
<html>
<head>
</head>
<body>

<p>Hello Mr/Mrs $rowUser[full_name] <br />
You have requested to unlock your account on {$site_name}
</p>

<strong>Your unlock code:</strong> $unlock_code <br />


</body>
</html>
";
        require_once('phpmail/class.phpmailer.php');
        //include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded


        $mail             = new PHPMailer();

        //$body             = file_get_contents('contents.html');
        //$body             = eregi_replace("[\]",'',$body);

        $mail->IsSMTP(); // telling the class to use SMTP
        //$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->Host       = PHPMAIL_HOST; // sets the SMTP server
        $mail->Port       = PHPMAIL_PORT;                    // set the SMTP port for the GMAIL server
        $mail->Username   = PHPMAIL_MAIL; // SMTP account username
        $mail->Password   = PHPMAIL_PASSWORD;        // SMTP account password

        $mail->SetFrom(PHPMAIL_MAIL, $site_name);
        $mail->AddReplyTo(PHPMAIL_MAIL, $site_name);
        $mail->Subject    = "Unlock account for $site_name";
        $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        //$mail->AltBody($message);
        //$mail->MsgHTML($body);
        $mail->MsgHTML($message);
        //$mail->Body = $message;

        //$usernname = $_SESSION['user_name'];
        //$address = $rowUsers['user_email'];
        $usernname = $rowUser['user_name'];
        $address = $rowUser['user_email'];
        $mail->AddAddress($address, $usernname);

        //$mail->AddAttachment("images/phpmailer.gif");      // attachment
        //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

        if(!$mail->Send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;

        } else {
            //echo "Message sent!";
            header('Location: lock_unlock_account.php');
        }
        }// if result true
    } // if isset correct captcha
} // if isset unlock


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title><?php echo $site_title; ?></title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="description" content="<?php echo $site_meta_description; ?>" />
    <meta name="keywords" content="<?php echo $site_meta_keywords; ?>" />
    <meta name="author" content="<?php echo $site_meta_author; ?>" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>

</head>
<body>
<?php include("inc/menu.php"); ?>

<div id="container">
    <?php
    include('inc/acc_lock_status.php');
    ?>

    <div id="logo">
        <a href="index.php"><img border="0" alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>


    <?php include('inc/user_menu.php') ?>
    <div id="content">
        <?php
if(isset($invalid_captcha)) {
    echo "<p class='error'>You have entered a invalid captcha ! Try again</p>";
} elseif(isset($correct_captcha)) {
    echo "<p class='success'>Check your email. You should recive your unlock code in short time.</p>";
}

if($lockedStatus) {
    echo "
    <p>Your account is currently locked. You cannot remove/change any settings from your account.</p>
    <p>To unlock it, press unlock button below and enter security code that came in your email</p>
    <p>
    <form action='' method='POST' />
    ";

    require_once('recaptchalib.php');
    echo recaptcha_get_html($publickey);

    echo "
    <input class='lock_button' type='submit' value='UNLOCK' name='unlock' />
    </form></p>";


if(strlen($rowUser['unlock_code']) > 3) {
    echo "
    <p>Enter your unlock code recived in email:</p>
    <form action='' method='POST'>
    <p><input type='text' name='unlock_code' /></p>
    <p><input type='submit' name='submit_unlock_code' /></p>";
    echo "</form>";
}


} else {
    echo "
    <p>Your account is UnLocked. You can do any modifications you like.</p>
    <p>To lock it, just press lock button below.</p>
    <form action='' method='POST' />
    <input class='lock_button' type='submit' value='LOCK' name='lock' />
    </form>
    ";
}
        ?>
    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>