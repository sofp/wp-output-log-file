<?php

if ( ! class_exists('WP_Output_Log_File_Admin') ) :


/**
 * WP OutputLogFile Admin Mange Class
 */
class WP_Output_Log_File_Admin {

	const PAGE_ID = 'wp-output-log-file-page-id';
	
	private $options;			// setting tab option
	private $options2;			// manage tab option
	
	private $active_tab;

	private $file_controler;
	
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	public function add_plugin_page() {
		$load_hook = add_options_page( 'WP Output Log File',
									   'WP Output Log File',
									   'manage_options',
									   self::PAGE_ID,
									   array( $this, 'admin_manage_page' ) );
		add_action( 'load-' . $load_hook, array($this, 'load') ); // for log download
	}
	
	public function admin_manage_page()  {
		$this->file_controler = new WP_Output_Log_File_Controler();
		// Set class property
		$this->options = get_option( 'wolf_options' );

		$this->active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field( $_GET[ 'tab' ] ) : 'tab_one';
		
		?>
		<div class="wrap">
		<h1>WP Output Log File</h1>
		<h2 class="nav-tab-wrapper">
		<a href="?page=<?php echo self::PAGE_ID; ?>&tab=tab_one" class="nav-tab <?php echo $this->active_tab == 'tab_one' ? 'nav-tab-active' : ''; ?>">Settings</a>
		<a href="?page=<?php echo self::PAGE_ID; ?>&tab=tab_two" class="nav-tab <?php echo $this->active_tab == 'tab_two' ? 'nav-tab-active' : ''; ?>">Manage</a>
		</h2>

		<form method="post" action="options.php">
		<?php
		// This prints out all hidden setting fields
		if( 'tab_one' ===  (string)$this->active_tab ) { 
			settings_fields( 'wolf_options_group' );
			do_settings_sections( 'wolf_options' );

			submit_button();
		} else {
			settings_fields( 'wolf_options2_group' );
			do_settings_sections( 'wolf_options2');
			
			$this->loglist();

			submit_button( 'Delete', 'delete button-primary' );
		}
		?>
		</form>
		</div>
<?php
	}

	public function page_init() {
		register_setting(
			'wolf_options_group', // Option group
			'wolf_options',
			array( $this, 'sanitize_and_check' )
		);

		add_settings_section(
			'wolf_setting_section', // section ID
			'Settings', // Title
			null,
			'wolf_options'
		);  
		add_settings_field( 'active', "Output Active",
							array( $this,'active_callback' ),
							'wolf_options',
							'wolf_setting_section' );


		add_settings_field('log_file_dir',
						   'File Directory',
						   array( $this, 'log_file_dir_callback'),
						   'wolf_options',
						   'wolf_setting_section');
		
		add_settings_field('filename',
						   'File Name',
						   array( $this, 'filename_callback'),
						   'wolf_options',
						   'wolf_setting_section');

		add_settings_field('timerecord',
						   'Time Record Format',
						   array( $this, 'timerecord_callback'),
						   'wolf_options',
						   'wolf_setting_section');

		add_settings_field('access_protect',
						   'Access Protect',
						   array( $this, 'access_protect_callback'),
						   'wolf_options',
						   'wolf_setting_section');
		
		register_setting(
			'wolf_options2_group',
			'wolf_options2',
			array( $this, 'logfiles_remove_action')
		);
		add_settings_section(
			'wolf_setting_section2',
			'Manage', // Title
			null,
			'wolf_options2'
		);
	}

	
	public function sanitize_and_check( $input ) {
		$new_input = array();

		$new_input[ 'active' ] = isset( $input[ 'active' ] ) ? intval( $input[ 'active' ] ) : 0;
		
		if( isset( $input['filename'] ) )
			$new_input['filename'] = sanitize_text_field( $input[ 'filename' ] );

		if ( isset( $input[ 'log_file_dir' ] ) ) {
			$dir = sanitize_text_field( trim($input[ 'log_file_dir' ] ) );
			$new_input['log_file_dir'] = $dir !== '' ? trailingslashit( $dir ) : $dir;
		}

		if ( isset( $input[ 'timerecord' ] ) ) {
			$new_input[ 'timerecord' ] = sanitize_text_field( $input[ 'timerecord' ] );
		}
		$new_input[ 'access_protect' ] = isset( $input[ 'access_protect' ] ) ? intval( $input[ 'access_protect' ] ) : 0;

		
		return $new_input;
	}

	public function active_callback() {
		printf( '<input type="checkbox" name="wolf_options[active]" value="1" %s >',
				checked( 1, isset($this->options['active']) ? intval( $this->options[ 'active' ] ) : 0, false ) );
	}

