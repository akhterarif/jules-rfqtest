<?php
/**
 * Check if WooCommerce Multilingual (WCML) is active.
 *
 * WCML's main file path can vary, but a common one is 'woocommerce-multilingual/wpml-woocommerce.php'.
 * If that doesn't work, the slug might be different, or the plugin might not be installed.
 */
function is_wcml_active() {
    // Ensure is_plugin_active() function is available. This is usually available in WordPress admin areas.
    // If using it in frontend, it might need to be included.
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    // Common path for WCML. This might need adjustment if the installation uses a different slug/path.
    $wcml_plugin_path = 'woocommerce-multilingual/wpml-woocommerce.php';

    return is_plugin_active( $wcml_plugin_path );
}

// Example usage:
if ( is_wcml_active() ) {
    echo 'WooCommerce Multilingual is active.';
} else {
    echo 'WooCommerce Multilingual is not active.';
}
?>
