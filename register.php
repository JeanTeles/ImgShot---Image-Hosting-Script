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

if(isset($_GET['ref']) && is_numeric($_GET['ref']) && !isset($_COOKIE['ref'])) {
    $resultRef = mysql_query("SELECT id FROM users WHERE id = {$_GET['ref']}");

    if($resultRef && mysql_num_rows($resultRef) > 0) {
        setcookie("ref", $_GET['ref'], time()+2592000);
    }
}

banIPcheck();

$configs = new configs();
$configsarr = $configs->fetch();

$login = new login();

$err = array();

if(isset($_POST['doRegister']) && $_POST['doRegister'] == 'Register')
{
    /******************* Filtering/Sanitizing Input *****************************
    This code filters harmful script code and escapes data of all POST data
    from the user submitted form.
     *****************************************************************/
    foreach($_POST as $key => $value) {
        $data[$key] = filter($value);
    }

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
    /************************ SERVER SIDE VALIDATION **************************************/
    /********** This validation is useful if javascript is disabled in the browswer ***/


    // Validate User Name
    if (!$login->isUserID($data['user_name'])) {
        $err[] = "ERROR - Invalid user name. It can contain alphabet, number and underscore.";
        //header("Location: register.php?msg=$err");
        //exit();
    }

    // Validate Email
    if(!$login->isEmail($data['usr_email'])) {
        $err[] = "ERROR - Invalid email address.";
        //header("Location: register.php?msg=$err");
        //exit();
    }
    // Check User Passwords
    if (!$login->checkPwd($data['pwd'],$data['pwd2'])) {
        $err[] = "ERROR - Invalid Password or mismatch. Enter 5 chars or more";
        //header("Location: register.php?msg=$err");
        //exit();
    }

    $user_ip = $_SERVER['REMOTE_ADDR'];

    // stores sha1 of password
    $sha1pass = $login->PwdHash($data['pwd']);

    // Automatically collects the hostname or domain  like example.com)
    $host  = $_SERVER['HTTP_HOST'];
    $host_upper = strtoupper($host);
    $path   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

    // Generates activation code simple 4 digit number
    $activ_code = rand(1000,9999);

    $usr_email = $data['usr_email'];
    $user_name = $data['user_name'];

    /************ USER EMAIL CHECK ************************************
    This code does a second check on the server side if the email already exists. It
    queries the database and if it has any existing email it throws user email already exists
     *******************************************************************/

    $rs_duplicate = mysql_query("select count(*) as total from users where user_email='$usr_email' OR user_name='$user_name'") or die(mysql_error());
    list($total) = mysql_fetch_row($rs_duplicate);

    if ($total > 0)
    {
        $err[] = "ERROR - The username/email already exists. Please try again with different username and email.";
        //header("Location: register.php?msg=$err");
        //exit();
    }

    //COOKIE FOR REFERRAL
    $refid = 0;
    if(isset($_COOKIE['ref']) && is_numeric($_COOKIE['ref'])){
        $resultRef = mysql_query("SELECT id FROM users WHERE id = {$_COOKIE['ref']}");
        if($resultRef && mysql_num_rows($resultRef) > 0) {
            $refid = $_COOKIE['ref'];
        }
    }

    /***************************************************************************/

    if(empty($err)) {

        $sql_insert = "INSERT into `users`
  			(`full_name`,`user_email`,`pwd`,`date`,`users_ip`,`activation_code`,`user_name`,`ref`)
		    VALUES
		    ('$data[full_name]','$usr_email','$sha1pass',now(),'$user_ip','$activ_code','$user_name','$refid')
			";

        mysql_query($sql_insert) or die("Insertion Failed:" . mysql_error());
        $user_id = mysql_insert_id();
        $md5_id = md5($user_id);
        mysql_query("update users set md5_id='$md5_id' where id='$user_id'");
        //	echo "<h3>Thank You</h3> We received your submission.";



        $message =
            "
<html>
<head>
</head>
<body>

<p>Hello Mr/Mrs $data[full_name] <br />
Thank you for your registration on {$site_name}.
</p><br />

<p>To activate your account, <a href=\"http://$host$path/activate.php?user=$md5_id&activ_code=$activ_code\">click here</a></p> <br />

<p>Bellow are your login details.</p>


<strong>User ID:</strong> $user_name <br />
<strong>Email:</strong> $usr_email <br />
<strong>Passwd:</strong> $data[pwd] <br />

</body>
</html>
";
        require_once('phpmail/class.phpmailer.php');
        //include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded


        $mail             = new PHPMailer();

        //$body             = file_get_contents('contents.html');
        //$body             = eregi_replace("[\]",'',$body);

        $mail->IsSMTP(); // telling the class to use SMTP
        //$mail->Host       = PHPMAIL_HOST; // sets the SMTP server
        $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->Host       = PHPMAIL_HOST; // sets the SMTP server
        $mail->Port       = PHPMAIL_PORT;                    // set the SMTP port for the GMAIL server
        $mail->Username   = PHPMAIL_MAIL; // SMTP account username
        $mail->Password   = PHPMAIL_PASSWORD;        // SMTP account password

        $mail->SetFrom(PHPMAIL_MAIL, $site_name);
        $mail->AddReplyTo(PHPMAIL_MAIL, $site_name);
        $mail->Subject    = "Login Details for $site_name";
        $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        //$mail->AltBody($message);
        //$mail->MsgHTML($body);
        $mail->MsgHTML($message);
        //$mail->Body = $message;

        //$usernname = $_SESSION['user_name'];
        //$address = $rowUsers['user_email'];
        $usernname = $user_name;
        $address = $usr_email;
        $mail->AddAddress($address, $usernname);

        //$mail->AddAttachment("images/phpmailer.gif");      // attachment
        //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

        if(!$mail->Send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;

        } else {
            //echo "Message sent!";
            header("Location: thankyou.php");
            exit();
        }


    }
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
    <script type="text/javascript">
        $(document).ready(function(){
            $("#regForm").validate({});

        });
    </script>

</head>
<body>
<?php include("inc/menu.php"); ?>

<div id="container">
    <div id="logo">
        <a href="index.php"><img alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>
    <div id="index_upload">
        <?php
        if(!empty($err))  {
            echo "<div class=\"msg\">";
            foreach ($err as $e) {
                echo "$e <br />";
            }
            echo "</div>";
        }
        ?>

        <form action="register.php" method="post" name="logForm" id="regForm" >
            <fieldset class="logreg">
                <legend class="logreg">Register</legend>
                <input name="btnAvailable" type="button" id="btnAvailable"
                       style="position:absolute; margin-left:155px;"
                       onclick='$("#checkid").html("Please wait..."); $.get("checkuser.php",{ cmd: "check", user: $("#user_name").val() } ,function(data){  $("#checkid").html(data); });'
                       value="Check Username Availability">
                <span style="color:red; font-size: 12px; font-weight:bold; position:absolute;" id="checkid" ></span>
                <br />
                <p><label for="full_name">Full Name</label> <input name="full_name" type="text" id="full_name" class="required"></p>
                <p><label for="user_name">Username</label> <input name="user_name" type="text" id="user_name" class="required username" minlength="5"></p>
                <p><label for="usr_email3">E-mail: </label> <input name="usr_email" type="text" id="usr_email3" class="required email"></p>
                <p><label for="pwd">Password</label> <input name="pwd" type="password" class="required password" id="pwd" minlength="5"><br /></p>
                <p><label for="pwd2">Retype Password</label> <input name="pwd2"  id="pwd2" class="required password" type="password" equalto="#pwd" minlength="5"><br /></p>
                <p><label>Image Verification:</label></p>
                    <?php
                    require_once('recaptchalib.php');
                    echo recaptcha_get_html($publickey);
                    ?>
                
                
                <p class="submit"><input class="button white medium" name="doRegister" type="submit" id="doRegister" value="Register"></p>
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