	/**
	 * a log file Directory setting from
	 */
	public function log_file_dir_callback() {
		
		$log_dir_value = '';
		
		if (isset( $this->options['log_file_dir'] ) && "" !== trim( $this->options['log_file_dir'] ) ) {
			$log_dir_value = $this->options['log_file_dir'];
		} else {
			$log_dir_value = $this->file_controler->get_default_log_file_dir();
		}

		$base_dir = trailingslashit( str_replace( ABSPATH, '', WP_CONTENT_DIR ) );
		printf('%s<input type="text" class="regular-text" name="wolf_options[log_file_dir]" value="%s">', $base_dir, $log_dir_value);
		if (isset( $this->options['log_file_dir'] )) {
			// print current full path directory
			if ( $this->file_controler->is_log_dir_wriable() ) {
				printf( "<p>OK:<code>%s</code>: is writable</p>", $this->file_controler->get_log_file_dir() );
			} else {
				printf( '<p class="error-message">NG:<code>%s</code>: is not writable</p>', $this->file_controler->get_log_file_dir() );
			}
		}
	}

	/**
	 * file name input form
	 */
	public function filename_callback() {
		$filename = $this->file_controler->get_default_log_file_name();

		if ( isset( $this->options['filename'] ) && trim(  '' !== $this->options['filename'] ) ) {
			$filename = esc_attr( $this->options['filename'] );
		}
		printf( '<input type="text" class="regular-text" name="wolf_options[filename]" value="%s">', $filename );
		printf( "<p>Output File:<code>%s</code></p>", $this->file_controler->get_trans_dateformat_filename( $filename ) );
	}

	/**
	 * Timerecord format input form
	 */
	public function timerecord_callback() {
		$timerecord = $this->file_controler->get_default_timerecord();
		
		printf( '<input type="text" class="regular-text" name="wolf_options[timerecord]" value="%s" placeholder="ex. Y-m-d H:i:s">',
				isset( $this->options['timerecord'] ) ? esc_attr( $this->options['timerecord']) : $timerecord );

		if ( isset( $this->options['timerecord'] ) && trim( $this->options[ 'timerecord' ] ) !== '' ) {
			$head_time_record = date_i18n( trim( $this->options[ 'timerecord' ] ) );
			printf( '<p>beginning of a line: <code>%s</code></p>', $head_time_record );
		}
	}


	public function access_protect_callback() {
		printf( '<input type="checkbox" name="wolf_options[access_protect]" value="1" %s > ',
				checked( 1, isset( $this->options[ 'access_protect' ]) ? intval( $this->options[ 'access_protect' ] ) : 0, false ) );
		if ( isset( $this->options[ 'access_protect' ] ) && intval($this->options[ 'access_protect' ]) === 1 ) {
			if ( $this->file_controler->set_protect_files() ) {
				printf( '<p>Setted: <code>%s</code> and <code>%s</code></p>', 'index.php', '.htaccess' );
			} else {
				printf( '<p>Not Set: <code>%s</code></p>', $this->file_controler->get_log_file_dir() );
			}
		}
	}
	
	/**
	 * Display log file list
	 */
	public function loglist() {

		$file_list = $this->file_controler->get_file_list();

		foreach ( $file_list as $file_name ) {
			$download_url = sprintf( "?page=%s&action=download&filename=%s", self::PAGE_ID, $file_name );
			$download_url = wp_nonce_url( $download_url, 'wolf-log-download' );
			
			echo '<li>';
			printf( '<label><input type="checkbox" name="wolf_options2[%s]" value="%s" > %s </label>',
					$file_name, $file_name, $file_name );
			printf( '&nbsp;&nbsp;[<a href="%s">download</a>]', $download_url );
			echo '</li>';
		}
	}

	/**
	 * log files remove action
	 */
	public function logfiles_remove_action( $args ) {
		$file_controler = new WP_Output_Log_File_Controler();
		$logfiles_dir = $file_controler->get_log_file_dir();

		if ( isset( $args ) && is_array( $args ) ) {
			foreach( $args as $fname ) {
				$file_path = $logfiles_dir  . '/'  . $fname;
				if ( file_exists( $file_path ) ) {
					unlink( $file_path );
				}
			}
		}
	}

	/**
	 * download log file
	 */
	public function load() {
		$file_controler = new WP_Output_Log_File_Controler();
		if ( isset( $_GET['action'] )
			&& trim( sanitize_text_field( $_GET['action'] ) ) === 'download'
			&& check_admin_referer( 'wolf-log-download' ) ) {
			
			$file_name = trim( sanitize_text_field( $_GET['filename'] ) );
			$logfile_path = $file_controler->get_log_file_dir() . '/' . $file_name;

			if ( file_exists( $logfile_path ) ) {
				header( "Content-type: text/plain" );
				header( "Content-Disposition: attachment; filename=$file_name" );
				@readfile( $logfile_path );
				die();
			}
			
		}
	}
	
}

if( is_admin() )
	new WP_Output_Log_File_Admin();

endif;

