<?php
define("OSDWPUVERSION", "1.4.7");

class OS_Disable_WordPress_Updates {
	private $__pluginsFiles;
	private $__themeFiles;

	function __construct() {
		$this->__pluginsFiles = array();
		$this->__themeFiles = array();
		
		add_action( 'admin_init', array(&$this, 'admin_init') );

		if( !function_exists( 'get_plugins' ) ) require_once ABSPATH . 'wp-admin/includes/plugin.php';
		
		if( count( get_plugins() ) > 0 ) foreach( get_plugins() as $file => $pl ) $this->__pluginsFiles[$file] = $pl['Version'];
		if( count( wp_get_themes() ) > 0 ) foreach( wp_get_themes() as $theme ) $this->__themeFiles[$theme->get_stylesheet()] = $theme->get('Version');

		/*
		 * Disable Theme Updates
		 * 2.8 to 3.0
		 */
		add_filter( 'pre_transient_update_themes', array($this, 'last_checked_themes') );
		/*
		 * 3.0
		 */
		add_filter( 'pre_site_transient_update_themes', array($this, 'last_checked_themes') );


		/*
		 * Disable Plugin Updates
		 * 2.8 to 3.0
		 */
		add_action( 'pre_transient_update_plugins', array(&$this, 'last_checked_plugins') );
		/*
		 * 3.0
		 */
		add_filter( 'pre_site_transient_update_plugins', array($this, 'last_checked_plugins') );


		/*
		 * Disable Core Updates
		 * 2.8 to 3.0
		 */
		add_filter( 'pre_transient_update_core', array($this, 'last_checked_core') );
		/*
		 * 3.0
		 */
		add_filter( 'pre_site_transient_update_core', array($this, 'last_checked_core') );


		/*
		 * Disable All Automatic Updates
		 */
		add_filter( 'auto_update_translation', '__return_false' );
		add_filter( 'automatic_updater_disabled', '__return_true' );
		add_filter( 'allow_minor_auto_core_updates', '__return_false' );
		add_filter( 'allow_major_auto_core_updates', '__return_false' );
		add_filter( 'allow_dev_auto_core_updates', '__return_false' );
		add_filter( 'auto_update_core', '__return_false' );
		add_filter( 'wp_auto_update_core', '__return_false' );
		add_filter( 'auto_core_update_send_email', '__return_false' );
		add_filter( 'send_core_update_notification_email', '__return_false' );
		add_filter( 'auto_update_plugin', '__return_false' );
		add_filter( 'auto_update_theme', '__return_false' );
		add_filter( 'automatic_updates_send_debug_email', '__return_false' );
		add_filter( 'automatic_updates_is_vcs_checkout', '__return_true' );


		add_filter( 'automatic_updates_send_debug_email ', '__return_false', 1 );
		if( !defined( 'AUTOMATIC_UPDATER_DISABLED' ) ) define( 'AUTOMATIC_UPDATER_DISABLED', true );
		if( !defined( 'WP_AUTO_UPDATE_CORE') ) define( 'WP_AUTO_UPDATE_CORE', false );

		add_filter( 'pre_http_request', array($this, 'block_request'), 10, 3 );
	}


	function OS_Disable_WordPress_Updates() {
		$this->__construct();
	}

	function admin_init() {
		if ( !function_exists("remove_action") ) return;
		
		
		/*
		 * Hide maintenance and update nag
		 */
		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'network_admin_notices', 'update_nag', 3 );
		remove_action( 'admin_notices', 'maintenance_nag' );
		remove_action( 'network_admin_notices', 'maintenance_nag' );
		

		/*
		 * Disable Theme Updates
		 * 2.8 to 3.0
		 */
		remove_action( 'load-themes.php', 'wp_update_themes' );
		remove_action( 'load-update.php', 'wp_update_themes' );
		remove_action( 'admin_init', '_maybe_update_themes' );
		remove_action( 'wp_update_themes', 'wp_update_themes' );
		wp_clear_scheduled_hook( 'wp_update_themes' );


		/*
		 * 3.0
		 */
		remove_action( 'load-update-core.php', 'wp_update_themes' );
		wp_clear_scheduled_hook( 'wp_update_themes' );


		/*
		 * Disable Plugin Updates
		 * 2.8 to 3.0
		 */
		remove_action( 'load-plugins.php', 'wp_update_plugins' );
		remove_action( 'load-update.php', 'wp_update_plugins' );
		remove_action( 'admin_init', '_maybe_update_plugins' );
		remove_action( 'wp_update_plugins', 'wp_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );

		/*
		 * 3.0
		 */
		remove_action( 'load-update-core.php', 'wp_update_plugins' );
		wp_clear_scheduled_hook( 'wp_update_plugins' );


		/*
		 * Disable Core Updates
		 * 2.8 to 3.0
		 */
		add_action( 'init', create_function( '', 'remove_action( \'init\', \'wp_version_check\' );' ), 2 );
		add_filter( 'pre_option_update_core', '__return_null' );

		remove_action( 'wp_version_check', 'wp_version_check' );
		remove_action( 'admin_init', '_maybe_update_core' );
		wp_clear_scheduled_hook( 'wp_version_check' );


		/*
		 * 3.0
		 */
		wp_clear_scheduled_hook( 'wp_version_check' );


		/*
		 * 3.7+
		 */
		remove_action( 'wp_maybe_auto_update', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_maybe_auto_update' );
		remove_action( 'admin_init', 'wp_auto_update_core' );
		wp_clear_scheduled_hook( 'wp_maybe_auto_update' );
	}


	public function block_request($pre, $args, $url) {
		/* Empty url */
		if( empty( $url ) ) {
			return $pre;
		}

		/* Invalid host */
		if( !$host = parse_url($url, PHP_URL_HOST) ) {
			return $pre;
		}

		$url_data = parse_url( $url );

		/* block request */
		if( false !== stripos( $host, 'api.wordpress.org' ) && (false !== stripos( $url_data['path'], 'update-check' ) || false !== stripos( $url_data['path'], 'browse-happy' )) ) {
			return true;
		}

		return $pre;
	}


	public function last_checked_core() {
		global $wp_version;
		
		return (object) array(
			'last_checked'		=> time(),
			'updates'			=> array(),
			'version_checked'	=> $wp_version
		);
	}


	public function last_checked_themes() {
		global $wp_version;

		return (object) array(
			'last_checked'		=> time(),
			'updates'			=> array(),
			'version_checked'	=> $wp_version,
			'checked'			=> $this->__themeFiles
		);
	}


	public function last_checked_plugins() {
		global $wp_version;

		return (object) array(
			'last_checked'		=> time(),
			'updates'			=> array(),
			'version_checked'	=> $wp_version,
			'checked'			=> $this->__pluginsFiles
		);
	}
}

if ( class_exists('OS_Disable_WordPress_Updates') ) {
	$OS_Disable_WordPress_Updates = new OS_Disable_WordPress_Updates();
}
?>