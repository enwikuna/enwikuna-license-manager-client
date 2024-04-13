<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces\ELMC_Admin_Templates_Interface;

/**
 * Class ELMC_Admin_Templates
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin
 */
class ELMC_Admin_Templates extends ELMC_Singleton_Abstract implements ELMC_Admin_Templates_Interface {

	/**
	 * The file path to the admin pages dir
	 *
	 * @var string
	 */
	private string $admin_pages_dir;

	/**
	 * ELMC_Admin_Templates constructor
	 */
	public function __construct() {
		$this->admin_pages_dir = Constants::get_constant( 'ELMC_ADMIN_TEMPLATES_DIR' ) . 'pages' . DIRECTORY_SEPARATOR;
	}

	/**
	 * Get an admin page
	 *
	 * @param string $page
	 * @param array $args
	 */
	public function get_admin_page( string $page, array $args = array() ): void {
		$admin_page = $this->admin_pages_dir . $page . '.php';

		elmc_get_template( $admin_page, $args );
	}
}
