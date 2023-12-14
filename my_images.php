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

if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0) {
    $pageid = $_GET['page'];
} else {
    $pageid = '1';
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
    <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
    <link type="text/css" href="css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
    <script>
        $(function() {
            $( "#selectable" ).selectable({
                selecting: function(event, ui) {
                    $(ui.selecting).find(':checkbox').attr('checked', true);
                },
                unselecting: function(event, ui) {
                    $(ui.unselecting).find(':checkbox').attr('checked', false);
                }
            });

        });
    </script>

    <script>
        $(function() {
            $( "input:submit, a, button", ".moderate_images" ).button();
        });
    </script>

    <!-- POPUP BOX CLICK IMAGE START -->
    <script language="javascript" type="text/javascript">
        <!--
        function popitup(url) {
            newwindow=window.open(url,'name','height=800,width=1024,resizable,scrollbars');
            if (window.focus) {newwindow.focus()}
            return false;
        }

        // -->
    </script>
    <!-- POPUP BOX CLICK IMAGE END -->

</head>
<body>
<?php include("inc/menu.php"); ?>

<div id="container">
    <?php include('inc/acc_lock_status.php'); ?>
    <div id="logo">
        <a href="index.php"><img border="0" alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>

    <?php include('inc/user_menu.php') ?>
    <form action="moderate_img.php" method="POST">
    <div id="content">

        <div id="selectable" class="all_images">
            <?php
            $qNum = "SELECT id FROM images WHERE id_user = '$_SESSION[user_id]'";
            $resultNum = mysql_query($qNum);

            $rowsnumber = mysql_num_rows($resultNum);
            if($rowsnumber < 1) {
                $rowsnumber = 1;
                $no_entries = true;
            }

//PAGINATION SCRIPT
            $p = new pagination();
            $arr = $p->calculate_pages($rowsnumber, 12, $pageid);
            $sql_limit = $arr['limit'];



            $q = "SELECT images.view_id, images.views, images.name, images.date_added, images.ftp, sources.thumb2, sources.img2, ftp_logins.url FROM images
             INNER JOIN sources ON images.source=sources.id
             LEFT JOIN ftp_logins ON images.ftp = ftp_logins.id
             WHERE id_user = '$_SESSION[user_id]'
             ORDER BY images.id DESC
             $sql_limit";
            $result = mysql_query($q);
            while($rowImages = mysql_fetch_assoc($result)) {
                if($rowImages['ftp'] > 0) {
                    $real_site_url = $rowImages['url'];
                } else {
                    $real_site_url = $site_url;
                }
                echo "<li class='ui-state-default'>";
                $dirDate = preg_replace('/-/', '/', $rowImages['date_added']);
                $dirThumb = $real_site_url . "/" . $rowImages['thumb2'] . "/" . $dirDate . "/" . $rowImages['name'];
                $dirImg = $real_site_url . "/" . $rowImages['img2'] . "/" . $dirDate . "/" . $rowImages['name'];
                echo "<div class='img_and_text'>
                <input type='checkbox' name='imagesidarr[]' style='display:none;' value='{$rowImages['view_id']}' />
                Views: {$rowImages['views']}<br />
                Added: {$rowImages['date_added']}<br />
                ID: {$rowImages['view_id']}

            <br />
            <a onclick=\"return popitup('img-{$rowImages['view_id']}.html')\" href=''><img src='{$dirThumb}' /></a>

            </div>";
                echo "</li>";
            }


            ?>


        </div>

    </div>
<div class='moderate_images'>
    <input type="submit" class="moderate_images" value="Moderate Selected Images" />
</div>
    </form>



    <div id="pagination">
        <?php
        echo "<br /><br /><br />";

        echo "<a href=\"my_images.php?page=$arr[previous]\" class=\"alfabet\">Previous</a> - ";

        foreach($arr['pages'] as $value) {
            echo "<a href=\"my_images.php?page=$value\" class=\"alfabet\">$value</a> ";
        }

        echo " - <a href=\"my_images.php?page=$arr[next]\" class=\"alfabet\"> Next</a> ";
        echo "<a href=\"my_images.php?page=$arr[last]\" class=\"alfabet\">Last</a>";


        echo "<br /><br /><p style=\"font-size:14px;\">" . $arr['info'] . "</p>";
        ?>
    </div>

</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>