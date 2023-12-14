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

$login = new login();

foreach($_GET as $key => $value) {
	$get[$key] = filter($value);
}

if(isset($get['user'])) {
$user = mysql_real_escape_string($get['user']);
}

if(isset($get['cmd']) && $get['cmd'] == 'check') {

if(!$login->isUserID($user)) {
echo "Invalid User ID";
exit();
}

if(empty($user) && strlen($user) <=3) {
echo "Enter 5 chars or more";
exit();
}



$rs_duplicate = mysql_query("select count(*) as total from users where user_name='$user' ") or die(mysql_error());
list($total) = mysql_fetch_row($rs_duplicate);

	if ($total > 0)
	{
	echo "Not Available";
	} else {
	echo "<span style='color:green;'>Available</span>";
	}
}

?>