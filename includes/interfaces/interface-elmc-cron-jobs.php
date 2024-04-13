<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Cron_Jobs_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Interfaces
 */
interface ELMC_Cron_Jobs_Interface {

	/**
	 * ELMC_Cron_Jobs constructor
	 */
	public function __construct();

	/**
	 * Function to create all Cron jobs
	 */
	public function create_cron_jobs(): void;

	/**
	 * Adds the license check cron after products are initialized
	 */
	public function plugins_loaded_action(): void;

	/**
	 * Returns all formatted Enwikuna License Manager Client cron events
	 *
	 * @return array
	 */
	public function get_formatted_cron_events(): array;

	/**
	 * Checks the license if expired via cron
	 */
	public function elmc_license_check_action(): void;

	/**
	 * Runs the needed updates during installation
	 *
	 * @param string $update_callback
	 */
	public function elmc_update_callback_action( string $update_callback ): void;

	/**
	 * Schedules a single event
	 *
	 * @param int $timestamp
	 * @param string $hook
	 * @param bool $check_scheduled
	 * @param array $args
	 */
	public function schedule_single( int $timestamp, string $hook, bool $check_scheduled = true, array $args = array() ): void;

	/**
	 * Schedules a recurring event
	 *
	 * @param int $timestamp
	 * @param string $recurrence
	 * @param string $hook
	 * @param bool $check_scheduled
	 * @param array $args
	 */
	public function schedule_recurring( int $timestamp, string $recurrence, string $hook, bool $check_scheduled = true, array $args = array() ): void;

	/**
	 * Clears scheduled hook
	 *
	 * @param string $hook
	 *
	 * @return void
	 */
	public function clear( string $hook ): void;

	/**
	 * Check if an event is already scheduled
	 *
	 * @param string $hook
	 * @param array $args
	 *
	 * @return bool
	 */
	public function is_scheduled( string $hook, array $args = array() ): bool;

	/**
	 * Checks if the plugin is currently updating
	 *
	 * @return bool
	 */
	public function is_updating(): bool;

	/**
	 * Returns a list of all cron hooks used in the plugin
	 *
	 * @return array
	 */
	public function get_cron_hooks(): array;
}
