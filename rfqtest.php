<?php
/**
 * Plugin Name: Rfqtest
 * Plugin URI:  https://welabs.dev
 * Description: rfqtest
 * Version: 0.0.1
 * Author: weLabs
 * Author URI: https://welabs.dev
 * Text Domain: rfqtest
 * WC requires at least: 5.0.0
 * Domain Path: /languages/
 * Requires Plugins: woocommerce, dokan-lite, dokan-pro
 * License: GPL2
 */
use WeLabs\Rfqtest\Rfqtest;

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'RFQTEST_FILE' ) ) {
    define( 'RFQTEST_FILE', __FILE__ );
}

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Load Rfqtest Plugin when all plugins loaded
 *
 * @return \WeLabs\Rfqtest\Rfqtest
 */
function welabs_rfqtest() {
    return Rfqtest::init();
}

// Lets Go....
welabs_rfqtest();
