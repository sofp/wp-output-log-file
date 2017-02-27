<?php

require_once( '/home/sofplant/www/c03/htdocs/wp-load.php');
require_once('class-wolf-ctl.php');

test7();

echo "\n";

function test1() {
	
	$fc = new WP_Output_Log_File_Controler();

// echo $fc->get_logfile_path();

// echo "\n";

	$test = 'test-%year%-%monthnum%-%day%.log';
	$test = 'test-%year%%monthnum%%day%.log';
	$test = 'test-%montm%%day%_%%%year%.log';

// echo $fc->get_date_filename($test) . "\n";

	$msg = '"test var "';
	$msg = addslashes($msg);
	echo sanitize_text_field($msg) . "\n";
}


function test2() {
	$default_folder = 'logs-' . substr(md5(uniqid()), 0, 4);
	echo $default_folder . "\n";
}

function test3() {
	echo sanitize_text_field('abc-%Y.log');
}

function test4() {
	// print_r(WOLF_USE_SUFFIX);
	$filename = 'a.x';
	$fc = new WP_Output_Log_File_Controler();
	if($fc->is_validate_suffix($filename)) {
		echo "ok";
	} else {
		echo "ng";
	}
}

function test5() {
	$fc = new WP_Output_Log_File_Controler();
	$file_list = $fc->get_file_list();
	print_r($file_list);
}

function test6() {
	// $dir = '/home/sofplant/www/c03/htdocs/wp-content/plugins/wp-output-log-file/classes';
	// $dir = '/home/sofplant/www/c03/htdocs/wp-content/';
	//$dir = '/home/sofplant/www/c03/htdocs/wp-admin/';
	// $dir = '/home/sofplant/www/c03/htdocs/';
	// $dir = '/home/sofplant/www/c03/logs/';
	// $dir = '/home/sofplant/www/c03/htdocs/wp-content/';
	$dir = '/home/sofplant/www/c03/htdocs/wp-content/plugins/wp-output-log-file/';
	$fc = new WP_Output_Log_File_Controler();
	if ( $fc->is_valid_log_path($dir) ) {
		echo "ok:$dir";
	} else {
		echo "ng:$dir";
	}
	
}

function test7() {
	$dir = '/home/sofplant/www/c03/htdocs/wp-content/plugins/wp-output-log-file/';
	$fc = new WP_Output_Log_File_Controler();
	$fc->set_protect_files();
}