<?php


class LittleMonkey {
	protected static $instance = null;

	/**
	 * Create/Get instance
	 *
	 * @return LittleMonkey|null
	 */
	public static function get_instance() {
		null === self::$instance and self::$instance = new self;

		return self::$instance;
	}

	/**
	 *
	 * Load admin menu, and assets
	 *
	 */
	public function init() {
		add_action( 'admin_menu', array( self::get_instance(), 'admin_menu' ) );

		// Enqueue scripts
		$this->enqueue_scripts();

		// Enqueue styles
		$this->enqueue_styles();

		// Register ajax actions
		$this->ajax_register();
	}
	/**
	 *
	 * Create admin menu
	 *
	 */
	public function admin_menu() {
		add_menu_page(
			LITTLEMONKEY_NAME . ' - Settings',
			'Settings',
			'manage_options',
			'littlemonkey_plugin_options',
			array( self::get_instance(), 'view_settings' ),
			'data:image/svg+xml;base64,' . base64_encode( '<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="M1591 1448q56 89 21.5 152.5t-140.5 63.5h-1152q-106 0-140.5-63.5t21.5-152.5l503-793v-399h-64q-26 0-45-19t-19-45 19-45 45-19h512q26 0 45 19t19 45-19 45-45 19h-64v399zm-779-725l-272 429h712l-272-429-20-31v-436h-128v436z"/></svg>' )
		);

		add_submenu_page(
			'littlemonkey_plugin_options',
			LITTLEMONKEY_NAME . ' - Bulk optimization',
			'Bulk Optimization',
			'manage_options',
			'littlemonkey_bulk_optimization',
			array( self::get_instance(), 'view_bulk' )
		);
	}

	public function ajax_register() {
		add_action( 'wp_ajax_little_monkey_ajax_progress', array( self::get_instance(), 'ajax_progress' ) );
	}

	public function ajax_progress() {
		wp_send_json(array('result' => 'success'));
	}

	/**
	 *
	 * Enqueue scripts
	 *
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'littlemonkey_script', LITTLEMONKEY_ASSETS . 'js/littlemonkey_scripts.js', array( 'jquery' ), '1.0.0', true );
		wp_localize_script( 'littlemonkey_script', 'WP_AJAX',
			array( 'WP_AJAX_URL' => admin_url( 'admin-ajax.php' ), 'WP_AJAX_IDENTIFIER' => 'little_monkey' ) );
	}

	/**
	 *
	 * Enqueue styles
	 *
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'littlemonkey_styles', LITTLEMONKEY_ASSETS . 'css/littlemonkey_styles.css' );
	}

	/**
	 *
	 * Thumbnail sizes
	 *
	 */
	public function get_thumbnail_sizes() {
		// Default wordpress sizes
		$default_sizes = [ 'large', 'medium_large', 'medium', 'thumbnail' ];

		// Get wordpress sizes
		$sizes = get_intermediate_image_sizes();

		// Get aditional sizes
		$additional_sizes = wp_get_additional_image_sizes();

		// Setup final size array
		$final_sizes = [];

		foreach ( $sizes as $size ) {
			$image_array = array(
				'name'      => '',
				'width'     => '',
				'height'    => '',
				'is_crop'   => false,
				'is_custom' => false
			);

			// Set thumb name
			$image_array['name'] = $size;

			//
			if ( ! in_array( $size, $default_sizes ) ) {
				$image_array['is_custom'] = true;
			}

			// Set width
			if ( ! empty( $additional_sizes[ $size ]['width'] ) ) {
				$image_array['width'] = (int) $additional_sizes[ $size ]['width'];
			} else {
				$image_array['width'] = (int) get_option( "{$size}_size_w" );
			}

			// Set height
			if ( ! empty( $additional_sizes[ $size ]['height'] ) ) {
				$image_array['height'] = (int) $additional_sizes[ $size ]['height'];
			} else {
				$image_array['height'] = (int) get_option( "{$size}_size_h" );
			}

			// Set crop
			if ( ! empty( $additional_sizes[ $size ]['is_crop'] ) ) {
				$image_array['is_crop'] = (int) $additional_sizes[ $size ]['is_crop'];
			} else {
				$image_array['is_crop'] = (int) get_option( "{$size}_crop" );
			}

			$final_sizes[] = $image_array;
		}

		return $final_sizes;
	}

	/**
	 *
	 * Views: index
	 *
	 */
	public function view_settings() {
		if ( ! empty( $_POST ) ) {
			$lm_api_key             = (string) $_POST['lm_api_key'];
			$lm_compression_level   = (int) $_POST['lm_compression_level'];
			$lm_process_uploads     = (bool) $_POST['lm_process_uploads'];
			$lm_backup              = (bool) $_POST['lm_backup'];
			$lm_remove_exif         = (bool) $_POST['lm_remove_exif'];
			$lm_resize_large_images = (bool) $_POST['lm_resize_large_images'];
			$lm_resize_height       = (int) $_POST['lm_resize_height'];
			$lm_resize_width        = (int) $_POST['lm_resize_width'];

			// Setup sizes
			$sizes = $_POST['lm_optimize_sizes'];
			foreach ( $sizes as $size => $v ) {
				$lm_optimize_sizes[] = $size;
			}

			// Lm settings
			$lm_settings['lm_api_key']             = $lm_api_key;
			$lm_settings['lm_compression_level']   = $lm_compression_level;
			$lm_settings['lm_process_uploads']     = $lm_process_uploads;
			$lm_settings['lm_backup']              = $lm_backup;
			$lm_settings['lm_remove_exif']         = $lm_remove_exif;
			$lm_settings['lm_resize_large_images'] = $lm_resize_large_images;
			$lm_settings['lm_resize_height']       = $lm_resize_height;
			$lm_settings['lm_resize_width']        = $lm_resize_width;
			$lm_settings['lm_roptimize_sizes']     = $lm_optimize_sizes;

			// Optimize sizes
			$lm_optimize_sizes = json_encode( $lm_optimize_sizes );

			// Check if is valid api
			update_option( 'lm_api_key', $lm_api_key );

			// Setup update options
			update_option( 'lm_compression_level', (int) $lm_compression_level );
			update_option( 'lm_process_uploads', (int) $lm_process_uploads );
			update_option( 'lm_backup', (int) $lm_backup );
			update_option( 'lm_remove_exif', (int) $lm_remove_exif );
			update_option( 'lm_resize_large_images', (int) $lm_resize_large_images );
			update_option( 'lm_resize_height', (int) $lm_resize_height );
			update_option( 'lm_resize_width', (int) $lm_resize_width );
			update_option( 'lm_optimize_sizes', $lm_optimize_sizes );
			update_option( 'lm_settings', json_encode( $lm_settings ) );
		}


		// View
		$thumbnail_sizes = $this->get_thumbnail_sizes();

		// Get setting view
		require LITTLEMONKEY_VIEWS . 'settings.php';
	}


	/**
	 *
	 * Views: index
	 *
	 */
	public function view_bulk() {
		echo 'ok';
	}
}
