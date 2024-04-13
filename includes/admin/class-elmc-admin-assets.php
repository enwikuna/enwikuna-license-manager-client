<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces\ELMC_Admin_Assets_Interface;

/**
 * Class ELMC_Admin_Assets
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin
 */
class ELMC_Admin_Assets extends ELMC_Singleton_Abstract implements ELMC_Admin_Assets_Interface {

	/**
	 * The suffix for script & style debugging in WordPress
	 *
	 * @var string
	 */
	private string $scripts_suffix;

	/**
	 * The file path to the admin css dir
	 *
	 * @var string
	 */
	private string $admin_css_dir;

	/**
	 * The file path to the admin js dir
	 *
	 * @var string
	 */
	private string $admin_js_dir;

	/**
	 * ELMC_Admin_Assets constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			$this->scripts_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$this->admin_css_dir  = Constants::get_constant( 'ELMC_ASSETS_URL' ) . 'css' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR;
			$this->admin_js_dir   = Constants::get_constant( 'ELMC_ASSETS_URL' ) . 'js' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR;

			$this->init_hooks();
		}
	}

	/**
	 * Init / set up hooks
	 */
	private function init_hooks(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_action' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles_action' ) );
	}

	/**
	 * Register admin scripts
	 */
	public function admin_enqueue_scripts_action(): void {
		$version = Constants::get_constant( 'ELMC_VERSION' );

		wp_register_script(
			'enwikuna-license-manager-client-admin',
			$this->admin_js_dir . 'enwikuna-license-manager-client-admin' . $this->scripts_suffix . '.js',
			array(
				'jquery',
			),
			$version
		);

		wp_enqueue_script( 'enwikuna-license-manager-client-admin' );

		wp_localize_script(
			'enwikuna-license-manager-client-admin',
			'elmc_admin',
			array(
				'dismiss_products_expiring_notice' => array(
					'nonce' => esc_js( wp_create_nonce( 'elmc-dismiss-products-expiring-notice' ) ),
				),
			)
		);
	}

	/**
	 * Register admin styles
	 */
	public function admin_enqueue_styles_action(): void {
		$screen    = get_current_screen();
		$screen_id = $screen->id ?? '';
		$version   = Constants::get_constant( 'ELMC_VERSION' );

		wp_register_style( 'enwikuna-license-manager-client-admin', $this->admin_css_dir . 'enwikuna-license-manager-client-admin' . $this->scripts_suffix . '.css', array(), $version );
		wp_register_style( 'enwikuna-license-manager-client-admin-general', $this->admin_css_dir . 'enwikuna-license-manager-client-admin-general' . $this->scripts_suffix . '.css', array(), $version );

		wp_enqueue_style( 'enwikuna-license-manager-client-admin-general' );

		if ( in_array( $screen_id, ELMC_Admin::get_instance()->get_elmc_admin_screen_ids(), true ) || in_array( $screen_id, ELMC_Admin::get_instance()->get_elmc_admin_external_screen_ids(), true ) ) {
			wp_enqueue_style( 'enwikuna-license-manager-client-admin' );
		}
	}
}
