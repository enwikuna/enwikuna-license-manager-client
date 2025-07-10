<?php
/**
 * Plugin Name: Enwikuna License Manager Client
 * Description: Will help customers to manage their licenses provided by the Enwikuna License Manager and receive automatic updates via releases.
 * Version: 1.0.1
 * Author: Enwikuna
 * Author URI: https://www.enwikuna.de
 * Plugin URI: https://github.com/enwikuna/enwikuna-license-manager-client
 * Text Domain: enwikuna-license-manager-client
 * Domain Path: /i18n/languages/
 * Requires at least: 5.4.0
 * Requires PHP: 7.4.0
 * Tested up to: 6.7.2
 *
 * @package Enwikuna\Enwikuna_License_Manager_Client
 */

defined( 'ABSPATH' ) || exit;

use Enwikuna\Enwikuna_License_Manager_Client\ELMC;

// Needed constants for main class initialization only
if ( ! defined( 'ELMC_PLUGIN_FILE' ) ) {
	define( 'ELMC_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'ELMC_ABSPATH' ) ) {
	define( 'ELMC_ABSPATH', realpath( plugin_dir_path( ELMC_PLUGIN_FILE ) ) . DIRECTORY_SEPARATOR );
}

if ( ! defined( 'ELMC_COMPANY_NAME' ) ) {
	define( 'ELMC_COMPANY_NAME', 'You Company' ); // Required: Define your company name here! It will be used in the plugin
}

if ( ! defined( 'ELMC_REST_API_URL' ) ) {
	define( 'ELMC_REST_API_URL', 'https://your-page.com/wp-json/elm/v1/' ); // Required: Define your own REST API URL here
}

if ( ! defined( 'ELMC_REST_API_KEY' ) ) {
	define( 'ELMC_REST_API_KEY', 'ck_xxx' ); // Required: Define your REST API key here (Enwikuna License Manager > Settings > REST API > Keys)
}

if ( ! defined( 'ELMC_REST_API_SECRET' ) ) {
	define( 'ELMC_REST_API_SECRET', 'cs_xxx' ); // Required: Define your REST API secret here (Enwikuna License Manager > Settings > REST API > Keys)
}

if ( ! defined( 'ELMC_PRODUCT_UUID' ) ) {
	define( 'ELMC_PRODUCT_UUID', '00000000-0000-0000-0000-000000000000' ); // Required: Define the product UUID of the Enwikuna License Manager Client product here (WooCommerce > Products > Enwikuna License Manager > License > Universally unique identifier (UUID))
}

if ( ! defined( 'ELMC_TRUSTED_SIGNING_KEY' ) ) {
	define( 'ELMC_TRUSTED_SIGNING_KEY', '' ); // Optional: Define here the trusted signing key if you want to use signed releases (Enwikuna License Manager > Settings > REST API > Product download public key)
}
// End of needed constants for main class initialization

if ( file_exists( ELMC_ABSPATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php' ) ) {
	require ELMC_ABSPATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
}

ELMC::get_instance();
