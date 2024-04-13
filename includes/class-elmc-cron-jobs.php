<?php

namespace Enwikuna\Enwikuna_License_Manager_Client;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Interfaces\ELMC_Cron_Jobs_Interface;

/**
 * Class ELMC_Cron_Jobs
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client
 */
class ELMC_Cron_Jobs extends ELMC_Singleton_Abstract implements ELMC_Cron_Jobs_Interface {

	/**
	 * ELMC_Cron_Jobs constructor
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Init / set up hooks
	 */
	private function init_hooks(): void {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded_action' ), 15 );
	}

	/**
	 * Function to create all Cron jobs
	 */
	public function create_cron_jobs(): void {
		// Schedule daily license check
		$this->schedule_recurring( time(), 'hourly', 'elmc_license_check' );
	}

	/**
	 * Adds the license check cron after products are initialized
	 */
	public function plugins_loaded_action(): void {
		add_action( 'elmc_license_check', array( $this, 'elmc_license_check_action' ) );
		add_action( 'elmc_update_callback', array( $this, 'elmc_update_callback_action' ) );
	}

	/**
	 * Returns all formatted Enwikuna License Manager Client cron events
	 *
	 * @return array
	 */
	public function get_formatted_cron_events(): array {
		$crons                 = _get_cron_array();
		$elmc_cron_hooks       = $this->get_cron_hooks();
		$formatted_cron_events = array();

		if ( ! empty( $crons ) ) {
			foreach ( $crons as $next_run => $cron ) {
				foreach ( $cron as $hook => $dings ) {
					if ( in_array( $hook, $elmc_cron_hooks, true ) ) {
						foreach ( $dings as $sig => $data ) {
							$formatted_cron_events[ "$hook-$sig-$next_run" ] = (object) array(
								'hook'     => $hook,
								'next_run' => $next_run,
								'sig'      => $sig,
								'args'     => $data['args'],
								'schedule' => $data['schedule'],
								'interval' => $data['interval'] ?? null,
							);
						}
					}
				}
			}

			// Ensure events are always returned in date descending order
			uasort(
				$formatted_cron_events,
				static function ( $a, $b ) {
					if ( $a->next_run === $b->next_run ) {
						return 0;
					}

					return ( $a->next_run > $b->next_run ) ? 1 : - 1;
				}
			);
		}

		return $formatted_cron_events;
	}

	/**
	 * Checks the license if expired via cron
	 */
	public function elmc_license_check_action(): void {
		ELMC::get_instance()->update_registered_products_data( true );

		elmc_delete_option( 'dismiss_products_expiring_notice' );
	}

	/**
	 * Runs the needed updates during installation
	 *
	 * @param string $update_callback
	 */
	public function elmc_update_callback_action( string $update_callback ): void {
		include_once Constants::get_constant( 'ELMC_ABSPATH' ) . 'includes' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'elmc-update-functions.php';

		if ( function_exists( $update_callback ) && is_callable( $update_callback ) ) {
			$update_callback();
		}
	}

	/**
	 * Schedules a single event
	 *
	 * @param int $timestamp
	 * @param string $hook
	 * @param bool $check_scheduled
	 * @param array $args
	 */
	public function schedule_single( int $timestamp, string $hook, bool $check_scheduled = true, array $args = array() ): void {
		if ( $check_scheduled && $this->is_scheduled( $hook, $args ) ) {
			return;
		}

		wp_schedule_single_event( $timestamp, $hook, $args );
	}

	/**
	 * Schedules a recurring event
	 *
	 * @param int $timestamp
	 * @param string $recurrence
	 * @param string $hook
	 * @param bool $check_scheduled
	 * @param array $args
	 */
	public function schedule_recurring( int $timestamp, string $recurrence, string $hook, bool $check_scheduled = true, array $args = array() ): void {
		if ( $check_scheduled && $this->is_scheduled( $hook, $args ) ) {
			return;
		}

		wp_schedule_event( $timestamp, $recurrence, $hook, $args );
	}

	/**
	 * Clears scheduled hook
	 *
	 * @param string $hook
	 *
	 * @return void
	 */
	public function clear( string $hook ): void {
		wp_unschedule_hook( $hook );
	}

	/**
	 * Check if an event is already scheduled
	 *
	 * @param string $hook
	 * @param array $args
	 *
	 * @return bool
	 */
	public function is_scheduled( string $hook, array $args = array() ): bool {
		if ( ! wp_next_scheduled( $hook, $args ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if the plugin is currently updating
	 *
	 * @return bool
	 */
	public function is_updating(): bool {
		$cron_events = $this->get_formatted_cron_events();

		if ( ! empty( $cron_events ) ) {
			foreach ( $cron_events as $cron_event_key => $cron_event ) {
				if ( 0 === strpos( $cron_event_key, 'elmc_update_callback' ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Returns a list of all cron hooks used in the plugin
	 *
	 * @return array
	 */
	public function get_cron_hooks(): array {
		return array(
			'elmc_license_check',
			'elmc_update_callback',
		);
	}
}
