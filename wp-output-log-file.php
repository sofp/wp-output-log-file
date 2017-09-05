<?php
/*
Plugin Name: WP Output Log File
Version: 1.1.0
Description: Manage log files plugin.
Author: Reiji sato
Author URI: http://www.sofplant.com
Plugin URI: https://github.com/sofp/wp-output-log-file
Text Domain: wp-output-log-file
Domain Path: /languages/
License: GPL v2 or later
*/

if ( ! function_exists( 'wo_log' ) ) :

require_once( 'includes/class-wolf-admin.php' );
require_once( 'includes/class-wolf-output.php' );
require_once( 'includes/class-wolf-ctl.php' );
/**
 *  log file suffix is .log, .txt, .csv only.
 */
$wp_output_log_file_use_suffix =  array( 'log', 'csv', 'txt' );

/**
 * @message log output message 
 */
function wo_log( $message ) {
	$wolf = new Wolf_Output();
	$wolf->logoutput( $message );
}

endif;
