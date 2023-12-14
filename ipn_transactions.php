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
        <div class="grid_9"><h2>Your current transactions: </h2></div>
        <div class="grid_3">
            <a href='accupgrade.php' class="button gray small mediumwidth2"><span>Upgrade Acc</span></a>
        </div>
        <div class="clear"></div>

        <div class="grid_12">
            <?php
            $q = "SELECT * FROM ipn_transactions WHERE id_user = {$_SESSION['user_id']} ORDER BY id DESC";
            $result = mysql_query($q);
            if($result && mysql_num_rows($result)> 0) {
                echo "
                <table class='style2_smaller'>
                <thead>
                    <tr>
                        <td>Payer email</td>
                        <td>Transaction ID</td>
                        <td>Amount</td>
                        <td>Date</td>
                        <td>Premium extend from</td>
                        <td>Premium extend to</td>
                    </tr>
                </thead>
                <tbody>

                ";
                while($rowTrasactions = mysql_fetch_assoc($result)){
                    echo "
                    <tr>
                        <td>{$rowTrasactions['payer_email']}</td>
                        <td>{$rowTrasactions['txn_id']}</td>
                        <td>{$rowTrasactions['amount']} $</td>
                        <td>{$rowTrasactions['date']}</td>
                        <td>{$rowTrasactions['updFromDate']}</td>
                        <td>{$rowTrasactions['updToDate']}</td>
                    </tr>
                    ";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No transactions at this moment</p>";
            }

            ?>

        </div>
        <div class="clear"></div>


    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>