<?php
/*
* Plugin Name: GravityView - Enable Remote Debugging
* Description: Allow GravityView Support to identify the issue. <strong>De-activate once GravityView Support is done!</strong>
* Version: 1.0
* Author: Katz Web Services, Inc.
* Author URI: https://gravityview.co
*/

class GravityView_Remote_Debug {

	function __construct() {

		add_action('plugins_loaded', array( $this, 'init' ) );

	}

	/**
	 * Check whether the request is coming from localhost or KWS computers.
	 *
	 * Also allows sending support key as a `key` URL arg. If the value is the support key, assume it's KWS.
	 *
	 * @return boolean Yes: is KWS; No: is not KWS
	 */
	public function is_kws() {

		$allowed_ips = array(
			"127.0.0.1",
			'::1:',
			'10.10.10.10',
			'24.9.165.93',
			'67.176.16.53',
			'95.136.84.75', // Luis
			'89.115.5.146', // Luis
		);

		return isset( $_SERVER["REMOTE_ADDR"] ) && in_array( $_SERVER["REMOTE_ADDR"], $allowed_ips );
	}

	function init() {

		if( !isset( $_GET['debug'] ) ) {
			return;
		}

		if( !$this->is_kws() && false === current_user_can( 'manage_options' ) ) {
			return;
		}

		// Turn on error reporting
		error_reporting(E_ALL ^ E_STRICT);

		@ini_set('display_errors', '1');

		if( !defined('WP_DEBUG') ) {
			define('WP_DEBUG', true);
		}

		if( !defined('DEBUG_LOG') ) {
			define('DEBUG_LOG', true);
		}

		switch( $_GET['debug'] ) {
			case 'plugins':
				foreach (get_option("active_plugins") as $plugin) {
					echo '<pre>'; print_r( get_plugin_data(WP_CONTENT_DIR . "/plugins/$plugin") ); echo '</pre>';
				}
				exit();
				break;
			case 'php':
				phpinfo();
				exit();
				break;
		}

	}

}

new GravityView_Remote_Debug;
