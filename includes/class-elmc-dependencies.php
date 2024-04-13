<?php

namespace Enwikuna\Enwikuna_License_Manager_Client;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Interfaces\ELMC_Dependencies_Interface;

/**
 * Class ELMC_Dependencies
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client
 */
class ELMC_Dependencies extends ELMC_Singleton_Abstract implements ELMC_Dependencies_Interface {

	/**
	 * Whether the plugin is loadable or not
	 *
	 * @var bool
	 */
	private bool $loadable = true;

	/**
	 * ELMC_Dependencies constructor
	 */
	public function __construct() {
		$required_constants = array(
			'ELMC_COMPANY_NAME',
			'ELMC_REST_API_URL',
			'ELMC_REST_API_KEY',
			'ELMC_REST_API_SECRET',
			'ELMC_PRODUCT_UUID',
		);

		foreach ( $required_constants as $constant ) {
			if ( empty( Constants::get_constant( $constant ) ) ) {
				$this->loadable = false;

				break;
			}
		}
	}

	/**
	 * Return if the plugin is loadable or not
	 *
	 * @return bool
	 */
	public function is_loadable(): bool {
		/**
		 * This filter filters if the plugin is loadable
		 *
		 * @param bool $loadable If is loadable or not
		 *
		 * @wp-hook elmc_dependencies_is_loadable
		 * @since 1.0.0
		 */
		return apply_filters( 'elmc_dependencies_is_loadable', $this->loadable );
	}
}
