<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin\Products\List_Tables;

defined( 'ABSPATH' ) || exit;

use ELMC_License_Status_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Product_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Products\List_Tables\Interfaces\ELMC_Admin_Products_List_Table_Interface;
use Enwikuna\Enwikuna_License_Manager_Client\ELMC;
use Enwikuna\Enwikuna_License_Manager_Client\Gateways\ELMC_ELM_Gateway;
use WP_List_Table;

/**
 * Class ELMC_Admin_Products_List_Table
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin\Products\List_Tables
 */
class ELMC_Admin_Products_List_Table extends WP_List_Table implements ELMC_Admin_Products_List_Table_Interface {

	/**
	 * Columns to show (name => label)
	 *
	 * @var array
	 */
	private array $columns;

	/**
	 * ELMC_Admin_Products_List_Table constructor
	 */
	public function __construct() {
		$this->columns = array(
			'name'         => esc_html__( 'Product', 'enwikuna-license-manager-client' ),
			'version'      => esc_html__( 'Version', 'enwikuna-license-manager-client' ),
			'expires'      => esc_html__( 'Support & Updates until', 'enwikuna-license-manager-client' ),
			'note'         => esc_html__( 'Note', 'enwikuna-license-manager-client' ),
			'setup_wizard' => esc_html__( 'Setup Wizard', 'enwikuna-license-manager-client' ),
			'license'      => esc_html__( 'License', 'enwikuna-license-manager-client' ),
		);

		parent::__construct(
			array(
				'singular' => 'product',
				'plural'   => 'products',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Renders the table list, we override the original class to render the table inside a form
	 * and to render any needed HTML (like the search box). By doing so the callee of a function can simple
	 * forget about any extra HTML
	 */
	public function display_table(): void {
		echo '<form id="' . esc_attr( $this->_args['plural'] ) . '-filter" method="post">';

		foreach ( $_GET as $key => $value ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( is_array( $value ) ) {
				foreach ( $value as $v ) {
					echo '<input type="hidden" name="' . esc_attr( elmc_clean( $key ) ) . '[]" value="' . esc_attr( elmc_clean( $v ) ) . '" />';
				}
			} else {
				echo '<input type="hidden" name="' . esc_attr( elmc_clean( $key ) ) . '" value="' . esc_attr( elmc_clean( $value ) ) . '" />';
			}
		}

		$this->display();

		$has_unregisted = false;

		if ( ! empty( $this->items ) ) {
			foreach ( $this->items as $product ) {
				/**
				 * @var ELMC_Product_Abstract $product
				 */
				if ( ! $product->is_registered() ) {
					$has_unregisted = true;
				}
			}
		}

		if ( ! $has_unregisted ) {
			$other_attributes = array(
				'disabled' => 'disabled',
			);
		} else {
			$other_attributes = null;
		}

		submit_button( esc_html__( 'Register products', 'enwikuna-license-manager-client' ), 'button-primary', 'register', true, $other_attributes );
		wp_nonce_field( 'elmc-register', 'elmc-register-nonce' );
		echo '</form>';
	}

	/**
	 * Prepares the list table items and arguments
	 */
	public function prepare_items(): void {
		$this->prepare_column_headers();

		$products       = ELMC::get_instance()->get_products( false );
		$total_products = count( $products );

		if ( ! empty( $products ) ) {
			$this->items = $products;
		}

		$this->set_pagination_args(
			array(
				'total_items' => $total_products,
				'per_page'    => $total_products,
			)
		);
	}

	/**
	 * Returns the columns for the products list table
	 *
	 * @return array
	 */
	public function get_columns(): array {
		return $this->columns;
	}

	/**
	 * Returns an array of CSS class names for the table
	 *
	 * @return array
	 */
	public function get_table_classes(): array {
		/**
		 * This filter filters the admin products table classes
		 *
		 * @param array $table_classes Includes all table classes
		 *
		 * @wp-hook elmc_admin_products_list_table_get_table_classes
		 * @since 1.0.0
		 */
		return apply_filters( 'elmc_admin_products_list_table_get_table_classes', array( 'widefat', 'striped', $this->_args['plural'] ) );
	}

	/**
	 * Prepare and outputs content for the name column
	 *
	 * @param ELMC_Product_Abstract $product
	 */
	public function column_name( ELMC_Product_Abstract $product ): void {
		$product_name = $product->get_name();
		$status       = $product->get_status();

		if ( ELMC_License_Status_Abstract::LOCKED === $status ) {
			$product_name .= ' &#9888;';
		}

		echo wp_kses_post( wpautop( '<strong title="' . esc_attr( $product->get_file() ) . '">' . $product_name . '</strong>' ) );
		echo '<span class="host">' . esc_html( $product->get_host() ) . '</span>';
	}

	/**
	 * Prepare and outputs content for the version column
	 *
	 * @param ELMC_Product_Abstract $product
	 */
	public function column_version( ELMC_Product_Abstract $product ): void {
		$update_info     = ELMC_ELM_Gateway::get_instance()->update_info( $product );
		$current_version = $product->get_version();
		$status          = 'latest';

		if ( ! empty( $update_info ) && ! empty( $update_info->new_version ) && version_compare( $update_info->new_version, $current_version, '>' ) ) {
			$update_url  = is_multisite() ? network_admin_url( 'update-core.php' ) : admin_url( 'update-core.php' );
			$status      = 'old';
			$new_version = esc_html__( 'Newest version:', 'enwikuna-license-manager-client' ) . ' <span class="version version-latest">' . esc_html( $update_info->new_version ) . '</span>';

			if ( ! $product->has_expired() ) {
				$new_version .= '<br/><a class="button button-secondary" href="' . $update_url . '">' . esc_html__( 'Check for updates', 'enwikuna-license-manager-client' ) . '</a>';
			}
		}

		echo '<span class="version version-' . esc_attr( $status ) . '">' . esc_html( $current_version ) . '</span>';

		if ( ! empty( $new_version ) ) {
			echo wp_kses_post( $new_version );
		}
	}

	/**
	 * Prepare and outputs content for the expires column
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	public function column_expires( ELMC_Product_Abstract $product ): string {
		if ( $product->get_expiration_date() ) {
			if ( $product->has_expired() ) {
				return '<a href="' . $product->get_renewal_url() . '" class="button button-primary" target="_blank">' . esc_html__( 'Renew now', 'enwikuna-license-manager-client' ) . '</a>';
			}

			return $product->get_expiration_date();
		}

		return '–';
	}

	/**
	 * Prepare and outputs content for the note column
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	public function column_note( ELMC_Product_Abstract $product ): string {
		$status = $product->get_status();

		if ( ELMC_License_Status_Abstract::LOCKED === $status ) {
			return esc_html__( 'Your license has been locked! Please contact our support for further information.', 'enwikuna-license-manager-client' );
		}

		return '–';
	}

	/**
	 * Prepare and outputs content for the setup wizard column
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	public function column_setup_wizard( ELMC_Product_Abstract $product ): string {
		if ( ! empty( $product->get_setup_wizard() ) ) {
			return '<a href="' . admin_url( 'admin.php?page=' . $product->get_setup_wizard() ) . '" class="button button-primary" target="_blank">' . esc_html__( 'Setup Wizard', 'enwikuna-license-manager-client' ) . '</a>';
		}

		return '–';
	}

	/**
	 * Prepare and outputs content for the license column
	 *
	 * @param ELMC_Product_Abstract $product
	 *
	 * @return string
	 */
	public function column_license( ELMC_Product_Abstract $product ): string {
		$output = '';

		if ( $product->is_registered() ) {
			$license_key = $product->get_license_key();

			if ( ! empty( $license_key ) ) {
				$output = '<code class="license-key">' . esc_html( $license_key ) . '</code><br>';
			}

			$unregister_url = add_query_arg(
				array(
					'page'   => 'elmc',
					'action' => 'unregister',
					'file'   => $product->get_file(),
				),
				admin_url( 'index.php' )
			);

			$unregister_url = wp_nonce_url( $unregister_url, 'elmc-unregister', 'elmc-unregister-nonce' );

			$output .= '<a class="button button-secondary" href="' . esc_url( $unregister_url ) . '">' . esc_html__( 'Deactivate', 'enwikuna-license-manager-client' ) . '</a>';
		} else {
			$output = '<input name="products_to_register[' . esc_attr( $product->get_file() ) . ']" id="product-' . esc_attr( $product->get_file() ) . '" type="text" value="" style="width: 100%" aria-required="true" placeholder="' . esc_attr( esc_html__( 'Enter license key', 'enwikuna-license-manager-client' ) ) . '" /><br/>';
		}

		return $output;
	}

	/**
	 * Outputs a message when there are no items to show in the table
	 */
	public function no_items(): void {
		/**
		 * This filter filters the admin products list table no items message
		 *
		 * @param string $message The no items message
		 *
		 * @wp-hook elmc_admin_products_list_table_no_items
		 * @since 1.0.0
		 */
		apply_filters( 'elmc_admin_products_list_table_no_items', esc_html_e( 'No products found.', 'enwikuna-license-manager-client' ) );
	}

	/**
	 * Prepares the _column_headers property which is used by WP_Table_List at rendering
	 */
	private function prepare_column_headers(): void {
		$this->screen->_column_headers = array( $this->get_columns(), get_hidden_columns( $this->screen ), $this->get_sortable_columns() );
	}
}
