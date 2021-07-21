<?php

class OfflinePrecacheAdmin {
	const NONCE = 'offline-precache-update-key';
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
		if ( isset( $_POST['action'] ) && $_POST['action'] == 'save_precache_options' ) {
			self::save_options();
		}
	}

	private static function init_hooks() {
		add_action( 'admin_init', array( 'OfflinePrecacheAdmin', 'admin_init' ) );
		add_action( 'admin_menu', array( 'OfflinePrecacheAdmin', 'add_plugin_page' ) );
		add_action( 'admin_enqueue_scripts', array( 'OfflinePrecacheAdmin', 'load_resources' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . 'offline-precache.php' ), array(
			'OfflinePrecacheAdmin',
			'admin_plugin_settings_link'
		) );
		self::$initiated = true;
	}

	public static function admin_init() {
		if ( get_option( 'Activated_Offline_Precache' ) ) {
			delete_option( 'Activated_Offline_Precache' );
			if ( ! headers_sent() ) {
				wp_redirect( add_query_arg( array( 'page' => 'offline-precache-key-config' ), admin_url( 'options-general.php' ) ) );
			}
		}
		load_plugin_textdomain( 'offline-precache' );
	}

	public static function admin_plugin_settings_link( $links ) {
		$settings_link = '<a href="' . esc_url( self::get_page_url() ) . '">' . __( 'Settings', 'offline-precache' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	public static function add_plugin_page() {
		add_options_page(
			__( 'Offline Pre-cache', 'offline-precache' ),
			__( 'Offline Pre-cache', 'offline-precache' ),
			'manage_options',
			'offline-precache-key-config',
			array( 'OfflinePrecacheAdmin', 'display_page' )
		);
	}

	public static function display_page() {
		$custom_strategies = self::get_custom_strategies();
		OfflinePrecache::view( 'config', compact('custom_strategies') );
	}

	public static function load_resources() {
		global $hook_suffix;

		if ( in_array( $hook_suffix, apply_filters( 'offline_precache_admin_page_hook_suffixes', array(
			'settings_page_offline-precache-key-config',
		) ) ) ) {
			wp_register_style( 'offline_precache.css', plugin_dir_url( __FILE__ ) . '_inc/offline_precache.css', array(), OFFLINE_PRECACHE_VERSION );
			wp_enqueue_style( 'offline_precache.css' );

			wp_register_script( 'offline-precache.js', plugin_dir_url( __FILE__ ) . '_inc/js/offline-precache.js', array( 'jquery' ), OFFLINE_PRECACHE_VERSION );
			wp_enqueue_script( 'offline-precache.js' );
		}
	}

	public static function get_page_url(  ) {

		$args = array( 'page' => 'offline-precache-key-config' );
		return add_query_arg( $args, admin_url( 'options-general.php' ) );
	}
	private static function save_options(){
		if ( ! current_user_can( 'manage_options' ) ) {
			die( __( 'Cheatin&#8217; uh?', 'offline-precache' ) );
		}

		if ( !wp_verify_nonce( $_POST['_wpnonce'], self::NONCE ) )
			return false;

		foreach( array( 'offline_precache_enabled', 'offline_precache_enabled_ga' ) as $option ) {
			update_option( $option, isset( $_POST[$option] ) && strtolower($_POST[$option]) == "on" ? '1' : '0' );
		}
		if(!empty($_POST['offline_precache_page_id']) ){
			$page = get_post( (int) sanitize_text_field($_POST['offline_precache_page_id']) );
			if ($page->post_status == 'publish') {
				update_option( "offline_precache_page_id", $page->ID );
			}
		}
		if(!empty($_POST['custom_strategies']) && is_array($_POST['custom_strategies'])){
			$custom_strategies = array_values($_POST['custom_strategies']);
			$sanitized_strategies = [];
			foreach ($custom_strategies as $mainKey => $custom_strategy){
				if(empty($custom_strategy['path'])) continue;
				foreach ($custom_strategy as $key => $value){
					if($key == "path" && trim($value) == "") continue;
					$sanitized_strategies[$mainKey][$key] = sanitize_text_field($value);
				}
			}
			update_option('offline_precache_custom_strategies', serialize($sanitized_strategies));
		}else{
			delete_option('offline_precache_custom_strategies');
		}
		return true;
	}
	public static function get_custom_strategies($withUrl = false)
	{
		$custom_strategies = get_option('offline_precache_custom_strategies');
		if(!is_null($custom_strategies) && is_string($custom_strategies))
			$custom_strategies = unserialize($custom_strategies);

		if(!is_array($custom_strategies))
			$custom_strategies = [];

		$custom_strategies = array_map('esc_attr', $custom_strategies);
		if($withUrl){
			$base_url = get_bloginfo('url');
			array_walk($custom_strategies, function (&$item) use ($base_url) {
				$item["path"] = $base_url . $item["path"];
			});
		}
		return $custom_strategies;
	}
}