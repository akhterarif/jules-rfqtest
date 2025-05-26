<?php
/**
 * Get the user's country code using WooCommerce.
 *
 * This function prioritizes billing/shipping information if available,
 * then falls back to IP-based geolocation if enabled in WooCommerce settings.
 */
function get_woocommerce_user_country() {
    if ( class_exists( 'WooCommerce' ) && WC()->customer ) {
        $country = WC()->customer->get_country(); // Tries billing, then shipping, then base/geolocated

        // If the country is empty, and you want to force IP geolocation (if not already done by WC()->customer->get_country())
        // Ensure WooCommerce geolocation is enabled: WooCommerce > Settings > General > Default customer location
        if ( empty( $country ) && class_exists( 'WC_Geolocation' ) ) {
            $geolocation_data = WC_Geolocation::geolocate_ip();
            if ( ! empty( $geolocation_data['country'] ) ) {
                $country = $geolocation_data['country'];
            }
        }
        
        // Fallback to WooCommerce base country if still no country found
        if ( empty( $country ) ) {
            $country = WC()->countries->get_base_country();
        }

        return $country; // Returns ISO 3166-1 alpha-2 code (e.g., 'US', 'GB')
    }
    return null; // WooCommerce is not active or customer object not available
}

// Example usage:
$user_country = get_woocommerce_user_country();
if ( $user_country ) {
    echo 'User Country Code: ' . $user_country;
} else {
    echo 'Could not determine user country or WooCommerce is not active.';
}

/**
 * To explicitly get the country from IP geolocation (even if billing/shipping is set)
 */
function get_ip_geolocated_country() {
    if ( class_exists( 'WooCommerce' ) && class_exists( 'WC_Geolocation' ) ) {
        // Note: geolocate_ip() can take an IP address as an argument.
        // If no IP is provided, it uses the current user's IP.
        $location = WC_Geolocation::geolocate_ip(); 
        return isset( $location['country'] ) ? $location['country'] : null;
    }
    return null;
}

// Example usage for IP geolocation:
// $ip_country = get_ip_geolocated_country();
// if ( $ip_country ) {
//     echo 'User IP Geolocation Country Code: ' . $ip_country;
// } else {
//     echo 'Could not determine user country via IP geolocation or WooCommerce is not active.';
// }

?>
