<?php
/**
 * @package MCMSSEO\Premium\Classes
 */

/**
 * Class MCMSSEO_Redirect_Settings_Presenter
 */
class MCMSSEO_Redirect_Settings_Presenter extends MCMSSEO_Redirect_Tab_Presenter {

	/**
	 * Extending the view vars with pre settings key
	 *
	 * @return array
	 */
	protected function get_view_vars() {
		return array_merge(
			$this->view_vars,
			array(
				'file_path'     => MCMSSEO_Redirect_File_Util::get_file_path(),
				'redirect_file' => $this->writable_redirect_file(),
			)
		);
	}

	/**
	 * Check if it is possible to write to the files
	 *
	 * @return false|string
	 */
	private function writable_redirect_file() {
		// Get redirect options.
		$redirect_options = MCMSSEO_Redirect_Page::get_options();

		if ( 'on' !== $redirect_options['disable_php_redirect'] ) {
			return false;
		}

		// Do file checks.
		$file_exists = file_exists( MCMSSEO_Redirect_File_Util::get_file_path() );

		if ( MCMSSEO_Utils::is_apache() ) {
			$separate_file = ( 'on' === $redirect_options['separate_file'] );

			if ( $separate_file && $file_exists ) {
				return 'apache_include_file';
			}

			if ( ! $separate_file ) {
				// Everything is as expected.
				if ( is_writable( MCMSSEO_Redirect_Htaccess_Util::get_htaccess_file_path() ) ) {
					return false;
				}
			}

			return 'cannot_write_htaccess';
		}

		if ( MCMSSEO_Utils::is_nginx() ) {
			if ( $file_exists ) {
				return 'nginx_include_file';
			}

			return 'cannot_write_file';
		}
	}
}