<?php

namespace WeLabs\Rfqtest;

use WeDevs\DokanPro\Modules\RequestForQuotation\Helper as RFQHelper;
use WeDevs\DokanPro\Modules\RequestForQuotation\Model\Quote;

class RFQTest2 {
    /**
     * The constructor.
     */
    public function __construct() {
        add_filter( 'dokan_get_template_part', array( $this, 'override_quote_heading_template' ), 10, 3 );

        // add the product in the cart
        add_action( 'template_redirect', [ $this, 'insert_customer_quote' ] );

    }

    public function insert_customer_quote() {
        // Handle quotation re-opened statement from vendor.




        if ( ! empty( $data['dokan_add_to_cart_customer'] ) ) {
            return $this->add_to_cart_customer( $data );
        }
    }

    public function add_to_cart_customer( $data ) {
        // Check if the user is logged in
        if ( ! is_user_logged_in() ) {
            return;
        }

        // Get the quote ID from the form submission
        $quote_id = isset( $data['dokan_add_to_cart_customer'] ) ? intval( $data['dokan_add_to_cart_customer'] ) : 0;

        // Check if the quote ID is valid
        if ( $quote_id <= 0 ) {
            return;
        }

        // Get the quote object
        $quote = RFQHelper::get_request_quote_by_id( $quote_id );

        // Check if the quote exists and is valid
        if ( ! $quote ) {
            return;
        }


        // get the quote details from the quote object
        $quote_details = Helper::get_product_ids_from_quote_details_by_quote_id( $quote_id );



        // Add the quote to the cart
        WC()->cart->add_to_cart( $quote->get_product_id(), 1, 0, [], [ 'quote_id' => $quote_id ] );
    }


    /**
     * Override the quote heading template.
     *
     * @param string $template
     * @param string $slug
     * @param string $name
     *
     * @return string
     */
    public function override_quote_heading_template( $template, $slug, $name ) {
        if ( 'quote-heading' === $slug ) {
            $template = Helper::get_template( '/rfq/quote-heading.php' );
        }
        return $template;
    }
}
