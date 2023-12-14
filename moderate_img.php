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
    <link rel="stylesheet" type="text/css" href="css/csTransPie.css" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/csTransPie.js"></script>

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
        <?php
        if($lockedStatus){
            echo "<p class='error'>No acces - ACCOUNT LOCKED</p>";
        } else {
?>
        <?php if(isset($_GET['msg'])) { echo "<p class='success'>" . filter($_GET['msg']) . "</p>"; } ?>
        <div class="all_images">
            <?php
        //print_r($_POST);
            if(isset($_POST['imagesidarr'])){
                 $_POST['imagesid'] = implode(",", $_POST['imagesidarr']);
            }



            if(isset($_POST['imagesid'])) {
                $imagesid = preg_replace('/ /', '', $_POST['imagesid']);
                $imagesid = explode(",",$imagesid);
                foreach($imagesid as $value) {
                    if(ctype_alnum($value)) {
                    $imagesid2[] = "'" .$value ."'";
                    }
                }

                if(isset($imagesid2)) {
                $imagesid2 = implode(",", $imagesid2);
                //echo $imagesid2;


                $q = "SELECT images.view_id, images.name, images.date_added, images.ftp, sources.thumb2, sources.img, sources.thumb, sources.img2, ftp_logins.url, ftp_logins.host, ftp_logins.user, ftp_logins.pass FROM images
                     INNER JOIN sources ON images.source=sources.id
                     LEFT JOIN ftp_logins ON images.ftp = ftp_logins.id
                     WHERE images.view_id IN (".$imagesid2.") AND id_user='{$_SESSION['user_id']}'
                     ORDER BY images.id DESC";

                $result = mysql_query($q);
                $rowsNum = mysql_num_rows($result);
                } else {
                    $rowsNum = 0;
                }

                echo "<p>Found: " . $rowsNum . " images </p>";


                if($rowsNum > 0) {
                while($rowImages = mysql_fetch_assoc($result)) {
                    if($rowImages['ftp'] > 0) {
                        $real_site_url = $rowImages['url'];
                    } else {
                        $real_site_url = $site_url;
                    }

                    $dirDate = preg_replace('/-/', '/', $rowImages['date_added']);
                    $dirThumb = $real_site_url . "/" . $rowImages['thumb2'] . "/" . $dirDate . "/" . $rowImages['name'];
                    echo "<img src='{$dirThumb}' />";

                    $BBCode_global[] = "[URL={$site_url}/img-{$rowImages['view_id']}.html][IMG]{$dirThumb}[/IMG][/URL]";
                    $HTMLCode_global[] = "<a href='{$site_url}/img-{$rowImages['view_id']}.html'><img src='{$dirThumb}' alt='image' /></a>";
                    $DirectLink_global[] = "{$site_url}/img-{$rowImages['view_id']}.html";

                    // DELETE A IMAGE
                    if(isset($_POST['delete']) && $rowImages['ftp'] == 0){
                        mysql_query("DELETE FROM images WHERE view_id ='".$rowImages['view_id']."' AND id_user='{$_SESSION['user_id']}'");
                        $imgUnlink = "" . $rowImages['img2'] . "/" . $dirDate . "/" . $rowImages['name'];
                        $thumbUnlink = "" . $rowImages['thumb2'] . "/" . $dirDate . "/" . $rowImages['name'];
                        while(is_file($imgUnlink) == TRUE) {
                            chmod($imgUnlink, 0666);
                            unlink($imgUnlink);
                        }
                        while(is_file($thumbUnlink) == TRUE) {
                            chmod($thumbUnlink, 0666);
                            unlink($thumbUnlink);
                        }
                    }  elseif(isset($_POST['delete']) && $rowImages['ftp'] > 0) {
                        $imgUnlink = $rowImages['img'] . "/" . $dirDate . "/" . $rowImages['name'];
                        $thumbUnlink =  $rowImages['thumb'] . "/" . $dirDate . "/" . $rowImages['name'];
                        mysql_query("DELETE FROM images WHERE view_id ='".$rowImages['view_id']."'");

                        $FTP = new FTP();
                        global $ftp_conn_id;
                        $FTP->connect($rowImages['host'], $rowImages['user'], $rowImages['pass']);
                        ftp_delete($ftp_conn_id, $imgUnlink);
                        ftp_delete($ftp_conn_id, $thumbUnlink);
                        $FTP->disconnect($ftp_conn_id);
                    } // if isset delete

                } // while fetch assoc

                    if(isset($_POST['delete'])){
                        ?>
                        <script language="javascript" type="text/javascript">
                            <!--
                            window.setTimeout('window.location="moderate_img.php?msg=You have succesfuly removed selected images"; ',0);
                            // -->
                        </script>
                        <?php
                        exit();
                    }



                    // MOVE TO GALLERY

                    if(isset($_POST['move_to_gallery']) && isset($_POST['move_to_gallery_id']) && is_numeric($_POST['move_to_gallery_id'])){
                        $newgalleryid = $_POST['move_to_gallery_id'];

                        $q = "UPDATE images SET gallery = '{$newgalleryid}'
                              WHERE view_id IN (".$imagesid2.") AND id_user='{$_SESSION['user_id']}'";
                        $result = mysql_query($q);
                        if($result){


                            echo "<script language=\"javascript\" type=\"text/javascript\">
                                <!--
                                window.setTimeout('window.location=\"view_galleries.php?id=" . $newgalleryid . "\"; ',0);
                                // -->
                            </script>";

                            exit();

                        } else {
                            die(mysql_error());
                        }
                    }



                    // MARK AS ADULT
                    if(isset($_POST['mark_as_adult'])){

                        $q = "UPDATE images SET adult = '1'
                              WHERE view_id IN (".$imagesid2.") AND id_user='{$_SESSION['user_id']}'";
                        $result = mysql_query($q);
                        if($result){

                            ?>
                            <script language="javascript" type="text/javascript">
                                <!--
                                window.setTimeout('window.location="moderate_img.php?msg=You have marked as adult selected images succesfuly"; ',0);
                                // -->
                            </script>
                            <?php
                            exit();


                        } else {
                            die(mysql_error());
                        }
                    }

                    // MARK AS CLEAN
                    if(isset($_POST['mark_as_clean'])){

                        $q = "UPDATE images SET adult = '0'
                              WHERE view_id IN (".$imagesid2.") AND id_user='{$_SESSION['user_id']}'";
                        $result = mysql_query($q);
                        if($result){

                            ?>
                            <script language="javascript" type="text/javascript">
                                <!--
                                window.setTimeout('window.location="moderate_img.php?msg=You have marked as clean selected images succesfuly"; ',0);
                                // -->
                            </script>
                            <?php
                            exit();


                        } else {
                            die(mysql_error());
                        }
                    }






                } // if rowsnum > 0



                echo "
                    <br />
                    <form action='' method='POST'>
                    <input type='hidden' name='imagesid' value=\"{$_POST['imagesid']}\" />
                    <input type='submit' value='Delete All' name='delete' />
                    <input type='submit' value='Mark as adult' name='mark_as_adult' />
                    <input type='submit' value='Mark as clean' name='mark_as_clean' />
                    </form>
                    <br />
                    ";

                $q = "SELECT id, name FROM galleries WHERE id_user = {$_SESSION['user_id']}";
                $result = mysql_query($q);
                $numRowsGalleries = mysql_num_rows($result);

                if($numRowsGalleries > 0) {
                ?>
                    <form action="" method="POST">
                        <input type='hidden' name='imagesid' value="<?php echo $_POST['imagesid'];?>"  />
                        <select name="move_to_gallery_id">
                            <?php
                            while($rowGalleriesName = mysql_fetch_assoc($result)) {
                                echo "<option value='{$rowGalleriesName['id']}'>{$rowGalleriesName['name']}</option>";
                            }
                            ?>
                        </select>
                        <input type="submit" name="move_to_gallery" value="Move to gallery" />
                    </form>
                 <?php
                }


                if(isset($BBCode_global)) {
                    echo "<div id='allimagecodes'>";


                    echo "<label>All BB Codes:</label><br />
              <textarea onclick='this.select();' class='imageallcodes'>";
                    foreach($BBCode_global as $value){
                        echo $value . " ";
                    }
                    echo "</textarea>";

                    echo "<label>All HTML Codes:</label><br />
              <textarea onclick='this.select();' class='imageallcodes'>";
                    foreach($HTMLCode_global as $value){
                        echo $value . " ";
                    }
                    echo "</textarea>";

                    echo "<label>All Links Codes:</label><br />
              <textarea onclick='this.select();' class='imageallcodes'>";
                    foreach($DirectLink_global as $value){
                        echo $value . "\r\n";
                    }
                    echo "</textarea>";



                    echo "</div>";
                }

            } else {
                ?>
                <form action="" method="POST">
                    <div class="head_info">Type all images ID you wanna moderate, separeted by comma. Attention ! Picture name is not the same with image ID !</div>
                    <textarea name="imagesid" cols="60" rows="10"></textarea> <br />
                    <input type="submit" name="" value="Search" />
                </form>
                <?php
            }

            //print_r($_POST);
            ?>

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