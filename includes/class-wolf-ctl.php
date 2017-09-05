<?php

/**
 * File system controler
 */
class WP_Output_Log_File_Controler {
	private $options;
	public function __construct() {
		$this->options = get_option( 'wolf_options' );
	}

	/**
	 * Get real log file full path
	 */
	public function get_logfile_path() {
		$filename = $this->get_log_file_name();
		$log_file_dir = $this->get_log_file_dir();
		return sprintf( "%s/%s", $log_file_dir, $filename );
	}

	public function get_log_file_name() {
		$filename = $this->get_trans_dateformat_filename( $this->options[ 'filename' ] );
		return $filename;
	}

	public function get_trans_dateformat_filename($input_filename) {
		$out = sanitize_file_name($input_filename);
		if (strpos($input_filename, '%')) {
			$search = array( '%Y', '%m', '%d', '%h' );
			$replace = array( date_i18n('Y'), date_i18n('m'), date_i18n('d'), date_i18n('H') );
			$out = sanitize_file_name( str_replace( $search, $replace, $input_filename ) );
		}
		return $out;
	}


	public function get_log_file_dir_real() {
		$log_file_dir = '';
		if (!isset($this->options['log_file_dir'])) {
			$this->options['log_file_dir'] = $this->get_default_log_file_dir();
		}
		$dir = trailingslashit( WP_CONTENT_DIR ) . trailingslashit( $this->options['log_file_dir'] );
		
		if ( ! file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		
		$dir = realpath( $dir );
		return $dir;
	}

	public function get_log_file_dir() {
		if ( isset( $this->options[ 'log_file_dir' ]) ) {
			$dir = trailingslashit( WP_CONTENT_DIR ) . trailingslashit( $this->options[ 'log_file_dir' ] );
			return $this->get_abs_dir( $dir );
		}
	}
	
	public function get_abs_dir( $dir ) {
		$fn = preg_split( "|/|", $dir );
		$stack = array();
		foreach ( $fn as $path ) {
			if ( $path === ".." ) {
				if ( count( $stack ))
					array_pop($stack);
			} else if ($path === ".") {
			} else if ($path === "") {
			} else {
				array_push($stack, $path);
			}
		}
		
		$r = trailingslashit( '/' . implode( "/", $stack ) ) ;
		return $r;
	}

	/**
	 * check real logfile directory writable
	 */
	public function is_log_dir_wriable() {
		$dir = $this->get_log_file_dir();
		if ( is_writable( $dir ) ) {
			return true;
		} else {
			if (! file_exists( $dir ) ) {
				wp_mkdir_p( $dir );
			}
			if ( realpath($dir) ) {
				if(is_writable($dir)) {
					return true;
				}
			}
		}
		return false;
	}
	
	/** 
	 * Before set log file directory
	 */
	public function get_default_log_file_dir() {
		$upload_dir = wp_upload_dir( null, false, true );
		$default_folder = sprintf( "%s/%s/",
								   $upload_dir['basedir'],
								   'logs-' . substr( md5( uniqid() ), 0, 4 ) );
		
		return str_replace( trailingslashit( WP_CONTENT_DIR ), '', trailingslashit( $default_folder ) );
		
	}

	/**
	 * Befor set log file name
	 */
	public function get_default_log_file_name() {
		return 'my-log-%Y%m%d.log';
	}


	/**
	 * Befor set timerecord
	 */
	public function get_default_timerecord() {
		return 'Y-m-d H:i:s:';
	}


	/**
	 * check useable log file suffix on this plugin 
	 */
	public function is_validate_suffix($filename) {
		global $wp_output_log_file_use_suffix;
		$file_name_info = pathinfo($filename);
		if ( isset( $file_name_info['extension'] )
			 &&in_array($file_name_info['extension'], $wp_output_log_file_use_suffix, true)) {
			return $filename;
		}
		return false;
	}

	/**
	 * return  filename list 
	 */
	public function get_file_list() {
		$file_lists = array();
		
		$log_file_dir = $this->get_log_file_dir();
		if ( is_dir( $log_file_dir ) ) {

			$files = scandir( $log_file_dir );

			foreach ( $files as $f ){
				if( $this->is_validate_suffix( $f ) ) {
					$file_lists[] = $f;
				}
			}
		}
		return $file_lists;
	}

	/**
	 * set protect files for security.
	 */
	public function set_protect_files() {
		$log_file_dir = $this->get_log_file_dir();
		
		if( $this->is_protectable_path( $log_file_dir )  && $this->is_log_dir_wriable() ) {
			$index_file = 'index.php';
			if ( ! file_exists( $log_file_dir . $index_file ) ) {
				$file_content = '<?php 
// Silence is golden.';
				file_put_contents( $log_file_dir . $index_file, $file_content );
			}

			$htaccess_file = '.htaccess';
			if ( ! file_exists( $log_file_dir . $htaccess_file ) ) {
				$file_content = 'Order deny,allow
Deny from all';
				file_put_contents( $log_file_dir . $htaccess_file, $file_content );
			}
			return true;
		} 
		return false;
	}

	/**
	 * check wordpress system directory
	 */
	public function is_valid_log_path( $log_path ) {
		//  no document root
		if ( $log_path === ABSPATH) {
			return false;
		}

		// no plugins
		if ( strstr( $log_path, WP_PLUGIN_DIR ) ) {
			return false;
		}

		// no wp-admin
		if ( strstr( $log_path, 'wp-admin' ) ) {
			return false;
		}

		// no wp-include
		if ( strstr( $log_path, ABSPATH . WPINC ) ) {
			return false;
		}
		
		
		return true;
	}

	public function is_protectable_path( $log_path ) {
		if ( ! $this->is_valid_log_path( $log_path ) )
			return false;

		if ( trailingslashit( $log_path ) === trailingslashit( WP_CONTENT_DIR ) ) {
			return false;
		}
		return true;
	}
	
}
