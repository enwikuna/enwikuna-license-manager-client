<?php

namespace Enwikuna\Enwikuna_License_Manager_Client;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\ELMC_Admin;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\ELMC_Admin_Notices;
use Enwikuna\Enwikuna_License_Manager_Client\Interfaces\ELMC_Interface;
use stdClass;

/**
 * Class ELMC
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client
 */
class ELMC extends ELMC_Singleton_Abstract implements ELMC_Interface {

	/**
	 * Enwikuna License Manager Client version
	 *
	 * @var string
	 */
	public string $version = '1.0.1';

	/**
	 * The signing key used to support signed releases
	 *
	 * @var string|null
	 */
	private $trusted_signing_key;

	/**
	 * List of all products
	 *
	 * @var array
	 */
	private array $products = array();

	/**
	 * ELMC constructor
	 */
	public function __construct() {
		$this->init_core_hooks();
		$this->includes();
		$this->define_constants();

		// Register uninstall hook -> uninstall must be always possible, also if dependency is missing
		register_uninstall_hook( Constants::get_constant( 'ELMC_PLUGIN_FILE' ), '\Enwikuna\Enwikuna_License_Manager_Client\ELMC_Uninstall::uninstall' );

		if ( ! ELMC_Dependencies::get_instance()->is_loadable() ) {
			ELMC_Admin_Notices::get_instance(); //Load always for dependency notices

			return;
		}

		/**
		 * This hook fires before the plugin load
		 *
		 * @wp-hook elmc_before_load
		 * @since 1.0.0
		 */
		do_action( 'elmc_before_load' );

		// Register activation hook
		register_activation_hook( Constants::get_constant( 'ELMC_PLUGIN_FILE' ), array( ELMC_Install::get_instance(), 'install' ) );

		// Register deactivation hook
		register_deactivation_hook( Constants::get_constant( 'ELMC_PLUGIN_FILE' ), '\Enwikuna\Enwikuna_License_Manager_Client\ELMC_Uninstall::deactivate' );

		$this->trusted_signing_key = Constants::get_constant( 'ELMC_TRUSTED_SIGNING_KEY' );

		$this->init_hooks();

		/**
		 * This hook fires after the plugin load
		 *
		 * @wp-hook elmc_after_load
		 * @since 1.0.0
		 */
		do_action( 'elmc_after_load' );
	}

	/**
	 * Init / setup important core hooks from WP -> used in dependency check too
	 */
	private function init_core_hooks(): void {
		add_action( 'init', array( $this, 'core_init_action' ), 0 );
	}

	/**
	 * Init / set up hooks
	 */
	private function init_hooks(): void {
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded_action' ) ); // Must be loaded after all plugins
		add_action( 'init', array( $this, 'init_action' ) ); // Must be loaded after all themes
		add_action( 'activated_plugin', array( $this, 'flush_product_cache_action' ) );
		add_action( 'deactivated_plugin', array( $this, 'flush_product_cache_action' ) );
		add_action( 'delete_site_transient_update_plugins', array( $this, 'flush_product_cache_action' ) );
		add_action( 'delete_site_transient_update_themes', array( $this, 'flush_product_cache_action' ) );
		add_action( 'automatic_updates_complete', array( $this, 'flush_product_cache_action' ) );
		add_filter( 'wp_trusted_keys', array( $this, 'filter_wp_trusted_keys' ) );
		add_filter( 'wp_signature_hosts', array( $this, 'filter_wp_signature_hosts' ) );
		add_filter( 'wp_signature_url', array( $this, 'filter_wp_signature_url' ), 10, 2 );

		// Used to test against local Enwikuna License Manager
		if ( Constants::get_constant( 'ELMC_DEBUG' ) ) {
			add_filter( 'http_request_host_is_external', '__return_true' );
			add_filter( 'http_request_args', array( $this, 'filter_http_request_args' ) );
		}
	}

	/**
	 * Include core functions used plugin wide
	 */
	private function includes(): void {
		/**
		 * This filter filters includes (function files)
		 *
		 * @param array $includes Includes all function filenames
		 *
		 * @wp-hook elmc_includes
		 * @since 1.0.0
		 */
		$includes = apply_filters(
			'elmc_includes',
			array(
				'core',
			)
		);

		foreach ( $includes as $include ) {
			include_once Constants::get_constant( 'ELMC_ABSPATH' ) . 'includes' . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'elmc-' . $include . '-functions.php';
		}
	}

