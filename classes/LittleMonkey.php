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

		// Load includes
		$this->includes();
	}

	public function includes() {
		// Include all dependencies
		require LITTLEMONKEY_PATH . 'classes/LittleMonkeyFiles.php';
	}

	/**
	 *
	 * Create admin menu
	 *
	 */
	public function admin_menu() {
		add_menu_page(
			LITTLEMONKEY_NAME . ' - Settings',
			LITTLEMONKEY_NAME,
			'manage_options',
			'littlemonkey_plugin_options',
			array( self::get_instance(), 'view_settings' ),
			'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320.63 416.78"><title>macacozada</title><g id="Camada_2" data-name="Camada 2"><path d="M71.4,308s9.51-17.17,9.87-49.71a286.37,286.37,0,0,0-.69-32.47C80,219.57,78.9,213.38,75.74,208c-2-3.41-6.33-7.87-10.61-8.87,0,0-12.78-24.22,8.73-61.62L69.42,131c4.78-7.17,48.15-67.22,153.21-52.71C199,65.84,170.94,63.45,151.47,63.64,158.55,21,109.73,0,109.73,0c-1.29,8.3,1.7,16.86,5.31,23.72a39.82,39.82,0,0,0-11-6.86C98,45.5,113.26,60.54,122.18,66.67c-58.44,15.86-85.38,61-85.38,61l.13.09-7.63,2.75c-10.69,13.74-11.18,70.19-11.18,70.19-31.6,34-12.26,83.44-12.26,83.44,8.52,20.35,13,24.34,14.15,25.08l-.12.81S30.05,320.47,36.47,322C36.47,322,61.55,321.69,71.4,308Z" style="fill:#fff"/><path d="M279.74,248.31c27.71-100.62-23.65-148.24-48.41-164.67C89.52,54.71,47,186.37,67.58,194.41c23,9,24,28,24,28s1,71-18,94C64.84,327,57.93,329,53,328.11,63.45,436,222.26,415.16,222.26,415.16,402.67,386.42,279.74,248.31,279.74,248.31ZM175.42,134.36a74.26,74.26,0,0,1,19-3.15,64.09,64.09,0,0,1,19.24,2.09q2.34.66,4.64,1.39c1.51.61,3,1.25,4.46,1.91l2.17,1c.7.41,1.4.8,2.08,1.25,1.37.86,2.71,1.73,4,2.68-1.63-.15-3.19-.39-4.73-.64-.78-.1-1.53-.26-2.29-.4l-2.31-.23c-1.53-.17-3-.37-4.53-.59s-3-.2-4.53-.33a153.17,153.17,0,0,0-17.92-.33q-9,.31-18,1.33c-6.06.66-12.13,1.49-18.49,2.32A56.94,56.94,0,0,1,175.42,134.36ZM278.14,346.5C251,375.24,195.91,380.83,155.2,371.25c-21.81-5.13-28.5-21.72-30-36-1.12-10.6.57-29.3,4.57-39.18l12.86-31.71h0a47.5,47.5,0,1,1,44.53-83,20.42,20.42,0,0,0,22.53.63A42.3,42.3,0,0,1,260.58,249h0S305.28,317.76,278.14,346.5Z" style="fill:#fff"/><ellipse cx="164.33" cy="222.84" rx="12.64" ry="12.99" transform="translate(-20.54 16.72) rotate(-5.48)" style="fill:#fff"/><ellipse cx="236.53" cy="220.95" rx="11.74" ry="12.07" transform="translate(-20.02 23.6) rotate(-5.48)" style="fill:#fff"/><ellipse cx="210.2" cy="254.47" rx="12.07" ry="20.24" transform="translate(-51.07 456.15) rotate(-88.11)" style="fill:#fff"/></g></svg>' )
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
		wp_send_json(array('progress' => '3'));
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
		$final_sizes = array();

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
		$file = new \LittleMonkey\Files\Files();
		$file->getFiles();
	}
}
