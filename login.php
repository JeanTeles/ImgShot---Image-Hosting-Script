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

banIPcheck();

$configs = new configs();
$configsarr = $configs->fetch();

$login = new login();

$err = array();
$badpass_date = date('Y-m-d');

foreach($_GET as $key => $value) {
    $get[$key] = filter($value); //get variables are filtered.
}

if (isset($_POST['doLogin']) && $_POST['doLogin']=='Login')
{

    foreach($_POST as $key => $value) {
        $data[$key] = filter($value); // post variables are filtered
    }


    $user_email = $data['usr_email'];
    $pass = $data['pwd'];


    if (strpos($user_email,'@') === false) {
        $user_cond = "user_name='$user_email'";
    } else {
        $user_cond = "user_email='$user_email'";

    }



    $result = mysql_query("SELECT `id`,`pwd`,`full_name`,`approved`,`user_level` FROM users WHERE
           $user_cond") or die (mysql_error());
    $num = mysql_num_rows($result);

    // Match row found with more than 1 results  - the user is authenticated.
    if ( $num > 0 ) {

        list($id,$pwd,$full_name,$approved,$user_level) = mysql_fetch_row($result);

        $qBannedCheck = "SELECT reason FROM banned_users WHERE id_user = $id";
        $resultqBannedCheck = mysql_query($qBannedCheck);
        if($resultqBannedCheck && mysql_num_rows($resultqBannedCheck) > 0) {
            $rowBannedCheck = mysql_fetch_assoc($resultqBannedCheck);
            $err[] = "Your account is banned. Reason: " . $rowBannedCheck['reason'];
        }

        if(!$approved) {
            //$msg = urlencode("Account not activated. Please check your email for activation code");
            $err[] = "Account not activated. Please check your email for activation code";

            //header("Location: login.php?msg=$msg");
            //exit();
        }

        // GET CAPTCHA IF PASSWORD ENETERED BAD TO MANY TIMES
        $ip = $_SERVER['REMOTE_ADDR'];

        $qBadPass = "SELECT badpass.user, badpass.times FROM badpass WHERE ip = '{$ip}' AND date = '{$badpass_date}'";
        $resultBadPass = mysql_query($qBadPass);
        $numRowsBadpass = mysql_num_rows($resultBadPass);
        if($numRowsBadpass > 0){
            $rowBadPass = mysql_fetch_assoc($resultBadPass);
            if($rowBadPass['times'] >= 5){
                require_once('recaptchalib.php');

                $resp = recaptcha_check_answer ($privatekey,
                    $_SERVER["REMOTE_ADDR"],
                    $_POST["recaptcha_challenge_field"],
                    $_POST["recaptcha_response_field"]);

                if (!$resp->is_valid) {
                    die ("<h3>Image Verification failed!. Go back and try again.</h3>" .
                        "(reCAPTCHA said: " . $resp->error . ")");
                }

            }
        }

        // GET CAPTCHA IF PASSWORD ENETERED BAD TO MANY TIMES

        //check against salt
        if ($pwd === $login->PwdHash($pass,substr($pwd,0,9))) {
            if(empty($err)){

                // this sets session and logs user in
                session_start();
                session_regenerate_id (true); //prevent against session fixation attacks.

                // this sets variables in the session
                $_SESSION['user_id']= $id;
                $_SESSION['user_name'] = $full_name;
                $_SESSION['user_level'] = $user_level;
                $_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);

                //update the timestamp and key for cookie
                $stamp = time();
                $ckey = $login->GenKey();
                mysql_query("update users set `ctime`='$stamp', `ckey` = '$ckey' where id='$id'") or die(mysql_error());

                //set a cookie

                if(isset($_POST['remember'])){
                    setcookie("user_id", $_SESSION['user_id'], time()+60*60*24*COOKIE_TIME_OUT, "/");
                    setcookie("user_key", sha1($ckey), time()+60*60*24*COOKIE_TIME_OUT, "/");
                    setcookie("user_name",$_SESSION['user_name'], time()+60*60*24*COOKIE_TIME_OUT, "/");
                }
                header("Location: myaccount.php");
            }
        }
        else
        {
            //$msg = urlencode("Invalid Login. Please try again with correct user email and password. ");

            if($numRowsBadpass > 0){
                $rowBadPass['times']++;
                $newUsers = $data['usr_email'] . ", " . $rowBadPass['user'];
                mysql_query("UPDATE badpass SET times = '{$rowBadPass['times']}', user='{$newUsers}' WHERE ip = '{$ip}'");
            } else {
                mysql_query("INSERT INTO badpass (`ip`,`times`,`user`,`date`) VALUES ('{$ip}','1','{$data['usr_email']}','{$badpass_date}')");
            }


            $err[] = "Invalid Login. Please try again with correct user email and password.";
            //header("Location: login.php?msg=$msg");
        }
    } else {
        $err[] = "Error - Invalid login. No such user exists";
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
        $("#logForm").validate();

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
        <form action="login.php" class="validateForm" method="post" name="logForm" id="logForm" >
            <fieldset class="logreg">
                <legend class="logreg">Login Users</legend>
                <p><label for="username">Username</label> <input name="usr_email" type="text" class="required" id="username" size="25"></p>
                <p><label for="password">Password</label> <input name="pwd" type="password" class="required password" id="password" size="25"><br> </p>
                <?php
                $q = "SELECT times FROM badpass WHERE ip = '{$_SERVER['REMOTE_ADDR']}' AND date = '{$badpass_date}'";
                $result = mysql_query($q);
                if($result && mysql_num_rows($result) > 0) {
                    $rowtimeslogin = mysql_fetch_assoc($result);
                    if($rowtimeslogin['times'] >= 5){
                        require_once('recaptchalib.php');
                        echo recaptcha_get_html($publickey);
                    }
                }
                ?>
                <p><input name="remember" type="checkbox" id="remember" value="1"> Remember me <br></p>
                <p class="submit"><input name="doLogin" type="submit" id="doLogin3" value="Login"> <a href="forgot.php"><span>Forgot password ?</span></a> </p>
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