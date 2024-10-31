<?php

/**
 *  Check Yoast Meta
 *
 * @since    2.0.0
 */

function repurpostWP_check_supported_yoast_meta($meta) {
    // Yoast meta list
    $yoast_meta_list = array(
        '_yoast_wpseo_focuskw',
        '_yoast_wpseo_title',
        '_yoast_wpseo_metadesc',
        '_yoast_wpseo_linkdex',
        '_yoast_wpseo_metakeywords',
        '_yoast_wpseo_meta-robots-noindex',
        '_yoast_wpseo_meta-robots-nofollow',
        '_yoast_wpseo_meta-robots-adv',
        '_yoast_wpseo_canonical',
        '_yoast_wpseo_redirect',
        '_yoast_wpseo_opengraph-title',
        '_yoast_wpseo_opengraph-description',
        '_yoast_wpseo_opengraph-image',
        '_yoast_wpseo_twitter-title',
        '_yoast_wpseo_twitter-description',
        '_yoast_wpseo_twitter-image'
    );

    return in_array( $meta, $yoast_meta_list );
}

/**
 *  Get Yoast Meta
 *
 * @since    2.0.0
 */

function repurpostWP_get_yoast_meta( $param ) {

    if ( repurpostWP_is_yoast_plugin_active() ) {

        if ( $param['post_id'] || $param['meta'] ) {
            
            if ( repurpostWP_check_supported_yoast_meta( $param['meta']) ) {

                $yoast_meta = get_post_meta( (int)$param['post_id'], $param['meta'], true ); 

                return $yoast_meta; //  TODO Response
            } else {

                $meta_error = array( 'message' => 'Unsupported meta tag.' );
        
                return new WP_REST_Response( $meta_error, 400 );
            }
        }

        $param_error = array( 'message' => 'Missing required fields' );

        return new WP_REST_Response( $param_error, 400 );

    } else {

        $error = array("message" => "You do not have the yoast API plugin installed.");
        return new WP_REST_Response($error, 400);

    }

}

/**
 *  Update Yoast Meta
 *
 * @since    2.0.0
 */

function repurpostWP_update_yoast_meta( $param ) {

    if ( repurpostWP_is_yoast_plugin_active() ) {
        
        if ( $param['post_id'] || $param['meta'] || $param['meta_value'] ) {

            if ( repurpostWP_check_supported_yoast_meta( $param['meta']) ) {

                $yoast_meta = update_post_meta( (int) $param['post_id'], $param['meta'], $param['meta_value'] );

                return $yoast_meta; //  TODO Response
            } else {

                $meta_error = array( 'message' => 'Unsupported meta tag.' );
        
                return new WP_REST_Response( $meta_error, 400 );
            }
        }

        $param_error = array( 'message' => 'Missing required fields' );

        return new WP_REST_Response( $param_error, 400 );

    } else {

        $error = array("message" => "You do not have the yoast API plugin installed.");
        return new WP_REST_Response($error, 400);

    }

}

