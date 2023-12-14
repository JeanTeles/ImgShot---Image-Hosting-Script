<?php
require_once('config.php');
    $dbconnect = new db();
    $dbconnect->connect();

    if(isset($_GET['session_id'])){
        session_id($_GET['session_id']);
        session_start();
    }
	
	$upload = new upload();
    //print_r($_FILES);

	if(isset($_FILES['uploaded']) && $_FILES['uploaded']['error'] == 0) {
        $_POST['thumb_size_contaner'] = 2;

        if(isset($_POST['adult']) && is_numeric($_POST['adult']) && $_POST['adult'] >= 0 && $_POST['adult'] <= 1){ // it's doubled, i may delete this
            // print_r($_POST);
        } else {
            $_POST['adult'] = 1;
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

        echo "<div id='ajaxup'>";
	$upload->regular_upload('uploaded', randomServer());
        echo "</div>";
	} else {
	echo "error";
	}
?>