<?php

namespace Enwikuna\Enwikuna_License_Manager_Client\Admin;

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Abstracts\ELMC_Singleton_Abstract;
use Enwikuna\Enwikuna_License_Manager_Client\Admin\Interfaces\ELMC_Admin_Notices_Interface;
use Enwikuna\Enwikuna_License_Manager_Client\ELMC;
use Enwikuna\Enwikuna_License_Manager_Client\ELMC_Cron_Jobs;
use Enwikuna\Enwikuna_License_Manager_Client\ELMC_Dependencies;
use Enwikuna\Enwikuna_License_Manager_Client\Gateways\ELMC_ELM_Gateway;

/**
 * Class ELMC_Admin_Notices
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client\Admin
 */
class ELMC_Admin_Notices extends ELMC_Singleton_Abstract implements ELMC_Admin_Notices_Interface {

	/**
	 * Info notice type
	 *
	 * @var string
	 */
	public const INFO = 'info';

	/**
	 * Success notice type
	 *
	 * @var string
	 */
	public const SUCCESS = 'success';

	/**
	 * Warning notice type
	 *
	 * @var string
	 */
	public const WARNING = 'warning';

	/**
	 * Error notice type
	 *
	 * @var string
	 */
	public const ERROR = 'error';

	/**
	 * Admin notices
	 *
	 * @var array
	 */
	private array $admin_notices;

	/**
	 * The user ID who triggered an admin notice
	 *
	 * @var int
	 */
	private int $user_id = 0;

	/**
	 * ELMC_Admin_Notices constructor
	 */
	public function __construct() {
		if ( is_admin() ) {
			$this->admin_notices = elmc_get_option( 'admin_notices', array() );
			$this->user_id       = get_current_user_id();

			$this->init_hooks();
		}
	}

	/**
	 * Init / set up hooks
	 */
	private function init_hooks(): void {
		add_action( 'wp_loaded', array( $this, 'wp_loaded_action' ) );

		if ( is_multisite() && is_network_admin() ) {
			add_action( 'network_admin_notices', array( $this, 'admin_notices_action' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'admin_notices_action' ) );
		}
	}

	/**
	 * Handles adding of default plugin notices
	 */
	public function wp_loaded_action(): void {
		if ( ELMC_Dependencies::get_instance()->is_loadable() ) {
			$this->network_activation_required_notice();
			$this->connection_issue_notice();

			if ( ! is_multisite() || ( is_multisite() && is_blog_admin() ) ) {
				$this->products_expiring_notice();
				$this->product_expired_notice();
				$this->product_upgrade_notice();
				$this->updating_notice();
			}
		} else {
			$this->dependency_notice();
		}
	}

	/**
	 * Adds an admin info notice
	 *
	 * @param string $notice
	 * @param array $args
	 */
	public function info( string $notice, array $args = array() ): void {
		$this->add( $notice, self::INFO, $args );
	}

	/**
	 * Adds an admin success notice
	 *
	 * @param string $notice
	 * @param array $args
	 */
	public function success( string $notice, array $args = array() ): void {
		$this->add( $notice, self::SUCCESS, $args );
	}

	/**
	 * Adds an admin warning notice
	 *
	 * @param string $notice
	 * @param array $args
	 */
	public function warning( string $notice, array $args = array() ): void {
		$this->add( $notice, self::WARNING, $args );
	}

	/**
	 * Adds an admin error notice
	 *
	 * @param string $notice
	 * @param array $args
	 */
	public function error( string $notice, array $args = array() ): void {
		$this->add( $notice, self::ERROR, $args );
	}

	/**
	 * Adds a specific notice by counter
	 *
	 * @param string $notice
	 * @param int $successful_count
	 * @param int $total_count
	 * @param array $args
	 */
	public function counter( string $notice, int $successful_count, int $total_count, array $args = array() ): void {
		$this->add( $notice, $this->get_count_notice_type( $successful_count, $total_count ), $args );
	}

