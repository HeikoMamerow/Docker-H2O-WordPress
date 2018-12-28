<?php

if( !class_exists( "SJH_Model" ) ) {
	class SJH_Model { 
		private static $instance;
		public $header_size_buffer = 0;

		/**
		 *  Initiator
		 */
		public static function get_instance(){
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		function __construct() {
			$this->define_constants();
			$this->actions();
		}

		function actions() {

			if( !is_admin() ) {
				add_action( 'init', array( $this, 'sjh_ob_start' ) );
				add_filter('script_loader_src', array( $this, 'preload_link_header' ), 99, 1);
				add_filter('style_loader_src', array( $this, 'preload_link_header' ), 99, 1);

				if( $this->is_prefetch_headers() ) {
					add_action( 'wp_head', array( $this, 'resource_hints' ), 99, 1);
				}
			}
		}

		function define_constants() {
			
			/**
			 * Cloudflare HTTP 520 error when more than 8k of headers are present.
			 * Limiting plugin's output to 4k should keep those errors away.
			 */
			define('SJH_MAX_HEADER_SIZE', 1024 * 4);
		}

		/**
		 * Determine if the plugin should render its own resource hints, or left it to WordPress.
		 * WordPress natively supports resource hints since 4.6.
		 * Override with'sjh_http2_resource_hints' filter.
		 * @return boolean true if the plugin should render resource hints.
		 */
		function is_prefetch_headers() {
			return apply_filters('sjh_http2_resource_hints', !function_exists( 'wp_resource_hints' ) );
		}

		/**
		 * Render "resource hints" in the <head> section of the page.
		 * These encourage preload/prefetch behavior
		 * HTTP/2 support is lacking fallback.
		 */
		function resource_hints() {
			$resource_types = array('script', 'style');
			array_walk( $resource_types, function( $resource_type ) {
				$resources = $this->get_resources($GLOBALS, $resource_type);
				array_walk( $resources, function( $src ) use ( $resource_type ) {
					printf( '<link rel="preload" href="%s" as="%s">', esc_url($src), esc_html( $resource_type ) );
				});	
			});

		}

		/**
		 * Get resources of a certain type that have been enqueued through the WordPress API.
		 * @param array $globals the $GLOBALS array
		 * @param string $resource_type resource type (script, style)
		 * @return array
		 */
		function get_resources( $globals = null, $resource_type ) {

			$globals = (null === $globals) ? $GLOBALS : $globals;
			$resource_type_key = "sjh_http2_{$resource_type}_urls";
			
			if(!(is_array($globals) && isset($globals[$resource_type_key]))) {
				return array();
			}
			else if(!is_array($globals[$resource_type_key])) {
				return array($globals[$resource_type_key]);
			}
			else {
				return $globals[$resource_type_key];
			}

		}

		/**
		 * Convert an URL to a relative path
		 *
		 * @param string $src URL
		 *
		 * @return string mixed relative path
		 */
		function url_to_relative_path($src) {
		    return '//' === substr($src, 0, 2) ? preg_replace('/^\/\/([^\/]*)\//', '/', $src) : preg_replace('/^http(s)?:\/\/[^\/]*/', '', $src);
		}

		/**
		 * Resources Type
		 *
		 * @param string $current_hook pass current_filter()
		 *
		 * @return string 'style' or 'script'
		 */
		function link_resource_type( $current_hook ) {
			return 'style_loader_src' === $current_hook ? 'style' : 'script';
		}
		/**
		 * Start an output buffer to call header() later.
		 */
		function sjh_ob_start() {
		    ob_start();
		}

		/**
		 * @param string $src URL
		 *
		 * @return void
		 */
		function preload_link_header($src) {

			$header_size = $this->header_size_buffer;
		    
		    if (strpos($src, home_url()) !== false) {

		        $preload_src = apply_filters('sjh_http2_preload_link_src', $src);


		        if ( !empty($preload_src) ) {

		        	$relative_path = $this->url_to_relative_path($preload_src);
		        	$resource_type = $this->link_resource_type(current_filter());

					$set_header = sprintf(
						'Link: <%s>; rel=preload; as=%s',
						esc_url( $relative_path ),
						sanitize_html_class( $resource_type )
					);

					// Check header limit
					if( ($header_size + strlen($set_header) ) < SJH_MAX_HEADER_SIZE) {
						$header_size += strlen($set_header);
						$this->header_size_buffer = $header_size;
						header( $set_header, false );
					}
					
					
					$GLOBALS['sjh_http2_' . $resource_type . '_urls'][] = $relative_path;
				}

		    }

		    return $src;
		}
	}

	/**
	 *  Prepare if class 'Astra_Customizer_Loader' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	$SJH_Model = SJH_Model::get_instance();
}
