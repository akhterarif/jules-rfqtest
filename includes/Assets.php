<?php

namespace WeLabs\Rfqtest;

class Assets {
	/**
	 * The constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_all_scripts' ), 10 );

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 10 );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ) );
		}
	}

	/**
	 * Register all Dokan scripts and styles.
	 *
	 * @return void
	 */
	public function register_all_scripts() {
		$this->register_styles();
		$this->register_scripts();
	}

	/**
	 * Register scripts.
	 *
	 * @param array $scripts
	 *
	 * @return void
	 */
	public function register_scripts() {
		$admin_script    = RFQTEST_PLUGIN_ADMIN_ASSET . '/js/script.js';
		$frontend_script = RFQTEST_PLUGIN_PUBLIC_ASSET . '/js/script.js';

		wp_register_script( 'rfqtest_admin_script', $admin_script, array(), RFQTEST_PLUGIN_VERSION, true );
		wp_register_script( 'rfqtest_script', $frontend_script, array(), RFQTEST_PLUGIN_VERSION, true );
	}

	/**
	 * Register styles.
	 *
	 * @return void
	 */
	public function register_styles() {
		$admin_style    = RFQTEST_PLUGIN_ADMIN_ASSET . '/css/style.css';
		$frontend_style = RFQTEST_PLUGIN_PUBLIC_ASSET . '/css/style.css';

		wp_register_style( 'rfqtest_admin_style', $admin_style, array(), RFQTEST_PLUGIN_VERSION );
		wp_register_style( 'rfqtest_style', $frontend_style, array(), RFQTEST_PLUGIN_VERSION );
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'rfqtest_admin_script' );
		wp_localize_script(
			'rfqtest_admin_script',
			'Rfqtest_Admin',
			array()
		);
	}

	/**
	 * Enqueue front-end scripts.
	 *
	 * @return void
	 */
	public function enqueue_front_scripts() {
		wp_enqueue_script( 'rfqtest_script' );
		wp_localize_script(
			'rfqtest_script',
			'Rfqtest',
			array()
		);
	}
}
