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
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript">

        $(document).ready(function() {

            //move the image in pixel
            var move = -15;

            //zoom percentage, 1.2 =120%
            var zoom = 1.2;

            //On mouse over those thumbnail
            $('.zitem').hover(function() {

                    //Set the width and height according to the zoom percentage
                    width = $('.zitem').width() * zoom;
                    height = $('.zitem').height() * zoom;

                    //Move and zoom the image
                    $(this).find('img').stop(false,true).animate({'width':width, 'height':height, 'top':move, 'left':move}, {duration:200});

                    //Display the caption
                    $(this).find('div.caption').stop(false,true).fadeIn(200);
                },
                function() {
                    //Reset the image
                    $(this).find('img').stop(false,true).animate({'width':$('.zitem').width(), 'height':$('.zitem').height(), 'top':'0', 'left':'0'}, {duration:100});

                    //Hide the caption
                    $(this).find('div.caption').stop(false,true).fadeOut(200);
                });

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
        Welcome to your account, <?php echo $_SESSION['user_name']; ?>

        <div id="images_menu">
            <div class="zitem">
                <a href="my_images.php"><img src="css/img/home/allimages.png" border="0" alt="All images" /></a>
                <div class="caption"><a href="">My Images</a></div>
            </div>
            <div class="zitem">
                <a href="galleries.php"><img src="css/img/home/galleries.png" border="0" alt="Galleries" /></a>
                <div class="caption"><a href="">Galleries</a></div>
            </div>
            <div class="zitem">
                <a href="moderate_img.php"><img src="css/img/home/search_images.png" border="0" alt="Search Images" /></a>
                <div class="caption"><a href="">Search & Moderate</a></div>
            </div>

            <div class="zitem">
                <a href="support.php"><img src="css/img/home/tickets.png" border="0" alt="Support" /></a>
                <div class="caption"><a href="">Support</a></div>
            </div>
        </div>
    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>