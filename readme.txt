=== WP Output Log File ===
Plugin Name: WP Output Log File
Contributors: sofplant
Tags: debug, debugging, log, developer, error
Requires at least: 3.6
Tested up to: 4.8.1
Stable tag: 1.1.0
Donate link: https://www.sofplant.com/

License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin can manage the output log file, specify output directory and file name, 
download and delete files regardless of WP_DEBUG mode.

== Description ==

Control the output log file regardless of WP_DEBUG mode.
Specify output directory and file name, download and delete files.

And specify the date format as the file name, you can split the log every day and every month.


== Installation ==

Just install from your WordPress "Plugins > Add New" screen and all will be well. Manual installation is very straightforward as well:

1. Upload the zip file and unzip it in the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to `Settings > WP Output Log File` and activ options you want.

= Example =

You can write customizing wordpress code ex. using theme functions.php file.

if ( function_exists( 'wo_log' ) ) { 
   wo_log( "Your message" ); // string

   wo_log( ['a' => 1, 'b' => 2, 'c' => 3 ] ); // other data type
}



== Screenshots ==

== Changelog ==

= 1.1.0 =
* Change wo_log message paramater can any data type
* Delete test file
* Change write error message color

= 1.0.1 =
* Fixed Compatibility with PHP 5.2 users
* Change Settings menu name to WP Output Log File
* Change readme.txt
