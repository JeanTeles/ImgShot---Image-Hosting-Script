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

banIPcheck();

$configs = new configs();
$configsarr = $configs->fetch();


        if(isset($_SESSION['user_id'])){
            $gallery_selection = "<div class='gallery_select'>
            <select name='set_gallery'>
            <option value=''>No gallery</option>";
            $q = "SELECT galleries.id, galleries.name FROM galleries WHERE id_user = {$_SESSION['user_id']}";
            $result = mysql_query($q);
            if($result){
                if(mysql_num_rows($result) > 0) {
                    while($rowGalleries = mysql_fetch_assoc($result)){
                        $gallery_selection .= "<option value='{$rowGalleries['id']}'> {$rowGalleries['name']} </option>";
                    }
                }
            }
            $gallery_selection .= "
            </select>
            </div>
            ";
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
    <link type="text/css" href="css/ui-lightness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />

    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <!--[if !IE]> -->
    <link type="text/css" href="css/csTransPie.css" rel="stylesheet" />
    <script type="text/javascript" src="js/csTransPieManual.js"></script>
    <!-- <![endif]-->

    <!--[if IE]>
    <link type="text/css" href="css/csTransPieCustom.css" rel="stylesheet" />
    <![endif]-->

  <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
  <script type="text/javascript" src="js/jquery.validate.js"></script>

    <?php
    if(AJAX_UPLOAD == 1) {
        if(isset($_GET['adult']) && is_numeric($_GET['adult'])) {
            switch($_GET['adult']){
                case 1:
                    $javaadult = 1;
                    break;
                case 0:
                    $javaadult = 0;
                    break;
            }
        }

        if(!isset($_GET['adult'])){
            $javaadult = 1;
        }
?>

  <script type="text/javascript" src="js/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
  <script type="text/javascript" src="js/uploadify/swfobject.js"></script>

  <script type="text/javascript">
      // <![CDATA[
      $(document).ready(function() {
          $('#file_upload').uploadify({
              'uploader'  : 'js/uploadify/uploadify.swf',
              'script'    : 'ajaxup.php<?php if(isset($_SESSION['user_id'])) { echo "?session_id=" . session_id() ; } ?>',
              'cancelImg' : 'css/img/cancel.png',
              'auto'      : true,
              'displayData' : 'speed',
              'multi'       : true,
              'fileDataName' : 'uploaded',
              'fileExt'     : '*.jpg;*.gif;*.png;*.jpeg',
              'fileDesc'    : 'Image Files',
              'queueSizeLimit' : 30,
              'simUploadLimit' : 1,
              'sizeLimit'   : 5024*1024,
              'scriptData'         : {'adult' : '<?php echo $javaadult; ?>'},

              'onOpen'      : function() {

                  document.getElementById('index_upload').style.cssText = 'width: 600px !important';
                  document.getElementById('ajax_allbbcodes').style.cssText = 'display:inherit;';

                  $("#ajax_bbcodes").animate({
                      width: "500",
                      height: "100"
                  }, 1000 );

                  $("#ajax_HTMLcodes").animate({
                      width: "500",
                      height: "100"
                  }, 1000 );

                  $("#ajax_DirectLinks").animate({
                      width: "500",
                      height: "100"
                  }, 1000 );
                  <?php if(DIRECT_LINK_SHOW == 1) { ?>
                  $("#ajax_DirectLinkToImgs").animate({
                      width: "500",
                      height: "100"
                  }, 1000 );
                  <?php } ?>
              },
              'onProgress'  : function(event,ID,fileObj,data) {
                  var bytes = Math.round(data.bytesLoaded / 1024);
                  $('#' + $(event.target).attr('id') + ID).find('.percentage').text(' - ' + bytes + 'KB Uploaded');


                  document.getElementById('progressbarOver').style.cssText = 'display:inherit;';


                  $("#progressbar").animate({
                      width: data.percentage + "%",
                      height: "20"
                  }, 200 );


                  return false;
              },
              'onComplete'  : function(event, ID, fileObj, response, data) {

                  old_guests = document.getElementById("testajax").innerHTML;
                  document.getElementById('testajax').innerHTML = old_guests + response;

                  myArrayBBCode = $('.ajax_BBCode');
                  oldbb = document.getElementById('ajax_bbcodes').innerHTML;
                  document.getElementById('ajax_bbcodes').innerHTML = oldbb + myArrayBBCode[myArrayBBCode.length-1].innerHTML + ' ';

                  myArrayHTMLCode = $('.ajax_HTMLCode');
                  oldbbHTMLCode = document.getElementById('ajax_HTMLcodes').innerHTML;
                  document.getElementById('ajax_HTMLcodes').innerHTML = oldbbHTMLCode + myArrayHTMLCode[myArrayHTMLCode.length-1].innerHTML + ' ';

                  myArrayDirectLink = $('.ajax_DirectLink');
                  oldbbDirectLink = document.getElementById('ajax_DirectLinks').innerHTML;
                  document.getElementById('ajax_DirectLinks').innerHTML = oldbbDirectLink + myArrayDirectLink[myArrayDirectLink.length-1].innerHTML + ' \r\n';

                <?php if(DIRECT_LINK_SHOW == 1) { ?>
                  myArrayDirectLinkToImg = $('.ajax_DirectLinkToImg');
                  oldbbDirectLinkToImg = document.getElementById('ajax_DirectLinkToImgs').innerHTML;
                  document.getElementById('ajax_DirectLinkToImgs').innerHTML = oldbbDirectLinkToImg + myArrayDirectLinkToImg[myArrayDirectLinkToImg.length-1].innerHTML + ' \r\n';
                <?php } ?>




              },
              'onAllComplete' : function(event,data) {
                  document.getElementById('progressbarOver').style.cssText = 'display:none;';
              }


          });
      });

      // ]]>

  </script>
      <?php } ?>

    <script type="text/javascript">
        $(function(){

            // Tabs
            $('#tabs').tabs();

            //hover states on the static widgets
            $('#dialog_link, ul#icons li').hover(
                function() { $(this).addClass('ui-state-hover'); },
                function() { $(this).removeClass('ui-state-hover'); }
            );

        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $(".validateForm").each(function() {
                $(this).validate(
                    {
                        rules: {
                            uploaded: {
                                required: true,
                                accept: "jpg|jpeg|gif|png"
                            }
                        }
                    }
                );
            });
        });
    </script>

  <!--[if !IE]> -->

    <script type="text/javascript">
        $(function()
        {
            $(".validateForm").cTP();
        });
    </script>

    <!-- <![endif]-->





</head>
<body>


<?php include("inc/menu.php"); ?>

<!--[if IE]>
<div id="ie_message"> For best experience, use other browser than IE</div>
<![endif]-->

<div id="container">
    <div id="logo">
        <a href="index.php"><img alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>

    <div id="index_upload">
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">Single</a></li>
                <li><a href="#tabs-2">Multi</a></li>
                <li><a href="#tabs-3">Ajax</a></li>
                <li><a href="#tabs-4">Remote</a></li>
                <li><a href="#tabs-5">Cover</a></li>
                <li><a href="#tabs-6">ZIP</a></li>
            </ul>
            <?php if(UPLOAD_ONLY_REGISTERED == 1 && !isset($_SESSION['user_id'])) {
            echo "<p class='error'>Please register to upload</p>";
        }
            ?>
            <div id="tabs-1"><?php include('inc/regular_upload.php'); ?></div>
            <div id="tabs-2"><?php if(MULTI_UPLOAD == 1) { include('inc/multi_upload.php'); } else { echo "<p class='error2'>Function deactivated</p>"; }?></div>
            <div id="tabs-3"><?php if(AJAX_UPLOAD == 1) { include('inc/ajax_upload.php'); } else { echo "<p class='error2'>Function deactivated</p>"; }?></div>
            <div id="tabs-4"><?php if(REMOTE_UPLOAD == 1) { include('inc/remote_upload.php'); } else { echo "<p class='error2'>Function deactivated</p>"; }?></div>
            <div id="tabs-5"><?php if(COVER_UPLOAD == 1) { include('inc/cover_upload.php'); } else { echo "<p class='error2'>Function deactivated</p>"; }?></div>
            <div id="tabs-6"><?php if(ZIP_UPLOAD == 1) { include('inc/zip_upload.php'); } else { echo "<p class='error2'>Function deactivated</p>"; }?></div>
        </div>




    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>