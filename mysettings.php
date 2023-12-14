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

$err = array();
$msg = array();

if(isset($_POST['doUpdatePass']) && $_POST['doUpdatePass'] == 'Update Password' && $lockedStatus == 0)
{
    $rs_pwd = mysql_query("select pwd from users where id='$_SESSION[user_id]'");
    list($old) = mysql_fetch_row($rs_pwd);
    $old_salt = substr($old,0,9);

    //check for old password in md5 format
    if($old === $login->PwdHash($_POST['pwd_old'],$old_salt))
    {
        $newsha1 = $login->PwdHash($_POST['pwd_new']);
        mysql_query("update users set pwd='$newsha1' where id='$_SESSION[user_id]'");
        $msg[] = "Your new password is updated";
        //header("Location: mysettings.php?msg=Your new password is updated");
    } else
    {
        $err[] = "Your old password is invalid";
        //header("Location: mysettings.php?msg=Your old password is invalid");
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
    <script type="text/javascript" src="js/jquery.validate.js"></script>
    <script type="text/javascript" src="js/csTransPie.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#pform").validate({});

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
    <div id="content" class='container_12'>
        <div class="grid_12">
        <h2>Welcome to your account, <?php echo $_SESSION['user_name'] . "</h2>"; if($lockedStatus == 1){echo " <span style='color:red'>(Account Locked)</span>";} ?>
            <hr>
        </div>
        <div class="clear">&nbsp;</div>


        <?php if(isset($msg)) { foreach($msg as $msgValue) {echo "<div class='grid_12'><p class='success'>$msgValue</p></div><div class='clear'>&nbsp;</div>"; } } ?>
        <?php if(isset($err)) { foreach($err as $errValue) {echo "<div class='grid_12'<p class='error'>$errValue</p></div><div class='clear'>&nbsp;</div>"; } } ?>

        


        <div class='grid_4'>
        <form name="pform" id="pform" method="post" action="">

            <h3>Change Password:</h3>

        <p>
        Old Password<br />
        <input name="pwd_old" type="password" class="small required password"  id="pwd_old" />
        </p>
        <p>
        New Password<br />
        <input name="pwd_new" type="password" id="pwd_new" class="small required password" />
         </p>
            <br />
         <p>
                <input name="doUpdatePass" type="submit" id="doUpdatePass" class="small" value="Update Password" />
         </p>

        </form>
        </div>
        <div class='grid_4'>
            <h3>Your referral link: </h3>
            <p><?php echo $site_url . "/register.php?ref=" . $_SESSION['user_id']; ?></p>
        </div>
        <div class='grid_4'>
            <h3>Your referrals: </h3>
            <?php
            $q = "SELECT user_name, date FROM users WHERE ref = {$_SESSION['user_id']}";
            $result = mysql_query($q);
            if($result && mysql_num_rows($result) > 0) {
                echo "
                <table class='style2'>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date Joined</th>
                    </tr>
                </thead>
                <tbody>";
                while($rowRef = mysql_fetch_assoc($result)) {
                    echo "
                    <tr>
                        <td>{$rowRef['user_name']}</td>
                        <td>{$rowRef['date']}</td>
                    </tr>";
                }
                echo "</tbody></table>";
            }
            ?>
        </div>
        <div class='clear'>&nbsp;</div>




    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>