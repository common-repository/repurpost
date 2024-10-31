<?php

/**
 *  Show Polylang plugin info
 *
 * @since    2.0.0
 */


/** ANOTACIONES PLUGIN
* 
* pll_the_languages(array( 'raw' => 1 )); => // Will return an array of arrays, one array per language
*
* pll_languages_list(); => // Returns the list of languages
*
* $my_post_language_details = apply_filters( 'wpml_post_language_details', NULL, $post->ID );
* $my_post_language_details; =>  array (
*                                   'language_code' => 'en',
*                                    'locale' => 'en_US',
*                                    'text_direction' => false,
*                                    'display_name' => 'English',
*                                    'native_name' => 'English',
*                                    'different_language' => false,
*                                );
*
* pll_get_post_language($post->ID, 'slug'); => // Gets the language of a post or page (or custom post type post)
*
* pll_set_post_language($id_post, 'en'); => // Sets the language of a post or page  */



function repurpostWP_polylang_plugin_info() {

    if (!repurpostWP_is_polylang_plugin_active()) {

        $result_error = array( 'message' => 'POLYLANG_PLUGIN_DEACTIVE' );

        return new WP_REST_Response( $result_error, 400 );

    }

    $polylang = get_option('polylang');

    $result = array(
        'polylang' => $polylang
    );

    return new WP_REST_Response( $result, 200 );
}