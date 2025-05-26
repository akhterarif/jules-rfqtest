<?php

namespace WeLabs\Rfqtest;

use WeDevs\DokanPro\Modules\RequestForQuotation\Helper as RFQHelper;

class Helper {

    public static function get_template( $template_name ) {
        $template_name = untrailingslashit( RFQTEST_TEMPLATE_DIR ) . '/' . untrailingslashit( $template_name );
        return $template_name ? $template_name : null;
    }

    public static function get_product_ids_from_quote_details_by_quote_id( $quote_id ) {
        $quote_details = RFQHelper::get_request_quote_details_by_quote_id( $quote_id );

        error_log( 'quote_details: ' . print_r( $quote_details, true ) );

        return $quote_details;



        // $product_ids = [];

        // if ( ! empty( $quote_details ) ) {
        //     foreach ( $quote_details as $detail ) {
        //         $product_ids[] = $detail->get_product_id();
        //     }
        // }

        // return $product_ids;
        
    }

    /**
     * Switches currency based on user's location if configured in WCML.
     *
     * This function checks if WooCommerce and WCML are active, retrieves the user's country,
     * and then looks up WCML's currency settings to see if a specific currency is
     * configured for that country. If found, and it's different from the current
     * active currency, it sets the new currency using WCML's functions.
     */
    public static function maybe_switch_currency_by_location() {
        // Check if WooCommerce is active
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }

        // Check if WCML is active
        // Ensure is_plugin_active() is available, as it's an admin function.
        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if ( ! is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
            return;
        }

        // Ensure WCML main class is loaded and available
        global $woocommerce_wpml;
        if ( ! isset( $woocommerce_wpml ) || ! property_exists( $woocommerce_wpml, 'settings' ) || ! property_exists( $woocommerce_wpml->multi_currency, 'currencies' ) ) {
            return;
        }

        // Get user's country code
        $user_country = WC()->customer ? WC()->customer->get_country() : null;

        if ( empty( $user_country ) ) {
            return;
        }

        $current_currency = get_woocommerce_currency();
        $target_currency  = null;

        // Retrieve WCML's currency settings.
        // These settings are typically configured in:
        // WP Admin -> WooCommerce -> WooCommerce Multilingual & Multicurrency -> Multi currency (tab)
        // -> "Show currencies to visitors from specific countries" (section)
        $wcml_currency_options = isset( $woocommerce_wpml->settings['currency_options'] ) ? $woocommerce_wpml->settings['currency_options'] : [];

        if ( empty( $wcml_currency_options ) ) {
            return;
        }
        
        foreach ( $wcml_currency_options as $currency_code => $options ) {
            // Check if the currency is enabled for specific countries ('by_location')
            // and if the user's country is in the list of allowed countries.
            // The exact key for 'location_mode' and 'countries' might vary slightly based on WCML version,
            // 'currency_countries' or 'location_mode_remote_countries' are common.
            // We are looking for settings where specific countries are *included* for a currency.
            
            $is_enabled_for_country = false;

            if ( isset( $options['location_mode'] ) ) {
                if ( $options['location_mode'] === 'by_location' || $options['location_mode'] === 'include' ) { // 'by_location' is a common value
                    // Check the key that holds the list of countries for this currency.
                    // Common keys: 'currency_countries', 'location_mode_remote_countries'.
                    $countries_for_currency = [];
                    if ( isset( $options['currency_countries'] ) && is_array( $options['currency_countries'] ) ) {
                        $countries_for_currency = $options['currency_countries'];
                    } elseif ( isset( $options['location_mode_remote_countries'] ) && is_array( $options['location_mode_remote_countries'] ) ) {
                        $countries_for_currency = $options['location_mode_remote_countries'];
                    }

                    if ( in_array( $user_country, $countries_for_currency ) ) {
                        $is_enabled_for_country = true;
                    }
                }
                // Add handling for 'exclude' if necessary, though the primary goal is to find a *positive* match.
                // else if ( $options['location_mode'] === 'exclude' ) {
                //     $excluded_countries = [];
                //     if ( isset( $options['location_mode_remote_excluded_countries'] ) && is_array( $options['location_mode_remote_excluded_countries'] ) ) {
                //         $excluded_countries = $options['location_mode_remote_excluded_countries'];
                //     }
                //     if ( !in_array( $user_country, $excluded_countries ) ) {
                //         // If the currency is available for all *except* certain countries, and the user's country is NOT excluded.
                //         // This logic can become complex if multiple currencies match this rule.
                //         // For simplicity, we prioritize direct 'include' rules.
                //     }
                // }
            }


            if ( $is_enabled_for_country ) {
                // Check if this currency code is actually an active currency in WCML
                if ( isset( $woocommerce_wpml->multi_currency->currencies[ $currency_code ] ) ) {
                    $target_currency = $currency_code;
                    break; // Found the first matching currency
                }
            }
        }

        if ( $target_currency && $target_currency !== $current_currency ) {
            // Use WCML's action to set the client currency
            do_action( 'wcml_set_client_currency', $target_currency );
        }
    }
}
