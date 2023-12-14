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

if(isset($_POST['send_pm']) && isset($_POST['to']) && isset($_POST['subject']) && isset($_POST['message'])) {
    /********************* RECAPTCHA CHECK *******************************
    This code checks and validates recaptcha
     ****************************************************************/
    require_once('recaptchalib.php');

    $resp = recaptcha_check_answer ($privatekey,
        $_SERVER["REMOTE_ADDR"],
        $_POST["recaptcha_challenge_field"],
        $_POST["recaptcha_response_field"]);

    if (!$resp->is_valid) {
        die ("<h3>Image Verification failed!. Go back and try again.</h3>" .
            "(reCAPTCHA said: " . $resp->error . ")");
    }

    global $site_name;
    $subjectpm = filter($_POST['subject']);
    $messagepm = filter($_POST['message']);
    $pmto = filter($_POST['to']);
    $q = "SELECT id, user_email, user_name FROM users WHERE user_name LIKE '{$pmto}'";
    $result = mysql_query($q);
    if($result && mysql_num_rows($result) > 0) {
        $rowUsers = mysql_fetch_assoc($result);
        $qSend = "INSERT INTO private_messages (`id_from`,`id_to`,`subject`,`message`,`readed`) VALUES ('{$_SESSION['user_id']}', '{$rowUsers['id']}','$subjectpm','$messagepm','0')";
        $resultSend = mysql_query($qSend);
        if($resultSend){
            $sendSucces = true;
            //$mailmessage = "{$_SESSION['user_name']} has sent you a message on {$site_name}. Log in to view it.";
            //sendMail($rowUsers['user_email'], $rowUsers['user_name'], 'You have a new Private Message on ' . $site_name, $mailmessage);
        }
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
    <link rel="stylesheet" type="text/css" href="css/grid.css" />
    <link rel="stylesheet" type="text/css" href="css/csTransPie.css" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/csTransPie.js"></script>

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
    <div id="content" class="container_12">
    <div class="grid_12">
        <a href="pm.php?page=compose" class="button gray"><span>Compose Mesage</span></a>

        <a href="pm.php?page=inbox" class="button blue"><span>Inbox</span></a>
        <a href="pm.php?page=outbox" class="button blue"><span>Outbox</span></a>
        <a href="pm.php?page=sent" class="button blue"><span>Sent</span></a>

    </div>
        <hr>
        <div class="clear">&nbsp;</div>

        <?php
        if(isset($sendSucces)){
            echo "<div class='grid_12'><p class='success'>Message has been sent succesfuly !</p></div><div class='clear'>&nbsp;</div>";
        }
        
        
        if(isset($_GET['page']) && $_GET['page'] == 'compose') {
          ?>

            <div class="grid_12">
                <form action="" method="POST">
                    <p>To: </p>
                    <input type="text" name="to" />

                    <p>Subject: </p>
                    <input type="text" size="40" name="subject" />

                    <p>Message: </p>
                    <textarea cols="59" rows="15" name="message"></textarea>

                    <p class="jqtranformdone">
                        <?php
                        require_once('recaptchalib.php');
                        echo recaptcha_get_html($publickey);
                        ?>
                    </p>

                    <p>
                        <input type="submit" name="send_pm" value="send" />
                    </p>
                </form>
            </div>
            <div class="clear">&nbsp;</div>

            <?php
        } elseif(isset($_GET['page']) && $_GET['page'] == 'inbox') {
            $q = "SELECT users.user_name AS fromuser, private_messages.subject, private_messages.id FROM private_messages
             INNER JOIN users ON private_messages.id_from = users.id
            WHERE private_messages.id_to = '{$_SESSION['user_id']}'";
            $result = mysql_query($q);
            echo "
            <table class='style1'>
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Subject</th>
                        </tr>
                    </thead>
                    <tbody>
            ";
            while($rowPM = mysql_fetch_assoc($result)) {
                echo "
                        <tr>
                            <td>{$rowPM['fromuser']}</td>
                            <td><a href='pm.php?idpmrecived={$rowPM['id']}'>{$rowPM['subject']}</a></td>
                        </tr>
                ";
            }
            echo "</tbody>
                </table>";

        }  elseif(isset($_GET['page']) && $_GET['page'] == 'outbox' || isset($_GET['page'])  && $_GET['page'] == 'sent') {
            switch($_GET['page']){
                case 'outbox':
                    $readed = 0;
                    break;
                case 'sent':
                    $readed = 1;
                    break;
            }
            $q = "SELECT users.user_name AS touser, private_messages.subject, private_messages.id FROM private_messages
             INNER JOIN users ON private_messages.id_to = users.id
            WHERE private_messages.id_from = '{$_SESSION['user_id']}' AND private_messages.readed = $readed";
            $result = mysql_query($q);
            echo "
            <table class='style1'>
                    <thead>
                        <tr>
                            <th>To</th>
                            <th>Subject</th>
                        </tr>
                    </thead>
                    <tbody>
            ";
            while($rowPM = mysql_fetch_assoc($result)) {
                echo "
                        <tr>
                            <td>{$rowPM['touser']}</td>
                            <td><a href='pm.php?idpmsent={$rowPM['id']}'>{$rowPM['subject']}</a></td>
                        </tr>
                ";
            }
            echo "</tbody>
                </table>";
        } elseif(isset($_GET['idpmsent']) && is_numeric($_GET['idpmsent'])) {
            $q = "SELECT users.user_name AS touser, private_messages.* FROM private_messages
             INNER JOIN users ON private_messages.id_to = users.id
            WHERE private_messages.id = '{$_GET['idpmsent']}' AND private_messages.id_from = {$_SESSION['user_id']}";
            $result = mysql_query($q);
            if($result && mysql_num_rows($result) > 0) {
                $rowPM = mysql_fetch_assoc($result);
                echo "
                <table class='style1'>
            <tbody>
                <tr>
                    <td>From: </td>
                    <td>You</td>
                </tr>
                <tr>
                    <td>To:</td>
                    <td>{$rowPM['touser']}</td>
                </tr>
                <tr>
                    <td>Subject:</td>
                    <td>{$rowPM['subject']}</td>
                </tr>
                <tr>
                    <td>Message:</td>
                    <td>{$rowPM['message']}</td>
                </tr>

                </tbody>
            </table>
                ";
            }
        }

        elseif(isset($_GET['idpmrecived']) && is_numeric($_GET['idpmrecived'])) {
            $q = "SELECT users.user_name AS fromuser, private_messages.* FROM private_messages
             INNER JOIN users ON private_messages.id_from = users.id
            WHERE private_messages.id = '{$_GET['idpmrecived']}' AND private_messages.id_to = {$_SESSION['user_id']}";
            $result = mysql_query($q);
            if($result && mysql_num_rows($result) > 0) {
                $rowPM = mysql_fetch_assoc($result);
                if($rowPM['readed'] == 0){
                    mysql_query("UPDATE private_messages SET `readed` = 1 WHERE private_messages.id = '{$_GET['idpmrecived']}' AND private_messages.id_to = {$_SESSION['user_id']}");
                }

                echo "
            <table class='style1'>
            <tbody>
                <tr>
                    <td>From: </td>
                    <td>{$rowPM['fromuser']}</td>
                </tr>
                <tr>
                    <td>To:</td>
                    <td>You</td>
                </tr>
                <tr>
                    <td>Subject:</td>
                    <td>{$rowPM['subject']}</td>
                </tr>
                <tr>
                    <td>Message:</td>
                    <td>{$rowPM['message']}</td>
                </tr>

                </tbody>
            </table>
                ";
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