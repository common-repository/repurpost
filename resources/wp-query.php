<?php

/**
 *  WP_Query
 *  https://developer.wordpress.org/reference/classes/wp_query/
 * 
 *  https://generatewp.com/wp_query/
 *
 * @since    2.0.0
 */

function repurpostWP_query( $request ) {

  $parameters = $request->get_query_params();

  // allow these args => what isn't explicitly allowed, is forbidden
  $allowed_args = array(
    'p',
    'name',
    'title',
    'page_id',
    'pagename',
    'post_parent',
    'post_parent__in',
    'post_parent__not_in',
    'post_status',
    'post__in',
    'post__not_in',
    'post_name__in',
    'post_type',
    'posts_per_page', // With restrictions
    'offset',
    'paged',
    'page',
    'ignore_sticky_posts',
    'order',
    'orderby',
    'year',
    'monthnum',
    'w',
    'day',
    'hour',
    'minute',
    'second',
    'm',
    'date_query',
    'inclusive',
    'compare',
    'column',
    'relation',
    'post_mime_type',
    // 'suppress_filters'  // TODO
    'lang', // Polylang
  );

  $default_args = array(
      /***  https://wordpress.org/support/article/post-types/  ***/
      //'post_type' => array( 'post', 'page', 'attachment', 'custom' )  // post by default in WP
      //'post_type' => 'any' // display ‘any‘ post type (retrieves any type except revisions and types with ‘exclude_from_search’ set to TRUE):

      //'post_status'   => 'publish', // is assigned by Default in WP
                        // 'inherit' // for media with post_type = attachment
      'posts_per_page'  => 10,
      'has_password'    => false
    );
  // args from url
  $query_args = array();

  foreach ( $parameters as $key => $value ) {

    // skip keys that are not explicitly allowed
    if( in_array( $key, $allowed_args ) ) {
      $query_args[ $key ] = $value;
    }
  }

  // Combine defaults and query_args
  $args = wp_parse_args( $query_args, $default_args );

  // Run query
  $query = new WP_Query( $args );
  if ($query->have_posts()) {
    return $query->posts;
  } else {
    return $query;
  }  
}