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


if(isset($_POST['submit_new_gallery']) && isset($_POST['gallery_name']) && !$lockedStatus) {
    $galleryOk = 1;
    $err = array();
    $msg = array();
    if(strlen($_POST['gallery_name']) < 2 || strlen($_POST['gallery_name']) > 21)  {
        $err[] = "Your gallery name should be between 3 and 20 characters.";
        $galleryOk = 0;
    }

    if(!limitedChars($_POST['gallery_name'])){
        $err[] = "Your gallery name should contain only letters and numbers. No spaces, comas, lines, etc.";
        $galleryOk = 0;
    }

    if($galleryOk == 1){
        $q = "SELECT COUNT(id_user) FROM galleries WHERE id_user = '{$_SESSION['user_id']}'";
        $result = mysql_query($q);
        list($numRowsGallery) = mysql_fetch_row($result);
            if($configs->isPremium($_SESSION['user_id'])) {
                $max_gallery_number = MAX_PREMIUM_GALLERIES;
            } else {
                $max_gallery_number = MAX_REGULAR_GALLERIES;
            }
        if($max_gallery_number <= $numRowsGallery) {
            $err[] = "You have reached maximum number of allowed galleries. For more galleries you should buy a premium account.";
            $galleryOk = 0;
        }
    }

    if($galleryOk == 1) {
        $galleryName = filter($_POST['gallery_name']);
        $q = "INSERT INTO galleries (`id_user`, `name`) VALUES ('{$_SESSION['user_id']}','{$galleryName}')";
        $result = mysql_query($q);
        if($result){
            header('Location: galleries.php?msg=You have succesfuly added <strong> ' . $galleryName . ' </strong> gallery.');
            exit();
        } else {
            die(mysql_error());
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
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>

</head>
<body>
<?php include("inc/menu.php"); ?>

<div id="container">
    <div id="logo">
        <a href="index.php"><img border="0" alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>

    <?php include('inc/user_menu.php') ?>
    <div id="content">

        <?php if(isset($err)) { foreach($err as $value) { echo "<p class='error'>{$value}</p>"; } } ?>
        <?php if(isset($msg)) { foreach($msg as $value) { echo "<p class='success'>{$value}</p>"; } } ?>

        <?php
        if($lockedStatus){
            echo "<p class='error'>No acces - ACCOUNT LOCKED</p>";
        } else {
        ?>
            <div style="text-align:center; margin-top:50px;">
            <form action="" method="POST">
            <input class='text_add_gallery' type="text" name="gallery_name" />
            <input class='submit_add_gallery' type="submit" value="Add" name="submit_new_gallery" />
            </form>
            </div>
        <?php } ?>
    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>