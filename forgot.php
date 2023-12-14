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
require('inc/recoverClass.php');
$dbconnect = new db();
$dbconnect->connect();

banIPcheck();

$configs = new configs();
$configsarr = $configs->fetch();

$recover = new recover();

foreach($_POST as $key => $value) {
    $data[$key] = filter($value);
}

if(isset($data['usr_email'])){



    if($recover->isEmail($data['usr_email'])){
        $q = "SELECT id, user_name FROM users WHERE `user_email` LIKE '{$data['usr_email']}'";
        $result = mysql_query($q);
        if($result){
            if(mysql_num_rows($result) > 0){
                list($userIdR, $userNameR) = mysql_fetch_row($result);
                $newactivcode = rand(10000, 99999);
                $qUpdate = "UPDATE users SET `activation_code` = '{$newactivcode}' WHERE id = {$userIdR}";
                $resultUpdate = mysql_query($qUpdate);
                if($resultUpdate){
                $messageR = "<p>You have requested a password recovery for account {$userNameR}<p>
                <p>Follow this link to recover your password: {$site_url}/forgot.php?activcode={$newactivcode}&user_id={$userIdR}</p>
                <p>If you haven't requested any password recovery, ignore this email !</p>
                ";
                sendMail($data['usr_email'], $userNameR, "Password recovery", $messageR);
                    $msg = "<p class='success'>A confirmation email has been sent. Please check your email.</p>";
                } // END RESULT UPDATE
            }
        }
    }
}

if(isset($_GET['activcode']) && is_numeric($_GET['activcode']) && isset($_GET['user_id']) && is_numeric($_GET['user_id'])){
    $q = "SELECT user_email, user_name FROM users WHERE id = {$_GET['user_id']} && activation_code = {$_GET['activcode']}";
    $result = mysql_query($q);
    if($result && mysql_num_rows($result) > 0) {
        list($userEmailR, $userNameR) = mysql_fetch_row($result);
        $new_pwd = $recover->GenPwd();
        $pwd_reset = $recover->PwdHash($new_pwd);
        mysql_query("UPDATE users SET `pwd` = '{$pwd_reset}' WHERE id = {$_GET['user_id']} && activation_code = {$_GET['activcode']}");


        $messageR = "<p>You have succesfuly changed password for account:  {$userNameR}<p>
                <p>Your new password: {$new_pwd}</p>
                ";
        sendMail($userEmailR, $userNameR, "New password", $messageR);
            $msg = "<p class='success'>Your password has been succesfuly resetted. Please check your email</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $site_title; ?></title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8">
    <meta name="description" content="<?php echo $site_meta_description; ?>">
    <meta name="keywords" content="<?php echo $site_meta_keywords; ?>">
    <meta name="author" content="<?php echo $site_meta_author; ?>">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/csTransPie.css">
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.validate.js"></script>
    <script type="text/javascript" src="js/csTransPie.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $("#recoverForm").validate();

    });
</script>
</head>
<body>
<?php include("inc/menu.php"); ?>

<div id="container">
    <div id="logo">
        <a href="index.php"><img alt="logo" src="<?php echo $logo_location; ?>"></a>
    </div>
    <div id="index_upload">
        <?php
        if(!empty($err))  {
            echo "<div class=\"msg\">";
            foreach ($err as $e) {
                echo "$e <br>";
            }
            echo "</div>";
        }
        ?>
        <form action="forgot.php" class="validateForm" method="post" name="recoverForm" id="recoverForm" >
            <fieldset class="logreg">
                <legend class="logreg">Forgot password</legend>
                <?php if(isset($msg)) { echo $msg; } ?>
                <p><label for="email">Your email</label> <input name="usr_email" type="text" class="required" id="email" size="25"></p>


                <p class="submit"><input name="recover" type="submit" value="Recover"> </p>
            </fieldset>
        </form>


    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>