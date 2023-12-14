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


if(ctype_alnum($_GET['id'])) {
    $id = $_GET['id'];
    if (preg_match("/php/i", $_SERVER['REQUEST_URI'])) {
        //echo $_SERVER['REQUEST_URI'];
        header('Location: slide-'. $id .'.html');
        exit();
    }

    $q = "SELECT galleries.name, galleries.public FROM galleries WHERE id = '{$id}'";
    $result = mysql_query($q);
    if($result && mysql_num_rows($result) > 0){
        $rowGallery = mysql_fetch_assoc($result);
    } else {
        header('Location: index.php');
        exit();
    }
} else {
    die("Incorrect Link");
}


?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $rowGallery['name'] . " - " . $site_title; ?></title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="description" content="<?php echo $site_meta_description; ?>" />
    <meta name="keywords" content="<?php echo $site_meta_keywords; ?>" />
    <meta name="author" content="<?php echo $site_meta_author; ?>" />
    <link rel="stylesheet" href="css/ninvo-slider/themes/default/default.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/ninvo-slider/themes/pascal/pascal.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/ninvo-slider/themes/orman/orman.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="css/ninvo-slider/nivo-slider.css" type="text/css" media="screen" />
    <link type="text/css" href="css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />

    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.nivo.slider.pack.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>

    <script type="text/javascript">
        $(window).load(function() {
            $('#slider').nivoSlider();
        });
    </script>



</head>
<body>
<?php include("inc/menu.php"); ?>

<div id="container">
    <div id="logo">
        <a href="index.php"><img alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>
    <div class="title">
    <?php echo $rowGallery['name']; ?>
    </div>
   <div id="content">
       <?php
       if($rowGallery['public'] == 1) {
?>
       
       
       <div class="slider-wrapper theme-default">
           <div class="ribbon"></div>
           <div id="slider" class="nivoSlider">
               <?php
               $q = "SELECT images.name, sources.img2, images.date_added, images.ftp, ftp_logins.url FROM images
               INNER JOIN sources ON images.source = sources.id
               LEFT JOIN ftp_logins ON images.ftp = ftp_logins.id
                WHERE images.gallery = '{$id}'";
               $result = mysql_query($q);
               while ($rowImage = mysql_fetch_assoc($result)) {
                   if($rowImage['ftp'] > 0) {
                       $real_site_url = $rowImage['url'];
                   } else {
                       $real_site_url = $site_url;
                   }

               $dir = preg_replace('/-/', '/', $rowImage['date_added']);
               $dirImg = $real_site_url . "/" . $rowImage['img2'] . "/" . $dir . "/" . $rowImage['name'];
                echo "<img src=\"{$dirImg}\" alt=\"{$rowImage['name']}\" />";
               }
?>
           </div>
       </div>

           <?php
       } else {
           echo "<p class='error'>This gallery it's not public.</p>";
       }
?>

   </div>




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
                    <label>BB Code :</label><br />
                    <input type='text' onclick="this.select();" value="<?php echo $site_url . "/slide-" . $id . ".html"; ?>">
                    <br /> <br />
                    <label>HTML Link:</label><br />
                    <input type='text' onclick="this.select();" value="<?php echo $site_url . "/slide-" . $id . ".html"; ?>">
                    <br /> <br />
                    <label>Direct link:</label><br />
                    <input type='text' onclick="this.select();" value="<?php echo $site_url . "/slide-" . $id . ".html"; ?>">
                </div>
            </div>

            <h3><a href="#">Share</a></h3>
            <div>
                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                    <a class="addthis_button_preferred_1"></a>
                    <a class="addthis_button_preferred_2"></a>
                    <a class="addthis_button_preferred_3"></a>
                    <a class="addthis_button_preferred_4"></a>
                    <a class="addthis_button_compact"></a>
                    <a class="addthis_counter addthis_bubble_style"></a>
                </div>

                <div class="addthis_toolbox addthis_default_style">
                    <a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                    <a class="addthis_button_tweet"></a>
                    <a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
                </div>

                <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4fac205412782f21"></script>

                <!-- AddThis Button END -->
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