	/**
	 * Output admin notices
	 */
	public function admin_notices_action(): void {
		if ( ! empty( $this->admin_notices ) ) {
			$screen    = get_current_screen();
			$screen_id = $screen->id ?? '';

			foreach ( $this->admin_notices as $notice_key => $notice ) {
				if ( ! empty( $notice['args']['exclude_on'] ) && in_array( $screen_id, $notice['args']['exclude_on'], true ) ) {
					continue;
				}

				if ( ! empty( $notice['args']['include_on'] ) && ! in_array( $screen_id, $notice['args']['include_on'], true ) ) {
					continue;
				}

				$user_id = $notice['user_id'];

				if ( ! empty( $user_id ) && $user_id !== $this->user_id ) {
					continue;
				}

				$title       = '';
				$content     = $notice['content'];
				$type        = $notice['type'];
				$class       = ! empty( $notice['args']['class'] ) ? 'elmc-notice-' . $notice['args']['class'] : '';
				$attrs       = $notice['args']['attrs'] ?? array();
				$dismissible = $notice['args']['dismissible'] ? 'is-dismissible' : '';

				if ( ! empty( $notice['args']['title']['content'] ) ) {
					$title = '<' . $notice['args']['title']['size'] . ' style="margin: 1em 0;">' . esc_html( $notice['args']['title']['content'] ) . '</' . $notice['args']['title']['size'] . '>';
				}

				if ( $notice['args']['strong'] ) {
					$content = '<strong>' . wp_kses_post( $content ) . '</strong>';
				}
				?>
				<div class="notice elmc-notice <?php echo esc_attr( $class ); ?> notice-<?php echo esc_attr( $type ); ?> <?php echo esc_attr( $dismissible ); ?>" <?php echo wp_kses_post( implode( ' ', $attrs ) ); ?>>
					<?php echo wp_kses_post( $title ); ?>
					<p><?php echo wp_kses_post( $content ); ?></p>
					<?php
					if ( ! empty( $notice['args']['products'] ) ) {
						echo '<ol style="list-style-type: disc">';

						foreach ( $notice['args']['products'] as $product_name ) {
							echo '<li>' . esc_html( $product_name ) . '</li>';
						}

						echo '</ol>';
					}
					?>
				</div>
				<?php

				$this->remove_admin_notice( $notice_key );
			}
		}
	}

	/**
	 * Removes all admin notices
	 */
	public function remove_all_admin_notices(): void {
		elmc_delete_option( 'admin_notices' );
	}

	/**
	 * Add an admin notice
	 *
	 * @param string $admin_notice
	 * @param string $type
	 * @param array $args
	 */
	private function add( string $admin_notice, string $type, array $args ): void {
		$defaults = array(
			'title'       => array(
				'size'    => '',
				'content' => '',
			),
			'strong'      => false,
			'dismissible' => true,
		);

		// Use MD5 to be sure that only one message will be added in case there are multiple instances of the class
		$this->admin_notices[ md5( wp_strip_all_tags( $admin_notice ) ) ] = array(
			'user_id' => $this->user_id,
			'content' => $admin_notice,
			'type'    => $type,
			'args'    => wp_parse_args( $args, $defaults ),
		);

		$this->store_admin_notices();
	}

	/**
	 * Stores all admin notices
	 */
	private function store_admin_notices(): void {
		elmc_update_option( 'admin_notices', $this->admin_notices );
	}

	/**
	 * Removes a specific admin notice
	 *
	 * @param string $notice_key
	 */
	private function remove_admin_notice( string $notice_key ): void {
		unset( $this->admin_notices[ $notice_key ] );

		$this->store_admin_notices();
	}

	/**
	 * Returns the notice type
	 *
	 * @param int $successful_count
	 * @param int $total_count
	 *
	 * @return string
	 */
	private function get_count_notice_type( int $successful_count, int $total_count ): string {
		if ( $successful_count === $total_count ) {
			return self::SUCCESS;
		}

		if ( $successful_count > 0 && $successful_count < $total_count ) {
			return self::WARNING;
		}

		return self::ERROR;
	}

