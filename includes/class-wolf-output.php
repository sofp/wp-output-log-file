<?php

class Wolf_Output {
	private $options;
	
	public function __construct(){
		$this->options = get_option( 'wolf_options' );
	}

	public function logoutput( $message ) {

		if( ! isset( $this->options[ 'active' ] ) || intval( $this->options['active'] ) !== 1 ) {
			return;
		}

		if ( ! is_string( $message ) )
			return;

		$file_ctl = new WP_Output_Log_File_Controler();
		$file_path = $file_ctl->get_logfile_path();
		if ( ! $file_ctl->is_validate_suffix( $file_path ) ) {
			return;
		}
		
		$out_log = sanitize_text_field( $message ) . "\n";
		if (isset( $this->options[ 'timerecord' ] ) && trim( $this->options[ 'timerecord' ] ) !== '') {
			$out_log = sprintf( "%s%s\n",
								date_i18n( $this->options[ 'timerecord' ] ),
							   $message );
		}

		error_log( $out_log, 3, $file_path );
	}
}