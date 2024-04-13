<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces;

defined( 'ABSPATH' ) || exit;

/**
 * Interface ELMC_Admin_Interface
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces
 */
interface ELMC_Admin_Interface {

	/**
	 * ELMC_Admin constructor
	 */
	public function __construct();

	/**
	 * Dashboard actions
	 */
	public function elmc_admin_page_action_elmc_action(): void;

	/**
	 * Pass plugins to API for update and changes
	 *
	 * @param false|object|array $result
	 * @param string $action
	 * @param object $args
	 *
	 * @return false|object|array
	 */
	public function filter_plugins_api( $result, string $action, object $args );

	/**
	 * Get all screen ids where Enwikuna License Manager Client assets should be loaded
	 *
	 * @return array
	 */
	public function get_elmc_admin_screen_ids(): array;

	/**
	 * Get all external screen ids where Enwikuna License Manager Client assets should be loaded
	 *
	 * @return array
	 */
	public function get_elmc_admin_external_screen_ids(): array;
}
