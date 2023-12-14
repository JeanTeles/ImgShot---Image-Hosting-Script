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


session_start();
require_once('config.php');
$dbconnect = new db();
$dbconnect->connect();

$configs = new configs();
$configsarr = $configs->fetch();

$galleries = new galery();


if(ctype_alnum($_GET['id'])) {
    $id = $_GET['id'];
    if (preg_match("/php/i", $_SERVER['REQUEST_URI'])) {
        //echo $_SERVER['REQUEST_URI'];
        header('Location: img-'. $id .'.html');
        exit();
    }
} else {
    die("Incorrect Link");
}

$q = "SELECT images.id_user, images.id, images.name, images.gallery, images.views, images.date_added, images.source, images.ftp, images.adult, sources.img2, sources.thumb2
FROM images
INNER JOIN sources ON images.source = sources.id
WHERE images.view_id = '$id'";

$result = mysql_query($q);
if(mysql_num_rows($result) > 0) {
$rowImage = mysql_fetch_assoc($result);

    $real_site_url = $site_url;

if($rowImage['ftp'] > 0) {
    $q = "SELECT url FROM ftp_logins WHERE id = '{$rowImage['ftp']}'";
    $result = mysql_query($q);
    $rowFTP = mysql_fetch_assoc($result);
    $site_url = $rowFTP['url'];
}


$dir = preg_replace('/-/', '/', $rowImage['date_added']);
$dirImg = $site_url . "/" . $rowImage['img2'] . "/" . $dir . "/" . $rowImage['name'];
$dirThumb = $site_url . "/" . $rowImage['thumb2'] . "/" . $dir . "/" . $rowImage['name'];


$viewsupdate = $rowImage['views'];
$viewsupdate++;


$currentDate = date('Y-m-d');
mysql_query("UPDATE images SET views = '$viewsupdate', last_view = '$currentDate' WHERE view_id = '$id'");



} else {
    header('Location: noimage.php');
    exit();
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
    <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
    <link type="text/css" href="css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
    <!-- POPUP BOX CLICK IMAGE START -->
    <script type="text/javascript">
        <!--
        function popitup(url) {
            newwindow=window.open(url,'name','height=800,width=1024');
            if (window.focus) {newwindow.focus()}
            return false;
        }

        // -->

    </script>

    <script>
        $(function() {
            $( ".downloadimg button" ).button({
                icons: {
                    primary: "ui-icon-image"
                }
            })
        });
    </script>

    <!-- POPUP BOX CLICK IMAGE END -->
</head>
<body>

<?php include("inc/menu.php"); ?>

<div id="container">
    <div id="logo">
        <a href="index.php"><img alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>

    <div class="top_ads">
        <?php



    if($configs->isPremium($rowImage['id_user'])) {
        echo stripslashes($configsarr['premium_ads_top']);
    } else {
        switch($rowImage['adult']) {
            case 0:
                echo stripslashes($configsarr['clean_top_ads']);
                break;
            case 1:
                echo stripslashes($configsarr['adult_top_ads']);
                break;
        }
        }
        ?>
    </div>

<?php if(CONTINUE_TO_IMAGE == 1 && !isset($_POST['imgContinue'])) {

    echo "
    <div id='continuetoimage'>
        <form action='' method='POST'>
            <p>
                <input class='button white bigwidth' type='submit' name='imgContinue' value='Continue to image ... ' />
            </p>
        </form>
    </div>
    ";

} else { ?>
    <div id="image_details">
        <?php
        list($width, $height) = getimagesize($dirImg);
        echo "<p><strong>Date added:</strong> {$rowImage['date_added']}</p>";
        echo "<p><strong>Views:</strong> {$viewsupdate} </p>";
        echo "<p><strong>Width:</strong> {$width}px</p>";
        echo "<p><strong>Height:</strong> {$height}px</p>";
            if($width > 1000) {
                echo "<br /><br /><p><i>This image was resized. Click to view full size</i></p>";
            }
        ?>
    </div>

<?php

if($width > 1000) {
//echo "<p>This image was resized. Click to view full size</p>";
echo "<a href='{$dirImg}' onclick=\"return popitup('{$dirImg}')\"><img class='centred_resized' src='{$dirImg}' alt='image' /></a>";
} else {
echo "<img class='centred' src='{$dirImg}' alt='image' />";
}
?>
<div class="downloadimg">
    <button onclick="return popitup('dlimg.php?id=<?php echo $id; ?>')">Download Image</button>
</div>
    <?php
    $q = "SELECT download_links FROM images_opt WHERE id_img = '$rowImage[id]'";
    $result = mysql_query($q);
    if(mysql_num_rows($result) > 0) {
        $rowImgOpt = mysql_fetch_assoc($result);
    echo "

    <textarea id='download_links'>{$rowImgOpt['download_links']}</textarea>

    ";
    }

    ?>
    <script type="text/javascript">
        $(function() {
            $( "#accordion" ).accordion();
        });
    </script>


    <div style="width:600px; margin:auto; margin-top:50px; margin-bottom:20px;">

        <div id="accordion">
            <h3><a href="#">Codes</a></h3>
            <div>
                <div id="imagecodes">
                    <label>BB Code:</label><br />
                    <input type='text' onclick="this.select();" value="<?php echo "[URL={$real_site_url}/img-{$id}.html][IMG]{$dirThumb}[/IMG][/URL]"; ?>">
                    <br /> <br />
                    <label>HTML:</label><br />
                    <input type='text' onclick="this.select();" value="<?php echo "<a href='{$real_site_url}/img-{$id}.html'><img src='{$dirThumb}' alt='image' /></a>"; ?>">
                    <br /> <br />
                    <label>Link:</label><br />
                    <input type='text' onclick="this.select();" value="<?php echo "{$real_site_url}/img-{$id}.html"; ?>">
                    <?php
                    if(DIRECT_LINK_SHOW == 1) {
                        echo "
                        <br /> <br />
                        <label>Direct Link to image:</label><br />
                        <input type='text' onclick='this.select();' value='{$dirImg}'>
                        ";
                    }
                    ?>
                </div>
            </div>

            <?php
            if($rowImage['gallery'] > 0) {
            echo "
            <h3><a href='#'>More from this gallery</a></h3>
            <div>
            ";
                $galleries->moreFromThisGallery($rowImage['gallery']);

            echo "</div>";
            }
            ?>

            <h3><a href="#">Share</a></h3>
            <div>
               <?php echo stripslashes($configsarr['share_plugins']); ?>
            </div>
        </div>
    </div>

<?php
} // END ELSE CONTINUE TO IMAGE
?>

<div class="bottom_ads">
<?php
    if($configs->isPremium($rowImage['id_user'])) {
        echo stripslashes($configsarr['premium_ads_bottom']);
    } else {
    switch($rowImage['adult']) {
        case 0:
            echo stripslashes($configsarr['clean_bottom_ads']);
            break;
        case 1:
            echo stripslashes($configsarr['adult_bottom_ads']);
            break;
    }
    }

?>
</div>


</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);

switch($rowImage['adult']) {
    case 0:
        echo stripslashes($configsarr['popup_clean']);
        break;
    case 1:
        echo stripslashes($configsarr['popup_adult']);
        break;
}
?>


</body>
</html>