<?php

defined( 'ABSPATH' ) || exit;

use Automattic\Jetpack\Constants;
use Enwikuna\Enwikuna_License_Manager_Client\Gateways\ELMC_ELM_Gateway;

?>
<div class="wrap elmc">
	<div class="elmc-page-content">
		<div id="elmc-dashboard-header">
			<h1><?php echo esc_html( sprintf( __( 'Welcome to %1$s', 'enwikuna-license-manager-client' ), Constants::get_constant( 'ELMC_COMPANY_NAME' ) ) ); ?></h1>
			<div id="elmc-dashboard-about">
				<?php echo esc_html( sprintf( __( 'Easily manage your licenses for %1$s products and enjoy automatic updates.', 'enwikuna-license-manager-client' ), Constants::get_constant( 'ELMC_COMPANY_NAME' ) ) ); ?>
			</div>
		</div>
		<div id="elmc-dashboard-content">
			<?php
			if ( ELMC_ELM_Gateway::get_instance()->is_connected() ) {
				global $elmc_admin_products_list_table;

				$elmc_admin_products_list_table->display_table();
			} else {
				?>
				<div id="api-unavailable">
					<h3><?php esc_html_e( 'Oops, seems like the API needs a break', 'enwikuna-license-manager-client' ); ?></h3>
					<p><?php esc_html_e( 'Sorry but currently we are not able to establish a connection to our licensing system - please be patient and try again in a few minutes.', 'enwikuna-license-manager-client' ); ?></p>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
