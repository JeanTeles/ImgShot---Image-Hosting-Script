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

if(isset($_POST['submit_ticket']) && strlen($_POST['subject']) > 2 && strlen($_POST['message']) > 2 && is_numeric($_POST['priority'])){
    $subject = filter($_POST['subject']);
    $message = filter($_POST['message']);

    require_once('recaptchalib.php');

    $resp = recaptcha_check_answer ($privatekey,
        $_SERVER["REMOTE_ADDR"],
        $_POST["recaptcha_challenge_field"],
        $_POST["recaptcha_response_field"]);

    if (!$resp->is_valid) {
        $correct_captcha = false;
    } else {
        $correct_captcha = true;
    }

    if($correct_captcha){
    $q = "INSERT INTO tickets_opened (`name`, `priority`, `id_user`, `closed`) VALUES ('{$subject}', '{$_POST['priority']}', '{$_SESSION['user_id']}', '0')";
    $result = mysql_query($q);
    if($result) {
        $datetime = date('Y-m-d H:i:s');
        $id_ticket_inserted = mysql_insert_id();
        $q2 = "INSERT INTO tickets_reply (`id_ticket`, `message`, `id_user_reply`, `date`) VALUES ('{$id_ticket_inserted}', '{$message}', '{$_SESSION['user_id']}', '{$datetime}')";
        $result2 = mysql_query($q2);
        if($result2){
            $ticket_submit_ok = true;
        }
    } else {
        die(mysql_error());
    }
    } //correct captcha

}

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
    <link rel="stylesheet" type="text/css" href="css/csTransPie.css" />
    <link rel="stylesheet" type="text/css" href="css/smoothness/jquery-ui-1.8.18.custom.css" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/csTransPie.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript">
        $(function() {
            $( ".view_tickets" ).button();
        });
    </script>

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
        if(isset($id_ticket_inserted)) {
            echo "<p class='success'>Your ticket has been submited succesfuly ! Click here to view your tickets</p>";
        }
        if(isset($correct_captcha) && !$correct_captcha){
            echo "<p class='error'>Invalid captcha code</p>";
        }
?>

            <a style="float:right;" class="view_tickets" href="view_tickets.php">View all tickets</a>



        <p class="big_head">Submit a ticket:</p>
        
        <div class="supportform">
        <form action="" method="POST">
            <p>Priority: </p>
            <select name="priority">
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
            </select>

            <p>Subject: </p>
            <input type="text" name="subject" />

            <p>Message: </p>
            <textarea cols="59" rows="15" name="message"></textarea>
            
            <p class="jqtranformdone">
                <?php
                require_once('recaptchalib.php');
                echo recaptcha_get_html($publickey);
                ?>
            </p>
            
            <p>
                <input type="submit" name="submit_ticket" />
            </p>
        </form>
        </div>


    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>