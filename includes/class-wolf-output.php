<?php

class Wolf_Output {
	private $options;
	
	public function __construct(){
		$this->options = get_option( 'wolf_options' );
	}

	/**
	 * Output Message
	 * @param 
	 */
	public function logoutput( $message ) {
		$out_log = '';
		if( ! isset( $this->options[ 'active' ] ) || intval( $this->options['active'] ) !== 1 ) {
			return;
		}

		if ( is_string( $message ) ) {
			$out_log = $message;
		} else {
			$out_log = print_r( $message, true );
		}

		$file_ctl = new WP_Output_Log_File_Controler();
		$file_path = $file_ctl->get_logfile_path();
		if ( ! $file_ctl->is_validate_suffix( $file_path ) ) {
			return;
		}
		
		$out_log = sanitize_text_field( $out_log ) . "\n";
		
		// time record
		if (isset( $this->options['timerecord'] ) && trim( $this->options[ 'timerecord' ] ) !== '' ) {
			$out_log = sprintf( "%s%s\n",
								date_i18n( $this->options[ 'timerecord' ] ),
								$out_log );
		}

		error_log( $out_log, 3, $file_path );
	}
}