	/**
	 * Add notice if is multisite but plugin not activated for network
	 */
	private function network_activation_required_notice(): void {
		if ( is_multisite() && is_network_admin() && ! is_plugin_active_for_network( Constants::get_constant( 'ELMC_PLUGIN_BASENAME' ) ) ) {
			$args = array(
				'dismissible' => false,
			);

			$this->error( esc_html__( 'Enwikuna License Manager Client must be network activated when in multisite environment!', 'enwikuna-license-manager-client' ), $args );
		}
	}

	/**
	 * Add notice in case products are going to be expired
	 */
	private function products_expiring_notice(): void {
		$expiring_products = elmc_get_option( 'expiring_products', array() );
		$product_names     = array();
		$show_notice       = false;

		if ( ! empty( $expiring_products ) ) {
			foreach ( $expiring_products as $file => $expiring ) {
				$product = ELMC::get_instance()->get_product( $file );

				if ( ! empty( $product ) && $expiring && ! $product->has_expired() ) {
					$product_names[] = $product->get_name();
					$show_notice     = true;
				} else {
					unset( $expiring_products[ $file ] );
				}
			}
		}

		if ( ! $show_notice || elmc_get_option( 'dismiss_products_expiring_notice' ) ) {
			elmc_delete_option( 'expiring_products' );

			return;
		}

		$warning_notice = sprintf( esc_html__( 'It seems like the Updates & Support of one of your %1$s products will expire soon:', 'enwikuna-license-manager-client' ), Constants::get_constant( 'ELMC_COMPANY_NAME' ) );
		$args           = array(
			'title'    => array(
				'size'    => 'h3',
				'content' => esc_html__( 'Update & Support expires', 'enwikuna-license-manager-client' ),
			),
			'class'    => 'expiring-products',
			'products' => $product_names,
		);

		$this->warning( $warning_notice, $args );
	}

	/**
	 * Add notice in case a product is finally expired
	 */
	private function product_expired_notice(): void {
		$check_license_url = '<a href="' . esc_url( admin_url( 'index.php?page=elmc' ) ) . '">' . esc_html__( 'check', 'enwikuna-license-manager-client' ) . '</a>';
		$products          = ELMC::get_instance()->get_products( false );

		if ( ! empty( $products ) ) {
			foreach ( $products as $product ) {
				if ( ! $product->is_registered() ) {
					$error_notice = wp_kses_post( sprintf( __( 'Your <strong>%1$s</strong> license doesn\'t seem to be registered. Please %2$s your license.', 'enwikuna-license-manager-client' ), $product->get_name(), $check_license_url ) );
				} elseif ( $product->has_expired() ) {
					$error_notice = wp_kses_post( sprintf( __( 'Your <strong>%1$s</strong> license seems to have expired. Please %2$s your license.', 'enwikuna-license-manager-client' ), $product->get_name(), $check_license_url ) );
				}
			}
		}

		if ( ! empty( $error_notice ) ) {
			$args = array(
				'dismissible' => false,
				'exclude_on'  => $this->get_expired_notice_excluded_screen_ids(),
			);

			$this->error( $error_notice, $args );
		}
	}

	/**
	 * Returns a list of all screen ids where the expired notice for products should not be shown
	 *
	 * @return array
	 */
	private function get_expired_notice_excluded_screen_ids(): array {
		return wp_parse_args(
			ELMC_Admin::get_instance()->get_elmc_admin_screen_ids(),
			array(
				'index_page_elmc-network',
				'update-core-network',
				'update-core',
			)
		);
	}

