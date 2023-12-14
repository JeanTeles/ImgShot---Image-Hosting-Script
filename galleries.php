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
    <link type="text/css" href="css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
    <style type="text/css">
        body { font-size: 62.5%; }
        label, input { display:block; }
        input.text { margin-bottom:12px; width:95%; padding: .4em; }
        fieldset { padding:0; border:0; margin-top:25px; }
        h1 { font-size: 1.2em; margin: .6em 0; }
    </style>

    <script type="text/javascript">
        $(function() {
            $( "#submit_gallery" ).button();
        });
    </script>

    <script type="text/javascript">
        $(function() {
            // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
            $( "#dialog:ui-dialog" ).dialog( "destroy" );


            $( "#dialog-form" ).dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true,
                buttons: {

                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                },
                close: function() {
                    allFields.val( "" ).removeClass( "ui-state-error" );
                }
            });

            $( "#add-gallery" )
                .button()
                .click(function() {
                    $( "#dialog-form" ).dialog( "open" );
                });
        });
    </script>

</head>
<body>
<?php include("inc/menu.php"); ?>

<div id="container">
    <?php include('inc/acc_lock_status.php'); ?>
    <div id="logo">
        <a href="index.php"><img border="0" alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>

    <?php include('inc/user_menu.php') ?>
    <div id="content">

        <?php if(isset($_GET['msg'])) {
        $value = filter($_GET['msg']);
         ?>
        <script>
            $(function() {
                // a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
                $( "#dialog:ui-dialog" ).dialog( "destroy" );

                $( "#dialog-message" ).dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
            });
        </script>

        <div id="dialog-message" title="Confirmation">
            <p>
                <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
                <?php echo $value; ?>
            </p>
        </div>
    <?php
        } ?>

        <!-- <a style="float:right;" class="plus_sign" href="add_gallery.php">Add gallery</a> -->
        <button style="float:right;" id="add-gallery">Add new gallery</button>

        <?php
        $q = "SELECT * FROM galleries WHERE id_user = '$_SESSION[user_id]'";
        $result = mysql_query($q);
        if(mysql_num_rows($result) > 0) {
            echo "
            <div id='users-contain' class='ui-widget'>
            <table class='ui-widget ui-widget-content' border='0'>
                <thead>
                    <tr class='ui-widget-header'>
                    <td>Name</td>
                    <td style='width:10px;'>Edit</td>
                    </tr>
                </thead><tbody>";
            while($rowGalleries = mysql_fetch_assoc($result)) {
                echo "<tr><td><a class='nicelinks' href='view_galleries.php?id=".$rowGalleries['id']."'>" . $rowGalleries['name'] . "</a></td> <td><a href='edit_gallery.php?id=".$rowGalleries['id']."'><img border='0' src='css/img/modify.gif' /></a></td></tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No galleries added yet ... ";
        }
        ?>




        <div id="dialog-form" title="Create new gallery">
            <p class="validateTips">All form fields are required.</p>

            <form action="add_gallery.php" method="POST">
                <fieldset>
                    <label for="name">Name</label>
                    <input type="text" name="gallery_name" id="name" class="text ui-widget-content ui-corner-all" />
                    <br />
                    <input type="submit" id="submit_gallery" class="ui-button ui-corner-all" value="Add gallery" name="submit_new_gallery" />
                </fieldset>
            </form>
        </div>




    </div>
</div>

<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>