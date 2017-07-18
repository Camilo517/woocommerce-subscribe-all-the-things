<?php
/**
 * WCS_ATT_Core_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Subscribe All the Things
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC Core compatibility functions.
 *
 * @class    WCS_ATT_Core_Compatibility
 * @version  1.0.0
 */
class WCS_ATT_Core_Compatibility {

	/**
	 * Cache 'gte' comparison results.
	 * @var array
	 */
	private static $is_wc_version_gte = array();

	/**
	 * Cache 'gt' comparison results.
	 * @var array
	 */
	private static $is_wc_version_gt = array();

	/*
	|--------------------------------------------------------------------------
	| WC version getters.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Helper method to get the version of the currently installed WooCommerce
	 *
	 * @since  1.0.0
	 * @return string woocommerce version number or null
	 */
	private static function get_wc_version() {
		return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than or equal to $version.
	 *
	 * @since  2.0.0
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wc_version_gte( $version ) {
		if ( ! isset( self::$is_wc_version_gte[ $version ] ) ) {
			self::$is_wc_version_gte[ $version ] = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>=' );
		}
		return self::$is_wc_version_gte[ $version ];
	}

	/**
	 * Returns true if the installed version of WooCommerce is greater than $version.
	 *
	 * @since  1.0.0
	 *
	 * @param  string  $version
	 * @return boolean
	 */
	public static function is_wc_version_gt( $version ) {
		if ( ! isset( self::$is_wc_version_gt[ $version ] ) ) {
			self::$is_wc_version_gt[ $version ] = self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>' );
		}
		return self::$is_wc_version_gt[ $version ];
	}

	/*
	|--------------------------------------------------------------------------
	| Helpers.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Wrapper for 'get_parent_id' with fallback to 'get_id'.
	 *
	 * @since  2.0.0
	 *
	 * @param  WC_Product  $product
	 * @return mixed
	 */
	public static function get_product_id( $product ) {
		$parent_id = $product->get_parent_id();
		return $parent_id ? $parent_id : $product->get_id();
	}

	/*
	|--------------------------------------------------------------------------
	| Wrapper functions for backwards compatibility.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Back-compat wrapper for 'wc_get_price_including_tax'.
	 *
	 * @since  2.0.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function wc_get_price_including_tax( $product, $args ) {
		if ( self::is_wc_version_gte_2_7() ) {
			return wc_get_price_including_tax( $product, $args );
		} else {

			$qty   = isset( $args[ 'qty' ] ) ? $args[ 'qty' ] : 1;
			$price = isset( $args[ 'price' ] ) ? $args[ 'price' ] : '';

			return $product->get_price_including_tax( $qty, $price );
		}
	}

	/**
	 * Back-compat wrapper for 'wc_get_price_excluding_tax'.
	 *
	 * @since  2.0.0
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return mixed
	 */
	public static function wc_get_price_excluding_tax( $product, $args ) {
		if ( self::is_wc_version_gte_2_7() ) {
			return wc_get_price_excluding_tax( $product, $args );
		} else {

			$qty   = isset( $args[ 'qty' ] ) ? $args[ 'qty' ] : 1;
			$price = isset( $args[ 'price' ] ) ? $args[ 'price' ] : '';

			return $product->get_price_excluding_tax( $qty, $price );
		}
	}

	/**
	 * Back-compat wrapper for 'wc_get_price_to_display'.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $args
	 * @return double
	 */
	public static function wc_get_price_to_display( $product, $args = array() ) {

		if ( self::is_wc_version_gte_2_7() ) {
			return wc_get_price_to_display( $product, $args );
		} else {

			$price = isset( $args[ 'price' ] ) ? $args[ 'price' ] : '';
			$qty   = isset( $args[ 'qty' ] ) ? $args[ 'qty' ] : 1;

			return $product->get_display_price( $price, $qty );
		}
	}

	/**
	 * Back-compat wrapper for 'WC_Product_Factory::get_product_type'.
	 *
	 * @since  3.9.0
	 *
	 * @param  mixed  $product_id
	 * @return mixed
	 */
	public static function get_product_type( $product_id ) {
		$product_type = false;
		if ( $product_id ) {
			if ( self::is_wc_version_gte_2_7() ) {
				$product_type = WC_Product_Factory::get_product_type( $product_id );
			} else {
				$terms        = get_the_terms( $product_id, 'product_type' );
				$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
			}
		}
		return $product_type;
	}

	/**
	 * Display a WooCommerce help tip.
	 *
	 * @since  1.0.4
	 *
	 * @param  string $tip
	 * @return string
	 */
	public static function wc_help_tip( $tip ) {
		if ( self::is_wc_version_gte_2_5() ) {
			return wc_help_tip( $tip );
		} else {
			return '<img class="help_tip woocommerce-help-tip" data-tip="' . $tip . '" src="' . WC()->plugin_url() . '/assets/images/help.png" />';
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated.
	|--------------------------------------------------------------------------
	*/

	/**
	 * Returns true if the installed version of WooCommerce is 2.7 or greater.
	 *
	 * @since  1.1.2
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_7() {
		_deprecated_function( __METHOD__ . '()', '2.0.0', 'WCS_ATT_Core_Compatibility::is_wc_version_gte()' );
		return self::is_wc_version_gte( '2.7' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.6 or greater.
	 *
	 * @since  1.0.4
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_6() {
		_deprecated_function( __METHOD__ . '()', '2.0.0', 'WCS_ATT_Core_Compatibility::is_wc_version_gte()' );
		return self::is_wc_version_gte( '2.6' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.5 or greater.
	 *
	 * @since  1.0.4
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_5() {
		_deprecated_function( __METHOD__ . '()', '2.0.0', 'WCS_ATT_Core_Compatibility::is_wc_version_gte()' );
		return self::is_wc_version_gte( '2.5' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.4 or greater.
	 *
	 * @since  1.0.0
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_4() {
		_deprecated_function( __METHOD__ . '()', '2.0.0', 'WCS_ATT_Core_Compatibility::is_wc_version_gte()' );
		return self::is_wc_version_gte( '2.4' );
	}

	/**
	 * Returns true if the installed version of WooCommerce is 2.3 or greater.
	 *
	 * @since  1.0.0
	 * @return boolean
	 */
	public static function is_wc_version_gte_2_3() {
		_deprecated_function( __METHOD__ . '()', '2.0.0', 'WCS_ATT_Core_Compatibility::is_wc_version_gte()' );
		return self::is_wc_version_gte( '2.3' );
	}
}
