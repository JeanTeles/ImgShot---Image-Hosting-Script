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



if(isset($_GET['name']) && ctype_alnum($_GET['name'])) {
    $name = $_GET['name'];


    if (preg_match("/php/i", $_SERVER['REQUEST_URI'])) {
        //echo $_SERVER['REQUEST_URI'];
        header('Location: page-'. $name .'.html');
        exit();
    }




    $q = "SELECT content FROM pages WHERE title = '$name'";
    $result = mysql_query($q);
    if(mysql_num_rows($result) > 0) {
        $rowPage = mysql_fetch_assoc($result);
    } else {
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo ucfirst($name) . " - " . $site_title; ?></title>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta name="description" content="<?php echo $site_meta_description; ?>" />
    <meta name="keywords" content="<?php echo $site_meta_keywords; ?>" />
    <meta name="author" content="<?php echo $site_meta_author; ?>" />
    <link rel="stylesheet" type="text/css" href="css/styles.css" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jquery.validate.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#uploadForm").validate(
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
    </script>

</head>
<body>
<?php include("inc/menu.php"); ?>

<div id="container">
    <div id="logo">
        <a href="index.php"><img alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>
    <div class="title">
    <?php echo ucfirst($name); ?>
    </div>
   <div id="content">
       <?php
       switch($name){
           case 'contact':
               include("inc/protected/contact.php");
               echo "<hr>";
               break;
       }
       echo stripslashes($rowPage['content']);
       ?>
   </div>

</div>


<?php
include('inc/footer.php');
echo stripslashes($configsarr['analytics']);
?>

</body>
</html>