	/**
	 * Add expired notice on upgrade screen in case a product is expired
	 */
	private function product_upgrade_notice(): void {
		$transient = get_site_transient( 'update_plugins' );
		$products  = ELMC::get_instance()->get_products( false );

		if ( ! empty( $products ) && ! empty( $transient ) && isset( $transient->response ) ) {
			foreach ( $transient->response as $file => $data ) {
				if ( isset( $products[ $file ] ) ) {
					$products[ $file ]->refresh_expiration_date();

					if ( $products[ $file ]->has_expired() ) {
						$elmc_page_url = is_multisite() ? network_admin_url( 'index.php?page=elmc' ) : admin_url( 'index.php?page=elmc' );
						$error_notice  = wp_kses_post( sprintf( __( 'Your <strong>%1$s</strong> license seems to have expired. Please %2$s your license before updating.', 'enwikuna-license-manager-client' ), $products[ $file ]->get_name(), '<a href="' . $elmc_page_url . '">' . esc_html__( 'check', 'enwikuna-license-manager-client' ) . '</a>' ) );
						$args          = array(
							'dismissible' => false,
							'class'       => 'update',
							'include_on'  => array( 'update-core' ),
							'attrs'       => array(
								'data-for-plugin="' . md5( $file ) . '"',
								'style="margin: 12px 0 0;"',
							),
						);

						$this->error( $error_notice, $args );
					}
				}
			}
		}
	}

	/**
	 * Handle admin notice when updating cron is running
	 */
	private function updating_notice(): void {
		if ( ELMC_Cron_Jobs::get_instance()->is_updating() ) {
			$cron_disabled = Constants::is_true( 'DISABLE_WP_CRON' );
			$notice        = esc_html__( 'Enwikuna License Manager Client is updating in the background. The update process may take a little while, so please be patient. Some functions may be not working correctly until the update is finished.', 'enwikuna-license-manager-client' );

			if ( $cron_disabled ) {
				$notice .= '<br><br>' . esc_html__( 'Note: WP CRON has been disabled on your install which may prevent this update from completing.', 'enwikuna-license-manager-client' );
			}

			$args = array(
				'title'       => array(
					'size'    => 'h3',
					'content' => esc_html__( 'Enwikuna License Manager Client is updating', 'enwikuna-license-manager-client' ),
				),
				'dismissible' => false,
			);

			$this->info( $notice, $args );
		} elseif ( elmc_str_to_bool( elmc_get_transient( 'updating' ) ) ) {
			$this->success( esc_html__( 'Enwikuna License Manager Client update completed. Thank you for updating to the latest version!', 'enwikuna-license-manager-client' ) );

			elmc_delete_transient( 'updating' );
		}
	}

	/**
	 * Handle admin notice for dependency issue
	 */
	private function dependency_notice(): void {
		$notice = wp_kses_post( __( '<strong>Enwikuna License Manager Client is inactive.</strong> The required configuration is missing for Enwikuna License Manager Client to work. Please contact our support for further assistance!', 'enwikuna-license-manager-client' ) );

		$args = array(
			'title'       => array(
				'size'    => 'h3',
				'content' => esc_html__( 'Enwikuna License Manager Client is not configured', 'enwikuna-license-manager-client' ),
			),
			'dismissible' => false,
		);

		$this->error( $notice, $args );
	}

	/**
	 * Handle admin notice for connection issue
	 */
	private function connection_issue_notice(): void {
		if ( ! ELMC_ELM_Gateway::get_instance()->is_connected() ) {
			$notice = sprintf( esc_html__( 'Enwikuna License Manager Client needs to connect to %1$s to check for new releases and security updates. Something in the network or security settings is preventing this. Please allow outgoing communication to %2$s to remove this notice.', 'enwikuna-license-manager-client' ), Constants::get_constant( 'ELMC_COMPANY_NAME' ), Constants::get_constant( 'ELMC_REST_API_URL' ) );

			$args = array(
				'title'       => array(
					'size'    => 'h3',
					'content' => sprintf( esc_html__( 'Enwikuna License Manager Client is unable to connect to %1$s', 'enwikuna-license-manager-client' ), Constants::get_constant( 'ELMC_COMPANY_NAME' ) ),
				),
				'include_on'  => array( 'plugins' ),
				'dismissible' => false,
			);

			$this->warning( $notice, $args );
		}
	}
}
