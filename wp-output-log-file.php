<?php
/*
Plugin Name: WP Output Log File
Version: 1.0.0
Description: management of wordpress output log files.
Author: Reiji sato
Author URI: http://www.sofplant.com
Plugin URI: https://github.com/sofp/wp-output-log-file
Text Domain: wp-output-log-file
Domain Path: /languages/
License: GPL v2 or later
*/

require_once( 'includes/class-wolf-admin.php' );
require_once( 'includes/class-wolf-output.php' );
require_once( 'includes/class-wolf-ctl.php' );

/**
 *  log file suffix is .log, .txt, .csv only.
 */
// define( 'WOLF_USE_SUFFIX', array( 'log', 'csv', 'txt' ) );
$wp_output_log_file_use_suffix =  array( 'log', 'csv', 'txt' );

if ( ! function_exists( 'wo_log' ) ) :

/**
 * @message log output message 
 */
function wo_log( $message ) {
	$wolf = new Wolf_Output();
	$wolf->logoutput( $message );
}

endif;
