<?php

namespace WeLabs\Rfqtest;

/**
 * Rfqtest class
 *
 * @class Rfqtest The class that holds the entire Rfqtest plugin
 */
final class Rfqtest {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '0.0.1';

    /**
     * Instance of self
     *
     * @var Rfqtest
     */
    private static $instance = null;

    /**
     * Holds various class instances
     *
     * @since 2.6.10
     *
     * @var array
     */
    private $container = [];

    /**
     * Constructor for the Rfqtest class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( RFQTEST_FILE, [ $this, 'activate' ] );
        register_deactivation_hook( RFQTEST_FILE, [ $this, 'deactivate' ] );

        add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
        add_action( 'woocommerce_flush_rewrite_rules', [ $this, 'flush_rewrite_rules' ] );
    }

    /**
     * Initializes the Rfqtest() class
     *
     * Checks for an existing Rfqtest instance
     * and if it doesn't find one then create a new one.
     *
     * @return Rfqtest
     */
    public static function init() {
        if ( self::$instance === null ) {
			self::$instance = new self();
		}

        return self::$instance;
    }

    /**
     * Magic getter to bypass referencing objects
     *
     * @since 2.6.10
     *
     * @param string $prop
     *
     * @return Class Instance
     */
    public function __get( $prop ) {
		if ( array_key_exists( $prop, $this->container ) ) {
            return $this->container[ $prop ];
		}
    }

    /**
     * Placeholder for activation function
     *
     * Nothing is being called here yet.
     */
    public function activate() {
        // Rewrite rules during rfqtest activation
        if ( $this->has_woocommerce() ) {
            $this->flush_rewrite_rules();
        }
    }

    /**
     * Flush rewrite rules after rfqtest is activated or woocommerce is activated
     *
     * @since 3.2.8
     */
    public function flush_rewrite_rules() {
        // fix rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {     }

    /**
     * Define all constants
     *
     * @return void
     */
    public function define_constants() {
        defined( 'RFQTEST_PLUGIN_VERSION' ) || define( 'RFQTEST_PLUGIN_VERSION', $this->version );
        defined( 'RFQTEST_DIR' ) || define( 'RFQTEST_DIR', dirname( RFQTEST_FILE ) );
        defined( 'RFQTEST_INC_DIR' ) || define( 'RFQTEST_INC_DIR', RFQTEST_DIR . '/includes' );
        defined( 'RFQTEST_TEMPLATE_DIR' ) || define( 'RFQTEST_TEMPLATE_DIR', RFQTEST_DIR . '/templates' );
        defined( 'RFQTEST_PLUGIN_ASSET' ) || define( 'RFQTEST_PLUGIN_ASSET', plugins_url( 'assets', RFQTEST_FILE ) );
        defined( 'RFQTEST_PLUGIN_ADMIN_ASSET' ) || define( 'RFQTEST_PLUGIN_ADMIN_ASSET' , RFQTEST_PLUGIN_ASSET . '/admin' );
        defined( 'RFQTEST_PLUGIN_PUBLIC_ASSET' ) || define( 'RFQTEST_PLUGIN_PUBLIC_ASSET' , RFQTEST_PLUGIN_ASSET . '/public' );

        // give a way to turn off loading styles and scripts from parent theme
        defined( 'RFQTEST_LOAD_STYLE' ) || define( 'RFQTEST_LOAD_STYLE', true );
        defined( 'RFQTEST_LOAD_SCRIPTS' ) || define( 'RFQTEST_LOAD_SCRIPTS', true );
    }

    /**
     * Load the plugin after WP User Frontend is loaded
     *
     * @return void
     */
    public function init_plugin() {
        $this->includes();
        $this->init_hooks();

        do_action( 'rfqtest_loaded' );
    }

    /**
     * Initialize the actions
     *
     * @return void
     */
    public function init_hooks() {
        // initialize the classes
        add_action( 'init', [ $this, 'init_classes' ], 4 );
        add_action( 'plugins_loaded', [ $this, 'after_plugins_loaded' ] );

        // Add currency switching hook
        add_action( 'template_redirect', [ '\WeLabs\Rfqtest\Helper', 'maybe_switch_currency_by_location' ] );
    }

    /**
     * Include all the required files
     *
     * @return void
     */
    public function includes() {
        // include_once STUB_PLUGIN_DIR . '/functions.php';
    }

    /**
     * Init all the classes
     *
     * @return void
     */
    public function init_classes() {
        $this->container['scripts'] = new Assets();
        $this->container['rfqtest_2'] = new RFQTest2();
    }

    /**
     * Executed after all plugins are loaded
     *
     * At this point rfqtest Pro is loaded
     *
     * @since 2.8.7
     *
     * @return void
     */
    public function after_plugins_loaded() {
        // Initiate background processes and other tasks
    }

    /**
     * Check whether woocommerce is installed and active
     *
     * @since 2.9.16
     *
     * @return bool
     */
    public function has_woocommerce() {
        return class_exists( 'WooCommerce' );
    }

    /**
     * Check whether woocommerce is installed
     *
     * @since 3.2.8
     *
     * @return bool
     */
    public function is_woocommerce_installed() {
        return in_array( 'woocommerce/woocommerce.php', array_keys( get_plugins() ), true );
    }

    /**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', RFQTEST_FILE ) );
	}

    /**
     * Get the template file path to require or include.
     *
     * @param string $name
     * @return string
     */
    public function get_template( $name ) {
        $template = untrailingslashit( RFQTEST_TEMPLATE_DIR ) . '/' . untrailingslashit( $name );

        return apply_filters( 'rfqtest_template', $template, $name );
    }
}
