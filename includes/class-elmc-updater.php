<?php

namespace Enwikuna\Enwikuna_License_Manager_Client;

defined( 'ABSPATH' ) || exit;

use ELMC_Product_Type_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Gateways\ELMC_ELM_Gateway;
use Enwikuna\Enwikuna_License_Manager_Client\Interfaces\ELMC_Updater_Interface;

/**
 * Class ELMC_Updater
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client
 */
class ELMC_Updater implements ELMC_Updater_Interface {

	/**
	 * The product passed in the constructor
	 *
	 * @var ELMC_Product_Abstract
	 */
	private ELMC_Product_Abstract $product;

	/**
	 * ELMC_Updater constructor
	 *
	 * @param ELMC_Product_Abstract $product
	 */
	public function __construct( ELMC_Product_Abstract $product ) {
		$this->product = $product;

		$this->init_hooks();
	}

	/**
	 * Init / set up hooks
	 */
	private function init_hooks(): void {
		$product_type = $this->product->get_type();

		add_filter(
			'pre_set_site_transient_update_' . ( ELMC_Product_Type_Abstract::THEME === $product_type ? 'themes' : 'plugins' ),
			array(
				$this,
				'filter_pre_set_site_transient_update_themes_plugins',
			),
			100
		);

		if ( ELMC_Product_Type_Abstract::PLUGIN === $product_type ) {
			add_action( 'in_plugin_update_message-' . plugin_basename( $this->product->get_file() ), array( $this, 'in_plugin_updater_message_action' ), 10, 2 );
		}
	}

	/**
	 * Check for product updates
	 *
	 * @param object $transient
	 *
	 * @return object
	 */
	public function filter_pre_set_site_transient_update_themes_plugins( object $transient ): object {
		if ( empty( $transient ) ) {
			return $transient;
		}

		$update_info = ELMC_ELM_Gateway::get_instance()->update_info( $this->product );

		if ( ! empty( $update_info ) ) {
			if ( empty( $update_info->new_version ) || version_compare( $update_info->new_version, $this->product->get_version(), '<=' ) ) {
				return $transient;
			}

			$filename = ELMC_Product_Type_Abstract::THEME === $this->product->get_type() ? $this->product->get_theme_key() : $this->product->get_file();

			/**
			 * This filter filters the plugin icon for the updater - must be a SVG image
			 *
			 * @param string $plugin_icon The plugin icon
			 * @param ELMC_Product_Abstract $product The product object
			 *
			 * @wp-hook elmc_{product_slug}_updater_plugin_icon
			 * @since 1.0.0
			 */
			$plugin_icon = apply_filters( 'elmc_' . $this->product->get_slug() . '_updater_plugin_icon', '', $this->product );

			if ( ELMC_Product_Type_Abstract::PLUGIN === $this->product->get_type() ) {
				$update_info->plugin = $this->product->get_file();
				$update_info->slug   = $this->product->get_slug();

				if ( ! empty( $plugin_icon ) ) {
					$update_info->icons = array(
						'svg' => $plugin_icon,
					);
				}
			} else {
				$update_info          = (array) $update_info;
				$update_info['theme'] = $this->product->get_theme_key();
			}

			$transient->response[ $filename ] = $update_info;
		}

		return $transient;
	}

	/**
	 * Output upgrade message for a plugin
	 *
	 * @param array $data
	 * @param object $response
	 *
	 * @return void
	 */
	public function in_plugin_updater_message_action( array $data, object $response ): void {
		if ( ! empty( $response->upgrade_notice ) ) {
			echo '<p class="elmc-upgrade-notice"><strong>' . esc_html__( 'Important', 'enwikuna-license-manager-client' ) . ':</strong>&nbsp;' . wp_kses_post( $response->upgrade_notice );
		}
	}
}
