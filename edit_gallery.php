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

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header('Location: galleries.php');
}

require_once('config.php');
$dbconnect = new db();
$dbconnect->connect();

$configs = new configs();
$configsarr = $configs->fetch();

$login = new login();
$login->page_protect();

$lockedStatus = $configs->isLocked($_SESSION['user_id']);


if(isset($_POST['submit_new_gallery']) && isset($_POST['gallery_name']) && isset($id) && !$lockedStatus) {
    $galleryOk = 1;
    $err = array();
    $msg = array();
    if(strlen($_POST['gallery_name']) < 2) {
        $err[] = "Your gallery name should be at least 3 caracters long.";
        $galleryOk = 0;
    }

    if(!limitedChars($_POST['gallery_name'])){
        $err[] = "Your gallery name should contain only letters and numbers. No spaces, comas, lines, etc.";
        $galleryOk = 0;
    }

    if($galleryOk == 1) {
        $galleryName = filter($_POST['gallery_name']);
        if(is_numeric($_POST['public_gallery'])) {
            $public_gallery_sql = $_POST['public_gallery'];
        } else {
            $public_gallery_sql = 0;
        }
        $q = "UPDATE galleries SET galleries.name = '{$galleryName}', galleries.public = '{$public_gallery_sql}' WHERE id={$id} AND id_user = {$_SESSION['user_id']}";
        $result = mysql_query($q);
        if($result){
            $msg[] = "You have succesfuly renamed gallery to <strong>{$galleryName}</strong>.";
        } else {
            die(mysql_error());
        }
    }
}

if(isset($_POST['delete_gallery']) && isset($_POST['delete_confirm'])){
    $q = "DELETE FROM galleries WHERE id = {$id} AND id_user = {$_SESSION['user_id']}";
    $result = mysql_query($q);
    if($result){
        mysql_query("UPDATE images SET gallery = 0 WHERE gallery = {$id} AND id_user = {$_SESSION['user_id']}");
        //DELETE SUCCESS
    }
}

$q = "SELECT galleries.name, galleries.public FROM galleries WHERE id_user = {$_SESSION['user_id']} AND id = {$id}";
$result = mysql_query($q);
if($result){
    if(mysql_num_rows($result) > 0){
    $rowGalleries = mysql_fetch_assoc($result);
    } else {
        header('Location: galleries.php');
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
    <link rel="stylesheet" type="text/css" href="css/csTransPie.css" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/csTransPie.js"></script>

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
            <input class='text_add_gallery' type="text" value="<?php echo $rowGalleries['name']; ?>" name="gallery_name" />
            <input class='submit_add_gallery' type="submit" value="Edit" name="submit_new_gallery" /> <br />
                <input type="radio" <?php if($rowGalleries['public'] == 1) {  echo "checked = \"checked\""; } ?> name="public_gallery" value="1" /> Public Gallery
                <input type="radio" <?php if($rowGalleries['public'] == 0) {  echo "checked = \"checked\""; } ?> name="public_gallery" value="0" /> Private Gallery
            </form>

            <form style='margin-top:50px;' action="" method="POST">
                <input type="checkbox" name="delete_confirm" /> I confirm that i want to delete this gallery <br />
                <input type="submit" name="delete_gallery" value="Delete Gallery" />
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