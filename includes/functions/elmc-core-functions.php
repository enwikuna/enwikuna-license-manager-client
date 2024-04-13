<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'elmc_get_template' ) ) {
	/**
	 * Includes a template
	 *
	 * @param string $template
	 * @param array $args
	 */
	function elmc_get_template( string $template, array $args = array() ): void {
		if ( ! file_exists( $template ) ) {
			_doing_it_wrong( __FUNCTION__, wp_kses_post( sprintf( __( '%s does not exist.', 'enwikuna-license-manager-client' ), '<code>' . $template . '</code>' ) ), '1.0.0' );

			return;
		}

		if ( ! empty( $args ) && is_array( $args ) ) {
			foreach ( $args as $key => $value ) {
				$$key = $value;
			}
		}

		/**
		 * This hook fires before a template gets loaded
		 *
		 * @param string $template The template
		 * @param string $args The args for the template
		 *
		 * @wp-hook elmc_before_get_template
		 * @since 1.0.0
		 */
		do_action( 'elmc_before_get_template', $template, $args );

		include $template;

		/**
		 * This hook fires after a template was loaded
		 *
		 * @param string $template The template
		 * @param string $args The args for the template
		 *
		 * @wp-hook elmc_after_get_template
		 * @since 1.0.0
		 */
		do_action( 'elmc_after_get_template', $template, $args );
	}
}

if ( ! function_exists( 'elmc_get_option' ) ) {
	/**
	 * Handles option returning with plugin prefix
	 *
	 * @param string $option
	 * @param mixed $default
	 *
	 * @return string|array
	 */
	function elmc_get_option( string $option, $default = false ) {
		return get_option( 'elmc_' . $option, $default );
	}
}

if ( ! function_exists( 'elmc_delete_option' ) ) {
	/**
	 * Handles option deletion with plugin prefix
	 *
	 * @param string $option
	 */
	function elmc_delete_option( string $option ) {
		delete_option( 'elmc_' . $option );
	}
}

if ( ! function_exists( 'elmc_update_option' ) ) {
	/**
	 * Handles option update with plugin prefix
	 *
	 * @param string $option
	 * @param mixed $value
	 *
	 * @return bool
	 */
	function elmc_update_option( string $option, $value ): bool {
		return update_option( 'elmc_' . $option, $value );
	}
}

if ( ! function_exists( 'elmc_is_action' ) ) {
	/**
	 * Returns the current page action
	 *
	 * @param string $action
	 *
	 * @return bool
	 */
	function elmc_is_action( string $action ): bool {
		$is_action = false;

		if ( isset( $_GET['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$get_action = elmc_clean( wp_unslash( $_GET['action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $action === $get_action ) {
				$is_action = true;
			}
		} elseif ( isset( $_POST[ $action ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$is_action = true;
		}

		return $is_action;
	}
}

if ( ! function_exists( 'elmc_set_transient' ) ) {
	/**
	 * Handles transient set with plugin prefix
	 *
	 * @param string $transient
	 * @param mixed $value
	 * @param int $expiration
	 *
	 * @return bool
	 */
	function elmc_set_transient( string $transient, $value, int $expiration = 0 ): bool {
		return set_transient( 'elmc_' . $transient, $value, $expiration );
	}
}

if ( ! function_exists( 'elmc_get_transient' ) ) {
	/**
	 * Handles transient returning with plugin prefix
	 *
	 * @param string $transient
	 *
	 * @return mixed
	 */
	function elmc_get_transient( string $transient ) {
		return get_transient( 'elmc_' . $transient );
	}
}

if ( ! function_exists( 'elmc_delete_transient' ) ) {
	/**
	 * Handles transient deletion with plugin prefix
	 *
	 * @param string $transient
	 */
	function elmc_delete_transient( string $transient ) {
		delete_transient( 'elmc_' . $transient );
	}
}

if ( ! function_exists( 'elmc_str_to_bool' ) ) {
	/**
	 * Converts string to bool
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	function elmc_str_to_bool( string $value ): bool {
		return (bool) filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}
}

if ( ! function_exists( 'elmc_clean' ) ) {
	/**
	 * Clean variables using sanitize_text_field - arrays are cleaned recursively
	 *
	 * @param string|array $var
	 *
	 * @return string|array
	 */
	function elmc_clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'elmc_clean', $var );
		}

		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}

if ( ! function_exists( 'elmc_sanitize_array_data' ) ) {
	/**
	 * Sanitize array data
	 *
	 * @param array $array
	 * @param bool $wp_unslash
	 *
	 * @return array
	 */
	function elmc_sanitize_array_data( array $array, bool $wp_unslash = true ): array {
		if ( empty( $array ) ) {
			return $array;
		}

		foreach ( $array as $index => $entry ) {
			if ( $wp_unslash ) {
				$array[ $index ] = wp_unslash( $entry );
			}

			$array[ $index ] = elmc_clean( $array[ $index ] );

			if ( is_numeric( $entry ) && 0 !== strpos( $entry, '0' ) ) {
				$array[ $index ] = absint( $array[ $index ] );
			}
		}

		return $array;
	}
}

if ( ! function_exists( 'elmc_get_current_tab' ) ) {
	/**
	 * Returns the current tab
	 *
	 * @param string $default
	 *
	 * @return string
	 */
	function elmc_get_current_tab( string $default = '' ): string {
		if ( isset( $_GET['tab'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$tab = elmc_clean( wp_unslash( $_GET['tab'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		$current_tab = empty( $tab ) ? $default : $tab;

		/**
		 * This filter filters the current page tab
		 *
		 * @param string $current_tab The current tab
		 *
		 * @wp-hook elmc_get_current_tab
		 * @since 1.0.0
		 */
		return apply_filters( 'elmc_get_current_tab', $current_tab );
	}
}

if ( ! function_exists( 'elmc_prepare_license_host_url' ) ) {
	/**
	 * Prepare single license host URL
	 *
	 * @param string $host
	 *
	 * @return string
	 */
	function elmc_prepare_license_host_url( string $host ): string {
		$host = trim( $host );
		$host = preg_replace( '#^http(s)?://#', '', $host );
		$host = preg_replace( '/^www\./', '', $host );
		$host = preg_replace( '{/$}', '', $host );

		/**
		 * This filter filters the prepared license host
		 *
		 * @param string $host The prepared license host URL
		 *
		 * @wp-hook elmc_prepare_license_host_url
		 * @since 1.0.0
		 */
		return apply_filters( 'elmc_prepare_license_host_url', $host );
	}
}
