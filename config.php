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

$site_url = "http://yoursite.com";
$site_name = "Image Hosting";
$site_title = "Image Hosting Script";
$site_meta_description = "Share your images";
$site_meta_keywords = "images, share";
$site_meta_author = "Zamfi";
$site_version = "1.2";
$logo_location = "css/img/logo.png";

// ADMIN CONFIGURATIONS
define("ADMIN_EMAIL", "admin@yoursite.com"); // email address for various notifications like IPN's confirmations or error logs, contact form and other administrative things.
define("UPLOAD_ONLY_REGISTERED", 0); // 1 = UPLOAD ONLY FOR REGISTERED USERS // 0 = UPLOAD FOR EVERYBODY

// DATABASE CONFIGURATIONS
define ("DB_HOST", ""); // set database host
define ("DB_USER", ""); // set database user
define ("DB_PASS",""); // set database password
define ("DB_NAME",""); // set database name

// UPLOADING IMAGE SETTINGS
define ("MAX_UPLOAD_SIZE", 10000); // in KB
define ("MAX_UPLOAD_SIZE_ZIP", 20000); // in KB
define ("MAX_UPLOAD_WIDTH", 2000); // pixels
define ("MAX_UPLOAD_HEIGHT", 2000); // pixels

// THUMBNAILS SIZE
define("SMALL_THUMB", 100); // pixels
define("MEDIUM_THUMB", 180); // pixels
define("LARGE_THUMB", 250); // pixels
define("LARGER_THUMB", 300); // pixels
define("COVER_THUMB", 500); // pixels

// UPLOADING TYPES
// 1 = YES | 2 = NO //
define("MULTI_UPLOAD", 1);
define("AJAX_UPLOAD", 1);
define("REMOTE_UPLOAD", 1);
define("COVER_UPLOAD", 1);
define("ZIP_UPLOAD", 1);

// OTHER IMAGES CONFIGURATIONS
define("DIRECT_LINK_SHOW", 0); // Direct link to image appear everywhere // 1 = yes, 0 = no
define("ADULT_RADIOBOX", 1); // 0 = Auto checked to NON-ADULT, 1 = Auto checked to ADULT, 2 = Require for user to choose what kind of image (mandatory)
define("CONTINUE_TO_IMAGE", 0); // 0 = DISABLED, 1 = ENABLED // Continue to image button

// EMAIL CONFIGURATIONS
define("PHPMAIL_PORT", 25); // Mail port
define("PHPMAIL_HOST", ""); // Mail host
define("PHPMAIL_MAIL", ""); // Mail address (it's best to put something like noreply@yoursite.com)
define("PHPMAIL_PASSWORD", ""); // Mail password

// USER GALLERIES CONFIGURATIONS
define("MAX_REGULAR_GALLERIES", 5);
define("MAX_PREMIUM_GALLERIES", 500);

// IPN CONFIGURATIONS (Paypal Gateway for premium payments)
define("IPN_ACTIVE", 1);
define("IPN_SANDBOX", false); // FALSE - to activate real paypal IPN or TRUE for sandbox testing mode

define("PAYPAL_ACCOUNT", "paypalacc@yahoo.com");

define("IPN_ONE_MONTH_PACKAGE", 10.00); //PRICE IN USD FOR A MONTH
define("IPN_THREE_MONTHS_PACKAGE", 20.00); //PRICE IN USD FOR A MONTH
define("IPN_SIX_MONTH_PACKAGE", 50.00); //PRICE IN USD FOR A MONTH
define("IPN_ONE_YEAR_PACKAGE", 70.00); //PRICE IN USD FOR A MONTH

// FTP UPLOADS CONFIGURATIONS
/*
 * 0 = Multiserver disabled - all uploads will be on your current server even you have any servers activated in Admin Panel (Local only)
 * 1 = Multiserver enabled - your uploads will be randomly mixed only on your active servers (FTP only)
 * 2 = Multiserver enabled mixed - your uploads will be randomly mixed on your active servers and also on your local server (FTP + Local)
 */
define("MULTISERVER", 0);

/* Registration Type (Automatic or Manual)
 1 -> Automatic Registration (Users will receive activation code and they will be automatically approved after clicking activation link)
 0 -> Manual Approval (Users will not receive activation code and you will need to approve every user manually)
*/
$user_registration = 1;  // set 0 or 1

define("COOKIE_TIME_OUT", 10); //specify cookie timeout in days (default is 10 days)
define('SALT_LENGTH', 9); // salt for password

//define ("ADMIN_NAME", "admin"); // sp

/* Specify user levels - better to leave it how it is at this moment */
define ("ADMIN_LEVEL", 5);
define ("USER_LEVEL", 1);
define ("GUEST_LEVEL", 0);


/*************** reCAPTCHA KEYS****************/
$publickey = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
$privatekey = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";

include('inc/functions.php');
?>