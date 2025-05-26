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


}
