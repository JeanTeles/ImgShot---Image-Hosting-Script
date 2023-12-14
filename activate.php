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


foreach($_GET as $key => $value) {
    $get[$key] = filter($value);
}

/******** EMAIL ACTIVATION LINK**********************/
if(isset($get['user']) && !empty($get['activ_code']) && !empty($get['user']) && is_numeric($get['activ_code']) ) {

    $err = array();
    $msg = array();

    $user = mysql_real_escape_string($get['user']);
    $activ = mysql_real_escape_string($get['activ_code']);

    //check if activ code and user is valid
    $rs_check = mysql_query("select id from users where md5_id='$user' and activation_code='$activ'") or die (mysql_error());
    $num = mysql_num_rows($rs_check);
    // Match row found with more than 1 results  - the user is authenticated.
    if ( $num <= 0 ) {
        $err[] = "Sorry no such account exists or activation code invalid.";
        //header("Location: activate.php?msg=$msg");
        //exit();
    }

    if(empty($err)) {
        // set the approved field to 1 to activate the account
        $rs_activ = mysql_query("update users set approved='1' WHERE
						 md5_id='$user' AND activation_code = '$activ' ") or die(mysql_error());
        $msg[] = "Thank you. Your account has been activated.<br /> <br />
        * <a href='login.php'>click here</a> to login with your username and password *";
        //header("Location: activate.php?done=1&msg=$msg");
        //exit();
    }
}

/******************* ACTIVATION BY FORM**************************/
if (isset($_POST['doActivate']) && $_POST['doActivate']=='Activate')
{
    $err = array();
    $msg = array();

    $user_email = mysql_real_escape_string($_POST['user_email']);
    $activ = mysql_real_escape_string($_POST['activ_code']);
    //check if activ code and user is valid as precaution
    $rs_check = mysql_query("select id from users where user_email='$user_email' and activation_code='$activ'") or die (mysql_error());
    $num = mysql_num_rows($rs_check);
    // Match row found with more than 1 results  - the user is authenticated.
    if ( $num <= 0 ) {
        $err[] = "Sorry no such account exists or activation code invalid.";
        //header("Location: activate.php?msg=$msg");
        //exit();
    }
    //set approved field to 1 to activate the user
    if(empty($err)) {
        $rs_activ = mysql_query("update users set approved='1' WHERE
						 user_email='$user_email' AND activation_code = '$activ' ") or die(mysql_error());
        $msg[] = "Thank you. Your account has been activated.<br /> <br />
        <a href='login.php'>Click here</a> to login with your username and password";
    }
    //header("Location: activate.php?msg=$msg");
    //exit();
}


?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $site_title; ?></title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="description" content="<?php echo $site_meta_description; ?>" />
    <meta name="keywords" content="<?php echo $site_meta_keywords; ?>" />
    <meta name="author" content="<?php echo $site_meta_author; ?>" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.validate.js"></script>

</head>
<body>
<?php include("inc/menu.php"); ?>

<div id="container">
    <div id="logo">
        <a href="index.php"><img alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>
    <div id="index_upload">
        <?php
        /******************** ERROR MESSAGES*************************************************
        This code is to show error messages
         **************************************************************************/
        if(!empty($err))  {
            echo "<div class=\"msg\">";
            foreach ($err as $e) {
                echo "* $e <br>";
            }
            echo "</div>";
        }
        if(!empty($msg))  {
            echo "<div class=\"msg\">" . $msg[0] . "</div>";

        }
        /******************************* END ********************************/
        ?>

    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>