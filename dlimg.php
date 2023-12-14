<?php
    session_start();
    require_once('config.php');
    $dbconnect = new db();
    $dbconnect->connect();

    $configs = new configs();
    $configsarr = $configs->fetch();

    $galleries = new galery();


    if(ctype_alnum($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        die("Incorrect Link");
    }

    $q = "SELECT images.name, images.date_added, images.ftp, sources.img2, ftp_logins.url
FROM images
LEFT JOIN ftp_logins ON images.ftp = ftp_logins.id
INNER JOIN sources ON images.source = sources.id
WHERE images.view_id = '$id'";

    $result = mysql_query($q);
    if(mysql_num_rows($result) > 0) {
    $rowImage = mysql_fetch_assoc($result);
    $dir = preg_replace('/-/', '/', $rowImage['date_added']);
    $dirImg = $rowImage['img2'] . "/" . $dir . "/" . $rowImage['name'];
    $dirImgFTP = $rowImage['url'] . "/" . $rowImage['img2'] . "/" . $dir . "/" . $rowImage['name'];


if (file_exists($dirImg) && $rowImage['ftp'] == 0) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($dirImg));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($dirImg));
    ob_clean();
    flush();
    readfile($dirImg);
    exit;
} elseif($rowImage['ftp'] > 0) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($dirImgFTP));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($dirImgFTP));
    ob_clean();
    flush();
    readfile($dirImgFTP);
    exit;
} else {
        header('Location: noimage.php');
        exit();
    }
    }

?>