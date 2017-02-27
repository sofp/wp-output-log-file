=== WP Output Log File ===
Plugin Name: WP Output Log File
Contributors: sofplant
Tags: log, developer, 
Donate link: https://www.sofplant.com/

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

You can manage the output log file, specify output directory and file name, 
download and delete files regardless of WP_DEBUG mode.

== Description ==

You can manage the output log file regardless of WP_DEBUG mode,
and specify output directory and file name, download and delete files.

You can specify the date format as the file name, you can split the log every day and every month.


== Installation ==

Just install from your WordPress "Plugins > Add New" screen and all will be well. Manual installation is very straightforward as well:

1. Upload the zip file and unzip it in the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to `Settings > WP Output Log File` and activ options you want.

= Example =

You can write customizing wordpress code ex. using theme functions.php file.

if ( function_exists( 'wo_log' ) ) { 
   wo_log( "Your message" );
}



== Screenshots ==

== Changelog ==

