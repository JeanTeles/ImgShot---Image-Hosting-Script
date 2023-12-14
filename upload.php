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
    session_start();

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
    <script type="text/javascript" src="js/csTransPie.js"></script>
    <link type="text/css" href="css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
    <link type="text/css" href="css/csTransPie.css" rel="stylesheet" />
</head>
<body>
<?php include("inc/menu.php"); ?>
<div id="container">
    <div id="logo">
        <a href="index.php"><img border="0" alt="logo" src="<?php echo $logo_location; ?>" /></a>
    </div>

    <script>
        $(function() {
            $( "#accordion" ).accordion({ autoHeight: false });
        });
    </script>

<?php
    if(UPLOAD_ONLY_REGISTERED == 1){
        if(!isset($_SESSION['user_id'])){
            header('Location: register.php');
            exit();
        }
    }

    if(MULTISERVER == 0) {
        function randomServer(){
            return 0;
        }
    } elseif(MULTISERVER == 1) {
        $q = "SELECT id FROM ftp_logins WHERE active = 1";
        $result = mysql_query($q);
        while($rowArr = mysql_fetch_assoc($result)){
            $numbers[] = $rowArr['id'];
        }
        $numbersCount = count($numbers)-1;
        //echo "Number_array: {$numbersCount}<br />";
        function randomServer(){
            global $numbersCount;
            global $numbers;
            $arrNumber = $numbers[rand(0,$numbersCount)];
            return $arrNumber;
        }
        //echo $numbers[$arrNumber];
    } elseif(MULTISERVER == 2) {
        $q = "SELECT id FROM ftp_logins WHERE active = 1";
        $result = mysql_query($q);
        while($rowArr = mysql_fetch_assoc($result)){
            $numbers[] = $rowArr['id'];
        }
        $numbersCount = count($numbers)-1;
        //echo "Number_array: {$numbersCount}<br />";
        function randomServer(){
            global $numbersCount;
            global $numbers;
            $numbersCount++;
            $numbers[] = 0;
            //print_r($numbers);
            $arrNumber = $numbers[rand(0,$numbersCount)];
            return $arrNumber;
        }
        //echo $numbers[$arrNumber];
    }


    if(isset($_POST['zip_upload']) && ZIP_UPLOAD == 1){
        $BBCode_global = array();
        $HTMLCode_global = array();
        $DirectLink_global = array();
        $DirectLinkToImg_global = array();

        $dirname = uniqid();
        mkdir("cache/zip/$dirname", 0777);
        mkdir("cache/zip/$dirname/extracted", 0777);
        $zip = new ZIP();
        $upp = new upload();


        $zip->zipFileUpload('uploadedzip');
        if($zip->zipIsValid('cache/zip/'.$uniquezipnamenumber.'.zip')){
        $zip->extract_upload('cache/zip/'.$uniquezipnamenumber.'.zip', 'cache/zip/'.$dirname.'/extracted/');


        if ($handle = opendir("cache/zip/$dirname/extracted")) {
            echo "<div style='width:600px; margin:auto;'><div id='accordion'>";
            $numImg = 1;
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {

                    //echo $file;


                    echo "<h3><a href='#'>Image {$numImg}</a></h3><div>";
                    $upp->zipUpload("cache/zip/$dirname/extracted/$file", randomServer());
                    echo "</div>";
                    $numImg++;
                }
            }
            closedir($handle);

            echo "</div></div>";
        }
        $zip->delTree("cache/zip/$dirname/extracted/");
        rmdir("cache/zip/$dirname/");
        unlink("cache/zip/$uniquezipnamenumber.zip");
        }
    } elseif(isset($_POST['zip_upload']) && ZIP_UPLOAD == 0){
        echo "<p class='error'>Sorry, ZIP upload function is deactivated</p>";
    }




        $upload = new upload();
        //print_r($_FILES);
    if(isset($_POST['single_remote_upload']) && isset($_POST['remote_upload']) && strlen($_POST['remote_upload']) > 5 && REMOTE_UPLOAD == 1) {
        echo "<div style='width:600px; margin:auto;'><div id='accordion'>";
        echo "<h3><a href='#'>Image 1</a></h3><div>";
        $upload->remoteUpload($_POST['remote_upload'], randomServer());
        echo "</div>";
        echo "</div></div>";
    } elseif(isset($_POST['single_remote_upload']) && isset($_POST['remote_upload']) && strlen($_POST['remote_upload']) > 5 && REMOTE_UPLOAD == 0){
        echo "<p class='error'>Sorry, remote upload function is deactivated</p>";
    }




    if(isset($_POST['simple_upload']) || isset($_POST['multi_upload'])) {
        $BBCode_global = array();
        $HTMLCode_global = array();
        $DirectLink_global = array();
        $DirectLinkToImg_global = array();



        echo "<div style='width:600px; margin:auto;'><div id='accordion'>";


    if(isset($_FILES['uploaded']) && $_FILES['uploaded']['error'] == 0) {
        echo "<h3><a href='#'>Image 1</a></h3><div>";
        $upload->regular_upload('uploaded', randomServer());
        echo "</div>";
    }

    if(isset($_FILES['uploaded2']) && $_FILES['uploaded2']['error'] == 0) {
        echo "<h3><a href='#'>Image 2</a></h3><div>";
        $upload->regular_upload('uploaded2', randomServer());
        echo "</div>";
    }

    if(isset($_FILES['uploaded3']) && $_FILES['uploaded3']['error'] == 0) {
        echo "<h3><a href='#'>Image 3</a></h3><div>";
        $upload->regular_upload('uploaded3', randomServer());
        echo "</div>";
    }

    if(isset($_FILES['uploaded4']) && $_FILES['uploaded4']['error'] == 0) {
        echo "<h3><a href='#'>Image 4</a></h3><div>";
        $upload->regular_upload('uploaded4', randomServer());
        echo "</div>";
    }

    if(isset($_FILES['uploaded5']) && $_FILES['uploaded5']['error'] == 0) {
        echo "<h3><a href='#'>Image 5</a></h3><div>";
        $upload->regular_upload('uploaded5', randomServer());
        echo "</div>";
    }

    if(isset($_FILES['uploaded6']) && $_FILES['uploaded6']['error'] == 0) {
        echo "<h3><a href='#'>Image 6</a></h3><div>";
        $upload->regular_upload('uploaded6', randomServer());
        echo "</div>";
    }

    if(isset($_FILES['uploaded7']) && $_FILES['uploaded7']['error'] == 0) {
        echo "<h3><a href='#'>Image 7</a></h3><div>";
        $upload->regular_upload('uploaded7', randomServer());
        echo "</div>";
    }

    if(isset($_FILES['uploaded8']) && $_FILES['uploaded8']['error'] == 0) {
        echo "<h3><a href='#'>Image 8</a></h3><div>";
        $upload->regular_upload('uploaded8', randomServer());
        echo "</div>";
    }

    if(isset($_FILES['uploaded9']) && $_FILES['uploaded9']['error'] == 0) {
        echo "<h3><a href='#'>Image 9</a></h3><div>";
        $upload->regular_upload('uploaded9', randomServer());
        echo "</div>";
    }

    if(isset($_FILES['uploaded10']) && $_FILES['uploaded10']['error'] == 0) {
        echo "<h3><a href='#'>Image 10</a></h3><div>";
        $upload->regular_upload('uploaded10', randomServer());
        echo "</div>";
    }

    echo "</div></div>";

    }


    //ALL UPLOADS

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

        if(DIRECT_LINK_SHOW == 1) {
            echo "<label>All Direct Links to image Codes:</label><br />
                  <textarea onclick='this.select();' class='imageallcodes'>";
            foreach($DirectLinkToImg_global as $value){
                echo $value . "\r\n";
            }
            echo "</textarea>";
        }



        echo "</div>";
    }
?>


</div>

<?php
include('inc/footer.php');
?>


</body>
</html>