	/**
	 * Define Enwikuna License Manager Client constants
	 */
	private function define_constants(): void {
		$this->define_constant( 'ELMC_PLUGIN_BASENAME', plugin_basename( Constants::get_constant( 'ELMC_PLUGIN_FILE' ) ) );
		$this->define_constant( 'ELMC_LANG_DIR', Constants::get_constant( 'ELMC_ABSPATH' ) . 'i18n' . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR );
		$this->define_constant( 'ELMC_PLUGIN_URL', untrailingslashit( plugins_url( DIRECTORY_SEPARATOR, Constants::get_constant( 'ELMC_PLUGIN_FILE' ) ) ) . DIRECTORY_SEPARATOR );
		$this->define_constant( 'ELMC_TEMPLATES_DIR', Constants::get_constant( 'ELMC_ABSPATH' ) . 'templates' . DIRECTORY_SEPARATOR );
		$this->define_constant( 'ELMC_ADMIN_TEMPLATES_DIR', Constants::get_constant( 'ELMC_TEMPLATES_DIR' ) . 'admin' . DIRECTORY_SEPARATOR );
		$this->define_constant( 'ELMC_ASSETS_URL', Constants::get_constant( 'ELMC_PLUGIN_URL' ) . 'assets' . DIRECTORY_SEPARATOR );
		$this->define_constant( 'ELMC_VERSION', $this->version );
	}

