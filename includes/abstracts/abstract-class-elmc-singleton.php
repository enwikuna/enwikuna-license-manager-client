<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Abstracts;

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\Interfaces\ELMC_Singleton_Interface;

/**
 * Class ELMC_Singleton_Abstract
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Abstracts
 */
abstract class ELMC_Singleton_Abstract implements ELMC_Singleton_Interface {

	/**
	 * @var array
	 */
	protected static array $instances = array();

	/**
	 * @return $this
	 */
	public static function get_instance(): self {
		$class = static::class;

		if ( ! array_key_exists( $class, self::$instances ) ) {
			self::$instances[ $class ] = new $class();
		}

		return self::$instances[ $class ];
	}

	/**
	 * Cloning is forbidden
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'enwikuna-license-manager-client' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'enwikuna-license-manager-client' ), '1.0.0' );
	}
}
