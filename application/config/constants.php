<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | Display Debug backtrace
  |--------------------------------------------------------------------------
  |
  | If set to TRUE, a backtrace will be displayed along with php errors. If
  | error_reporting is disabled, the backtrace will not display, regardless
  | of this setting
  |
 */
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
defined('FILE_READ_MODE') OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') OR define('DIR_WRITE_MODE', 0755);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */
defined('FOPEN_READ') OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
  |--------------------------------------------------------------------------
  | Exit Status Codes
  |--------------------------------------------------------------------------
  |
  | Used to indicate the conditions under which the script is exit()ing.
  | While there is no universal standard for error codes, there are some
  | broad conventions.  Three such conventions are mentioned below, for
  | those who wish to make use of them.  The CodeIgniter defaults were
  | chosen for the least overlap with these conventions, while still
  | leaving room for others to be defined in future versions and user
  | applications.
  |
  | The three main conventions used for determining exit status codes
  | are as follows:
  |
  |    Standard C/C++ Library (stdlibc):
  |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
  |       (This link also contains other GNU-specific conventions)
  |    BSD sysexits.h:
  |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
  |    Bash scripting:
  |       http://tldp.org/LDP/abs/html/exitcodes.html
  |
 */
defined('EXIT_SUCCESS') OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/**
 * Contants for Tables
 */
define('TBL_BUSINESSES', 'businesses');
define('TBL_BUSINESSES_SETTINGS', 'business_settings');
define('TBL_BUSINESS_PROMO_IMAGES', 'business_promo_images');
define('TBL_BUSINESS_TYPES', 'business_types');
define('TBL_USER_HEAR_ABOUTS', 'user_hear_abouts');
define('TBL_CARD_DETAIL', 'card_detail');
define('TBL_CART', 'cart');
define('TBL_CART_ITEM', 'cart_item');
define('TBL_CHECK_IN', 'check_in');
define('TBL_CITIES', 'cities');
define('TBL_COUNTRIES', 'countries');
define('TBL_FAVORITES', 'favorites');
define('TBL_HOTELS', 'hotels');
define('TBL_ICPS', 'icps');
define('TBL_ICP_IMAGES', 'icp_images');
define('TBL_ICP_IMAGE_TAG', 'icp_image_tag');
define('TBL_ICP_PHYSICAL_PRODUCT_IMAGES', 'icp_physical_product_images');
define('TBL_ICP_SETTINGS', 'icp_settings');
define('TBL_LIKES', 'likes');
define('TBL_ORDERS', 'orders');
define('TBL_ORDER_DETAIL', 'order_detail');
define('TBL_SETTINGS', 'settings');
define('TBL_SHIPPING_ADDRESS', 'shipping_address');
define('TBL_STATES', 'states');
define('TBL_SUBSCRIBED_USERS', 'subscribed_users');
define('TBL_ADMINCONFIG', 'tbladminconfig');
define('TBL_APPTOKENS', 'tblapptokens');
define('TBL_TRANSACTIONS', 'transactions');
define('TBL_USERS', 'users');
define('TBL_USER_IMAGES', 'user_images');
define('TBL_INVOICES', 'tblinvoices');

/**
 * Constants for Images
 */
//define('HOTEL_IMAGES', 'uploads/');
define('HOTEL_IMAGES', 'uploads/hotel_images/');
define('PROFILE_IMAGES', 'uploads/profile_images/');
define('BUSINESS_LOGO_IMAGES', 'uploads/business_logo/');
define('BUSINESS_PROMO_IMAGES', 'uploads/business_promo_images/');
define('ICP_IMAGES', 'uploads/icp_images/');
define('ICP_BLUR_IMAGES', 'uploads/icp_blur_images/');
define('ICP_SMALL_IMAGES', 'uploads/icp_small_images/');
define('ICP_LOGO', 'uploads/icp_logo/');
define('ICP_PREVIEW_IMAGES', 'uploads/icp_preview_images/');
define('ICP_FRAMES', 'uploads/icp_frames/');
define('ICP_PHYSICAL_PRODUCT_IMAGES', 'uploads/icp_physical_product_images/');
define('USER_IMAGES', 'uploads/user_images/');
define('CROP_FACES', 'uploads/crop_faces/');
//define('USER_IMAGE_SITE_PATH', 'http://clientapp.narola.online/pg/FaceTag/Images/selfiPic/');
define('USER_IMAGE_SITE_PATH', 'Mobile/Images/selfiPic/');

/**
 * Constants for Email 
 */
define('EMAIL_FROM', 'info@facetag.com.au');
define('EMAIL_FROM_NAME', 'facetag');

/**
 * Google Captch Site Key and Secret Constatns
 */
//define('GOOGLE_SITE_KEY', '6LcbSwoUAAAAAGwOF1kSPJuMoXiS93XMOYnRjzI4');
//define('GOOGLE_SECRET_KEY', '6LcbSwoUAAAAALW-xGxnulbTqPbthDtXHomkEp27');

/*@anp : Google Captch Site Key and Secret Constatns*/
define('GOOGLE_SITE_KEY', '6Lc0aSMUAAAAAM5ZJWSSC3u3PqZQlwiRjbPzEm0w');
define('GOOGLE_SECRET_KEY', '6Lc0aSMUAAAAAGjHF7ViRUNmpguWt6VFygzBq47k');

/**
 * Facerecognition access token
 */
//define('FACE_RECOGNITION_TOKEN', '7fd6d7c1bdcd3a58455810d0ff76b2a1');
define('FACE_RECOGNITION_TOKEN', 'Ug2-NOC3O86aadLQzbOBLvYFt2Rymyay');

/**
 * API key for push notification 
 */
define('ANDROIDAPIKEY', 'AIzaSyBYLa5QEsiJRnCfRUejwOX7oPKnGhl_JFw');

/**
 * FIREBASE FCM URL
 */
define("FIREBASE_API_KEY", "AIzaSyCUSBFW6goHzhp_5OkaWyyySFoTbEhrE28");
define("FIREBASE_FCM_URL", "https://fcm.googleapis.com/fcm/send");
define("SUCCESS", "success");
define("FAILED", "failed");

