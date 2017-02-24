<?php
/*
Plugin Name: WP Output Log File
Description: Management of wordpress output log files
Version: 1.0.0
Author: Reiji sato
Author URI: http://www.sofplant.com
Text Domain: wp-output-log-file
*/

require_once('includes/class-wolf-admin.php'); 
require_once('includes/class-wolf-output.php'); 
require_once('includes/class-wolf-ctl.php'); 

/**
 *  log file suffix is .log, .txt, .csv only.
 */
define('WOLF_USE_SUFFIX', array('log', 'csv', 'txt'));


if ( ! function_exists( 'wo_log' ) ) :

/**
 * @message log output message 
 */
function wo_log($message) {
	$wolf = new Wolf_Output();
	$wolf->logoutput($message);
}

endif;
