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
if(isset($_GET['id'])){
    if(is_numeric($_GET['id'])) {
    $id = $_GET['id'];
        if(isset($_POST['submit_reply'])){

            $replyMsg = filter($_POST['reply']);
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
            
            
            if(isset($correct_captcha)){
            $datetime = date('Y-m-d H:i:s');
            $q = "INSERT INTO tickets_reply (`id_ticket`, `id_user_reply`, `is_admin`, `message`, `date`) VALUES ('{$id}', '{$_SESSION['user_id']}', '0', '{$replyMsg}', '{$datetime}')";
            mysql_query($q);

                $q = "UPDATE tickets_opened SET closed = 0 WHERE id = '{$id}'";
                mysql_query($q);
            }
        }

    } else {
        header('Location: view_tickets.php');
        exit();
    }
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
            if(isset($invalid_captcha)){
                echo "<p class='error'>Invalid captcha entered</p>";
            }
            if(isset($id)){
                $q = "SELECT * FROM tickets_opened WHERE tickets_opened.id_user = {$_SESSION['user_id']} AND tickets_opened.id = {$id}";
                $result = mysql_query($q);
                $rowTikOpen = mysql_fetch_assoc($result);
                echo "
                <p>Ticket #{$rowTikOpen['id']}</p>
                <p>Subject: {$rowTikOpen['name']}</p>
                ";

                $q = "SELECT tickets_reply.message, tickets_reply.is_admin, tickets_reply.date, users.full_name FROM tickets_reply
                INNER JOIN users ON tickets_reply.id_user_reply = users.id
                WHERE tickets_reply.id_ticket = {$id}
                ORDER BY tickets_reply.id ASC";
                $result = mysql_query($q);
                if($result) {
                    while($rowTikReply = mysql_fetch_assoc($result)){
                        if($rowTikReply['is_admin'] == 1){
                            $admin_sign = "<img src='css/img/star_full.png' />";
                        } else {
                            $admin_sign = "";
                        }
                        echo "<div class='ticket_reply'><div class='user'>$admin_sign {$rowTikReply['full_name']} <br /><span class='date'> {$rowTikReply['date']} </span> </div><div class='message'>{$rowTikReply['message']}</div></div>";
                    }

                    echo "
                    <form action='' method='POST'>
                    <p>Reply message:</p>
                    <p><textarea cols='79' name='reply'></textarea></p>
                    ";

                            require_once('recaptchalib.php');
                            echo recaptcha_get_html($publickey);
                        echo "

                    <input type='submit' value='Reply' name='submit_reply' />
                    </form>
                    ";


                } else {
                    echo mysql_error();
                }
            }

            if(!isset($_GET['id'])) {
                    $q = "SELECT * FROM tickets_opened WHERE id_user = {$_SESSION['user_id']} AND closed = 0";
                $result = mysql_query($q);
                if($result){
                    if(mysql_num_rows($result) > 0) {
                        echo "<p class='big_head'>Open Tickets</p>";
                    }
                    while($rowTikOpen = mysql_fetch_assoc($result)) {
                        echo "<a class='ticket_opened' href='view_tickets.php?id=".$rowTikOpen['id']."'>Ticket #".$rowTikOpen['id']." - ".$rowTikOpen['name']." <span class='opened_ticket'>Opened</span> </a>";
                    }
                } else {
                    echo mysql_error();
                }




                $q = "SELECT * FROM tickets_opened WHERE id_user = {$_SESSION['user_id']} AND closed = 1 ORDER BY id DESC LIMIT 10";
                $result = mysql_query($q);
                if($result){
                    if(mysql_num_rows($result) > 0) {
                        echo "<p class='big_head'>Latest 10 Closed Tickets</p>";
                    }
                    while($rowTikOpen = mysql_fetch_assoc($result)) {
                        echo "<a class='ticket_opened' href='view_tickets.php?id=".$rowTikOpen['id']."'>Ticket #".$rowTikOpen['id']." - ".$rowTikOpen['name']. " <span class='closed_ticket'>Closed</span> </a> ";
                    }
                } else {
                    echo mysql_error();
                }
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