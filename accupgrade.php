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

$q = "SELECT * FROM users WHERE id = {$_SESSION['user_id']}";
$result = mysql_query($q);
$rowUser = mysql_fetch_assoc($result);

if(IPN_SANDBOX){
    $paypal_payment_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
} else {
    $paypal_payment_url = "https://www.paypal.com/cgi-bin/webscr";
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
    <div id="content" class="container_12">
        <?php if(IPN_ACTIVE == 1){ ?>
        
        <div class="grid_9">
            <h2>Buy or extend your premium account ! </h2>
        </div>
        <div class="grid_3">
        <a href='ipn_transactions.php' class="button gray medium mediumwidth2"><span>Transactions</span></a>
        </div>
        <hr>
        <div class="clear">&nbsp;</div>
        <div class="grid_8">
        <?php
        if($configs->account_expired($rowUser['premium'])){
            echo "
            <table class='style2'>
            <tbody>
                <tr>
                    <td>Account type: </td>
                    <td>Premium </td>
                </tr>
                <tr>
                    <td>Expire: </td>
                    <td>{$rowUser['premium']} </td>
                </tr>
            </tbody>
            </table>
            ";
        } else {
            echo "
            <table class='style2'>
            <tbody>
                <tr>
                    <td>Account type: </td>
                    <td>Regular </td>
                </tr>

            </tbody>
            </table>
            ";
        }
        ?>
            <br />

            <h2>Benefits haveing a premium account: </h2>
            <ul class='style1'>
                <li>Ads free - all your images will be clean, without any ads</li>
                <li>More galleries - you can make up to <?php echo MAX_PREMIUM_GALLERIES; ?> galleries</li>
            </ul>

        </div>
        <div class="grid_4">
<div class="boxpayment">
    <h3>One Month Package - <?php echo IPN_ONE_MONTH_PACKAGE; ?>$</h3>
    <form name="_xclick" action="<?php echo $paypal_payment_url; ?>"
          method="post">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="<?php echo PAYPAL_ACCOUNT; ?>">
        <input type="hidden" name="currency_code" value="USD">
        <input type="hidden" name="item_name" value="One month premium on <?php echo $site_name; ?>">
        <input type="hidden" name="amount" value="<?php echo IPN_ONE_MONTH_PACKAGE; ?>">
        <input type="hidden" name="custom" value="<?php echo $_SESSION['user_id']; ?>">
        <input type="hidden" name="return" value="<?php echo $site_url . "/accupgrade.php"; ?>">
        <input type="hidden" name="notify_url" value="<?php echo $site_url . "/ipn/ipn.php"; ?>">
        <input type="image" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif"
               style="border:none;" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
    </form>
</div>


            <div class="boxpayment">
                <h3>Three Months Package - <?php echo IPN_THREE_MONTHS_PACKAGE; ?>$</h3>
                <form name="_xclick" action="<?php echo $paypal_payment_url; ?>"
                      method="post">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="<?php echo PAYPAL_ACCOUNT; ?>">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="item_name" value="One month premium on <?php echo $site_name; ?>">
                    <input type="hidden" name="amount" value="<?php echo IPN_THREE_MONTHS_PACKAGE; ?>">
                    <input type="hidden" name="custom" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="return" value="<?php echo $site_url . "/accupgrade.php"; ?>">
                    <input type="hidden" name="notify_url" value="<?php echo $site_url . "/ipn/ipn.php"; ?>">
                    <input type="image" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif"
                           style="border:none;" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                </form>
            </div>

            <div class="boxpayment">
                <h3>Six months package - <?php echo IPN_SIX_MONTH_PACKAGE; ?>$</h3>
                <form name="_xclick" action="<?php echo $paypal_payment_url; ?>"
                      method="post">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="<?php echo PAYPAL_ACCOUNT; ?>">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="item_name" value="One month premium on <?php echo $site_name; ?>">
                    <input type="hidden" name="amount" value="<?php echo IPN_SIX_MONTH_PACKAGE; ?>">
                    <input type="hidden" name="custom" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="return" value="<?php echo $site_url . "/accupgrade.php"; ?>">
                    <input type="hidden" name="notify_url" value="<?php echo $site_url . "/ipn/ipn.php"; ?>">
                    <input type="image" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif"
                           style="border:none;" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                </form>
            </div>

            <div class="boxpayment">
                <h3>One year package - <?php echo IPN_ONE_YEAR_PACKAGE; ?>$</h3>
                <form name="_xclick" action="<?php echo $paypal_payment_url; ?>"
                      method="post">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="<?php echo PAYPAL_ACCOUNT; ?>">
                    <input type="hidden" name="currency_code" value="USD">
                    <input type="hidden" name="item_name" value="One month premium on <?php echo $site_name; ?>">
                    <input type="hidden" name="amount" value="<?php echo IPN_ONE_YEAR_PACKAGE; ?>">
                    <input type="hidden" name="custom" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="return" value="<?php echo $site_url . "/accupgrade.php"; ?>">
                    <input type="hidden" name="notify_url" value="<?php echo $site_url . "/ipn/ipn.php"; ?>">
                    <input type="image" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif"
                           style="border:none;" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
                </form>
            </div>
        </div>
        <div class="clear">&nbsp;</div>

<?php } else { ?>

        <div class="grid_12"><p class='info'>Sorry, account upgrades are not available for the moment.</p></div>
            <div class="clear">&nbsp;</div>
<?php }?>
    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>