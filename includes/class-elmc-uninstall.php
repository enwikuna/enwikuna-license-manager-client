<?php

namespace Enwikuna\Enwikuna_License_Manager_Client;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\ELMC_Admin_Notices;
use Enwikuna\Enwikuna_License_Manager_Client\Interfaces\ELMC_Uninstall_Interface;

/**
 * Class ELMC_Uninstall
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client
 */
class ELMC_Uninstall implements ELMC_Uninstall_Interface {

	/**
	 * Deactivate Enwikuna License Manager Client
	 */
	public static function deactivate(): void {
		self::clear_scheduled_hooks();
		self::remove_admin_notices();
	}

	/**
	 * Uninstall Enwikuna License Manager Client
	 */
	public static function uninstall(): void {
		global $wpdb; //DB

		self::clear_scheduled_hooks();
		self::remove_admin_notices();

		// Only remove ALL plugin data if ELMC_REMOVE_ALL_DATA constant is set to true in the wp-config.php
		if ( Constants::get_constant( 'ELMC_REMOVE_ALL_DATA' ) ) {
			// Delete options
			$options_table        = $wpdb->options;
			$delete_options_query = $wpdb->prepare( "DELETE FROM $options_table WHERE option_name LIKE %s;", 'elmc\_%' ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

			$wpdb->query( $delete_options_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			// Delete usermeta
			$usermeta_table        = $wpdb->usermeta;
			$delete_usermeta_query = $wpdb->prepare( "DELETE FROM $usermeta_table WHERE meta_key LIKE %s;", 'elmc\_%' ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

			$wpdb->query( $delete_usermeta_query ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			wp_cache_flush();
		}
	}

	/**
	 * Clears all scheduled hooks
	 */
	public static function clear_scheduled_hooks(): void {
		$cron_hooks = ELMC_Cron_Jobs::get_instance()->get_cron_hooks();

		foreach ( $cron_hooks as $cron_hook ) {
			ELMC_Cron_Jobs::get_instance()->clear( $cron_hook );
		}
	}

	/**
	 * Remove all admin notices
	 *
	 * @return void
	 */
	public static function remove_admin_notices(): void {
		ELMC_Admin_Notices::get_instance()->remove_all_admin_notices();
	}
}