	/**
	 * Define constant if not already set
	 *
	 * @param string $name
	 * @param string|bool $value
	 */
	private function define_constant( string $name, $value ): void {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Core init action
	 */
	public function core_init_action(): void {
		$this->load_plugin_textdomain();
	}

	/**
	 * Default plugins loaded action
	 */
	public function plugins_loaded_action(): void {
		$this->maybe_register_products();
	}

	/**
	 * Init products
	 *
	 * @return void
	 */
	public function init_action(): void {
		$this->main_init_classes();
		$this->maybe_register_products();
	}

	/**
	 * Init important classes
	 */
	private function main_init_classes(): void {
		ELMC_Admin::get_instance();
		ELMC_Cron_Jobs::get_instance();
	}

	/**
	 * Load Enwikuna License Manager Client translation files
	 */
	private function load_plugin_textdomain(): void {
		$locale = determine_locale();
		$locale = apply_filters( 'plugin_locale', $locale, 'enwikuna-license-manager-client' );

		unload_textdomain( 'enwikuna-license-manager-client' );
		load_textdomain( 'enwikuna-license-manager-client', Constants::get_constant( 'ELMC_LANG_DIR' ) . 'enwikuna-license-manager-client-' . $locale . '.mo' );
		load_plugin_textdomain( 'enwikuna-license-manager-client', false, Constants::get_constant( 'ELMC_LANG_DIR' ) );
	}

	/**
	 * Maybe register products
	 *
	 * @return void
	 */
	private function maybe_register_products(): void {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( is_multisite() && ! is_network_admin() && ! is_plugin_active_for_network( Constants::get_constant( 'ELMC_PLUGIN_BASENAME' ) ) ) {
			return;
		}

		$this->register_products();
	}

	/**
	 * Register the products
	 */
	public function register_products(): void {
		/**
		 * This filter filters the registered updatable products
		 *
		 * @param array $updatable_products Includes all updatable products
		 *
		 * @wp-hook elmc_updatable_products
		 * @since 1.0.0
		 */
		$updatable_products = apply_filters( 'elmc_updatable_products', array() );
		$products           = $this->prepare_updatable_products( $updatable_products );

		if ( ! empty( $products ) ) {
			foreach ( $products as $product ) {
				if ( is_object( $product ) && ! empty( $product->file ) && ! empty( $product->product_id ) ) {
					$this->add_product(
						$product->file,
						$product->product_id,
						array(
							'setup_wizard' => $product->setup_wizard ?? null,
						)
					);
				}
			}
		}

		// Self update
		$this->add_product( 'enwikuna-license-manager-client/enwikuna-license-manager-client.php', Constants::get_constant( 'ELMC_PRODUCT_UUID' ), array( 'free' => true ) );
	}

	/**
	 * Adds a new product
	 *
	 * @param string $file
	 * @param string $product_id
	 * @param array $args
	 *
	 * @return bool
	 */
	public function add_product( string $file, string $product_id, array $args = array() ): bool {
		$parsed_args = wp_parse_args(
			$args,
			array(
				'free'         => false,
				'setup_wizard' => null,
			)
		);

		if ( ! empty( $file ) && ! isset( $this->products[ $file ] ) ) {
			$is_theme = false === strpos( $file, 'style.css' ) ? false : true;

			$this->products[ $file ] = $is_theme ? new ELMC_Theme( $file, $product_id, $parsed_args ) : new ELMC_Plugin( $file, $product_id, $parsed_args );

			if ( $this->products[ $file ]->is_registered() && ! $this->products[ $file ]->has_expired() ) {
				$this->products[ $file ]->set_updater( new ELMC_Updater( $this->products[ $file ] ) );
			}
		}

		return false;
	}

	/**
	 * Returns all products
	 *
	 * @param bool $show_free
	 *
	 * @return ELMC_Product_Abstract[]
	 */
	public function get_products( bool $show_free = true ): array {
		$products = $this->products;

		if ( ! $show_free ) {
			foreach ( $this->products as $file => $product ) {
				/**
				 * @var ELMC_Product_Abstract $product
				 */
				if ( $product->is_free() ) {
					unset( $products[ $file ] );
				}
			}
		}

		return $products;
	}

	/**
	 * Returns a product (plugin) by its basename
	 *
	 * @param $file
	 *
	 * @return false|ELMC_Product_Abstract
	 */
	public function get_product( $file ) {
		return ( $this->products[ $file ] ?? false );
	}

	/**
	 * /**
	 * Get registered products
	 *
	 * @return array
	 */
	public function get_registered_products(): array {
		if ( is_multisite() ) {
			return get_site_option( 'elmc_registered_products', array() );
		}

		return elmc_get_option( 'registered_products', array() );
	}

	/**
	 * Update registered products
	 *
	 * @param $products
	 *
	 * @return bool
	 */
	public function update_registered_products( $products ): bool {
		if ( is_multisite() ) {
			return update_site_option( 'elmc_registered_products', $products );
		}

		return elmc_update_option( 'registered_products', $products );
	}

	/**
	 * Update all registered products data from licensing server
	 *
	 * @param bool $force_flush_cache
	 */
	public function update_registered_products_data( bool $force_flush_cache = false ): void {
		$products = $this->get_products( false );

		elmc_delete_option( 'expiring_products' );

		if ( ! empty( $products ) ) {
			foreach ( $products as $file => $product ) {
				if ( ! $product->is_registered() ) {
					continue;
				}

				$host = $product->get_host();

				// If the host is not the same as the registered one, unregister the product e.g. when cloning the page
				if ( ! empty( $host ) && elmc_prepare_license_host_url( home_url() ) !== $host ) {
					$product->unregister();
				} else {
					if ( $force_flush_cache ) {
						$product->flush_cache();
					}

					$expire = $product->refresh_expiration_date();

					if ( ! empty( $expire ) ) {
						$diff              = date_diff( date_create( gmdate( 'Y-m-d' ) ), date_create( $expire ) );
						$expiring_products = elmc_get_option( 'expiring_products', array() );

						/**
						 * This filter filters the expiration offset for a product
						 *
						 * @param int $expiration_offset The expiration offset in days (default 7)
						 * @param ELMC_Product_Abstract $product The product object
						 *
						 * @wp-hook elmc_{product_slug}_expiration_offset
						 * @since 1.0.0
						 */
						$expiration_offset = apply_filters( 'elmc_' . $product->get_slug() . '_expiration_offset', 7, $product );

						if ( ( empty( $diff->y ) && empty( $diff->m ) && $diff->d <= $expiration_offset ) || strtotime( $expire ) <= time() ) {
							$expiring_products[ $file ] = true;

							$product->flush_cache();
						} else {
							$expiring_products[ $file ] = false;
						}

						elmc_update_option( 'expiring_products', $expiring_products );
					}

					$product->refresh_status();
				}
			}
		} else {
			elmc_delete_option( 'dismiss_products_expiring_notice' );
		}
	}

	/**
	 * Prepare updatable products
	 *
	 * @param array $updatable_products
	 * @param array $args
	 *
	 * @return array
	 */
	private function prepare_updatable_products( array $updatable_products, array $args = array() ): array {
		$products = array();

		if ( ! empty( $updatable_products ) ) {
			foreach ( $updatable_products as $file => $updatable_product ) {
				if ( $this->is_plugin_active( $file ) || $this->is_theme_active( $file ) ) {
					if ( isset( $updatable_product['args'] ) ) {
						$args = wp_parse_args( $args, $updatable_product['args'] );
					}

					$products[ $file ] = $this->generate_updatable_product(
						$file,
						$updatable_product['product_id'],
						$args
					);
				}
			}
		}

		return $products;
	}

	/**
	 * Generates an updatable product in the right format
	 *
	 * @param string $file
	 * @param string $product_id
	 * @param array $args
	 *
	 * @return stdClass
	 */
	public function generate_updatable_product( string $file, string $product_id, array $args = array() ): stdClass {
		$plugin             = new stdClass();
		$plugin->file       = $file;
		$plugin->product_id = $product_id;

		if ( isset( $args['setup_wizard'] ) ) {
			$plugin->setup_wizard = $args['setup_wizard'];
		}

		return $plugin;
	}

	/**
	 * Flush product gateway cache
	 */
	public function flush_product_cache_action(): void {
		$products = $this->get_products( false );

		if ( ! empty( $products ) ) {
			foreach ( $products as $product ) {
				$product->flush_cache();
			}
		}
	}

	/**
	 * Add trusted signing key to trusted keys
	 *
	 * @param array $trusted_keys
	 *
	 * @return array
	 */
	public function filter_wp_trusted_keys( array $trusted_keys ): array {
		if ( ! empty( $this->trusted_signing_key ) ) {
			$trusted_keys[] = $this->trusted_signing_key;
		}

		return $trusted_keys;
	}

	/**
	 * Add signature host to signature hostnames
	 *
	 * @param array $hostnames
	 *
	 * @return array
	 */
	public function filter_wp_signature_hosts( array $hostnames ): array {
		if ( ! empty( $this->trusted_signing_key ) ) {
			$url         = wp_parse_url( Constants::get_constant( 'ELMC_REST_API_URL' ) );
			$hostnames[] = $url['host'];
		}

		return $hostnames;
	}

	/**
	 * Replace download URL with signature URL for signature download
	 *
	 * @param false|string $signature_url
	 * @param string $url
	 *
	 * @return false|string
	 */
	public function filter_wp_signature_url( $signature_url, string $url ) {
		if ( ! empty( $this->trusted_signing_key ) && strpos( $url, Constants::get_constant( 'ELMC_REST_API_URL' ) ) !== false ) {
			$signature_url = str_replace( 'download/latest', 'download/latest/signature', $url );
		}

		return $signature_url;
	}

	/**
	 * Disable SSL verification for locally created SSL certificates
	 *
	 * @param array $parsed_args
	 *
	 * @return array
	 */
	public function filter_http_request_args( array $parsed_args ): array {
		$parsed_args['sslverify'] = false;

		return $parsed_args;
	}

	/**
	 * Check if a plugin is active
	 *
	 * @param $plugin_file
	 *
	 * @return bool
	 */
	private function is_plugin_active( $plugin_file ): bool {
		$active_plugins = get_site_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = wp_parse_args( $active_plugins, $network_activated_plugins );
		}

		if ( in_array( $plugin_file, $active_plugins, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a theme is active
	 *
	 * @param $theme_file
	 *
	 * @return bool
	 */
	private function is_theme_active( $theme_file ): bool {
		$theme = false === strpos( $theme_file, 'style.css' ) ? $theme_file . '/style.css' : $theme_file;

		return $this->get_active_theme() === $theme;
	}

	/**
	 * Returns the current active theme
	 *
	 * @return string
	 */
	private function get_active_theme(): string {
		$theme = get_option( 'stylesheet' );

		return false === strpos( $theme, 'style.css' ) ? $theme . '/style.css' : $theme;
	}
}
