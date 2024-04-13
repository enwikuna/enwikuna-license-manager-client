<?php

namespace Enwikuna\Enwikuna_License_Manager_Client;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Interfaces\ELMC_Install_Interface;
use wpdb;

/**
 * Class ELMC_Install
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client
 */
class ELMC_Install extends ELMC_Singleton_Abstract implements ELMC_Install_Interface {

	/**
	 * WordPress database
	 *
	 * @var wpdb
	 */
	private wpdb $wpdb;

	/**
	 * Current version of Enwikuna License Manager Client
	 *
	 * @var string
	 */
	private string $current_elmc_version;

	/**
	 * DB updates and callbacks that need to be run per version
	 *
	 * @var array
	 */
	private array $version_updates = array();

	/**
	 * ELMC_Install constructor
	 */
	public function __construct() {
		global $wpdb; //DB

		$this->wpdb                 = $wpdb;
		$this->current_elmc_version = elmc_get_option( 'version' );

		$this->init_hooks();
	}

	/**
	 * Init / set up hooks
	 */
	private function init_hooks(): void {
		add_action( 'admin_init', array( $this, 'admin_init_action' ) );
	}

	/**
	 * Install Enwikuna License Manager Client
	 */
	public function install(): void {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( elmc_str_to_bool( elmc_get_transient( 'installing' ) ) ) {
			return;
		}

		// If installation not running, run it now
		elmc_set_transient( 'installing', 'yes', MINUTE_IN_SECONDS * 10 );

		ELMC_Uninstall::remove_admin_notices();
		ELMC_Uninstall::clear_scheduled_hooks();

		$this->update_functions();
		$this->create_cron_jobs();
		$this->update_elmc_version();

		elmc_delete_transient( 'installing' );

		/**
		 * This hook fires after the plugin is installed successfully
		 *
		 * @wp-hook elmc_installed
		 * @since 1.0.0
		 */
		do_action( 'elmc_installed' );
	}

	/**
	 * Check for plugin update -> install again to verify data
	 */
	public function admin_init_action(): void {
		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( $this->current_elmc_version, Constants::get_constant( 'ELMC_VERSION' ), '<' ) ) {
			$this->install();

			/**
			 * This hook fires if the plugin has been updated
			 *
			 * @wp-hook elmc_install_plugin_updated
			 * @since 1.0.0
			 */
			do_action( 'elmc_install_plugin_updated' );
		}
	}

	/**
	 * Runs all update functions that are needed for new version
	 */
	private function update_functions(): void {
		$loop = 10;

		/**
		 * This filter filters if the update functions should be used
		 *
		 * @param bool $enable_update_functions If update functions enabled or not
		 *
		 * @wp-hook elmc_enable_update_functions
		 * @since 1.0.0
		 */
		if ( ! empty( $this->current_elmc_version ) && ! empty( $this->version_updates ) && apply_filters( 'elmc_enable_update_functions', true ) ) {
			foreach ( $this->version_updates as $version => $update_callbacks ) {
				if ( version_compare( $this->current_elmc_version, $version, '<' ) ) {
					foreach ( $update_callbacks as $update_callback ) {
						ELMC_Cron_Jobs::get_instance()->schedule_single(
							time() + $loop,
							'elmc_update_callback',
							false,
							array(
								'update_callback' => $update_callback,
							)
						);

						$loop += 10;
					}

					elmc_set_transient( 'updating', 'yes', MINUTE_IN_SECONDS * 30 );
				}
			}
		}
	}

	/**
	 * Creates all needed Cron jobs for the plugin
	 *
	 * @return void
	 */
	private function create_cron_jobs(): void {
		ELMC_Cron_Jobs::get_instance()->create_cron_jobs();
	}

	/**
	 * Update Enwikuna License Manager Client version to current
	 */
	private function update_elmc_version(): void {
		elmc_delete_option( 'version' );
		elmc_update_option( 'version', Constants::get_constant( 'ELMC_VERSION' ) );
	}
}
