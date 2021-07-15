<?php

class OfflinePrecache {
	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	private static function init_hooks() {
		add_rewrite_endpoint( 'serviceworker.js', EP_PERMALINK );
		add_action( 'template_redirect', array('OfflinePrecache', 'custom_links_redirect') );
		add_action( 'wp_head', array('OfflinePrecache', 'register_service_worker') );
		self::$initiated = true;
	}
	public static function custom_links_redirect(){
	    global $wp_query;
	    if($wp_query->query_vars['pagename'] == "serviceworker-js" && $wp_query->query_vars['page_id'] == 0){
		    include plugin_dir_path( __FILE__ ) . '_inc/sw.php';
		    exit;
        }
	    return;
    }
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], OFFLINE_PRECACHE__MINIMUM_WP_VERSION, '<' ) ) {
			load_plugin_textdomain( 'offline-precache' );

			$message = '<strong>' . sprintf( esc_html__( 'Offline Pre-Cache %s requires WordPress %s or higher.', 'offline-precache' ), OFFLINE_PRECACHE_VERSION, OFFLINE_PRECACHE__MINIMUM_WP_VERSION ) . '</strong> ' . sprintf( __( 'Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Offline Pre-Cache plugin</a>.', 'offline-precache' ), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/offline-precache/download/' );

			OfflinePrecache::bail_on_activation( $message );
		} elseif ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php' ) ) {
			add_option( 'Activated_Offline_Precache', true );
			$pageId = self::create_offline_page();
			add_option('offline_precache_page_id', $pageId);
		}
	}
	private static function bail_on_activation( $message, $deactivate = true ) {
		?>
        <!doctype html>
        <html>
        <head>
            <meta charset="<?php bloginfo( 'charset' ); ?>"/>
            <style>
                * {
                    text-align: center;
                    margin: 0;
                    padding: 0;
                    font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;
                }

                p {
                    margin-top: 1em;
                    font-size: 18px;
                }
            </style>
        </head>
        <body>
        <p><?php echo esc_html( $message ); ?></p>
        </body>
        </html>
		<?php
		if ( $deactivate ) {
			$plugins        = get_option( 'active_plugins' );
			$preCachePlugin = plugin_basename( OFFLINE_PRECACHE__PLUGIN_DIR . 'offline-precache.php' );
			$update         = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $preCachePlugin ) {
					$plugins[ $i ] = false;
					$update        = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}

	public static function view( $name, array $args = array() ) {
		$args = apply_filters( 'offline_precache_view_arguments', $args, $name );

		foreach ( $args as $key => $val ) {
			$$key = $val;
		}

		load_plugin_textdomain( 'offline-precache' );

		$file = OFFLINE_PRECACHE__PLUGIN_DIR . 'views/' . $name . '.php';

		include( $file );
	}
	private static function create_offline_page() {
		$page_details = array(
			'post_title'   => 'Offline Content',
			'post_content' => "<p>It appears you don't currently have a network connection. However, you can still view pages that you have visited before and continue browsing the catalog as it was when you last saw it. Keep in mind that prices and offers may be out of date - we will show you the updated catalog once you are back online.</p>
<p>Some actions such as managing your user account, adding products to your basket and checking out are unavailable while offline. If you wish to place an order, please try again once your network connection is restored.</p>",
			'post_status'  => 'publish',
			'comment_status' => 'close',
			'ping_status'    => 'close',
			'post_name'      => 'offline-precache',
			'post_author'  => 1,
			'post_type'    => 'page'
		);
		return wp_insert_post( $page_details );
	}
	public function register_service_worker(){
		echo "<script type=\"text/javascript\">
            (() => {
                    if ('serviceWorker' in navigator) {
                        navigator.serviceWorker
                            .register('". get_bloginfo('url') . "/serviceworker.js"  ."')
        .catch(err => {
        console.log(\"Service worker registration failed: \", err);
        });
        }
        })();
        </script>";
	}
}