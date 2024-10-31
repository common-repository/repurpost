<?php

function repurpostWP_normalize_tag( $tag_data ) {

    $tag = array(); // Show tag data
    $tag['id'] = $tag_data->term_id;
    $tag['name'] = $tag_data->name;
    $tag['slug'] = $tag_data->slug;
    $tag['term_group'] = $tag_data->term_group;
    $tag['taxonomy'] = $tag_data->taxonomy;
    $tag['description'] = $tag_data->description;
    $tag['parent'] = $tag_data->parent;
    $tag['count'] = $tag_data->count;
    $tag['filter'] = $tag_data->filter;

    return $tag;

} // End repurpostWP_normalize_tag

function repurpostWP_get_tag( $param ) {

    $tag_data = get_term($param['id'] , 'post_tag');

    if (!empty($tag_data)) { 

        if( !is_wp_error($tag_data) ) { // Check if the tag exists

            $tag = repurpostWP_normalize_tag($tag_data);

            return new WP_REST_Response( $tag, 200 ); // Return tag data

        } else {
            return new WP_REST_Response( $tag_data, 400 ); // Return error
        }

    } else {
        $error = array('message' => 'Taxonomy not found.');
        return new WP_REST_Response( $error, 400 );
    }
    
} // End repurpostWP_get_tag


function repurpostWP_delete_tag( $param ) {
      
    $tag_data = get_term($param['id'], 'post_tag');

    if(!empty($tag_data)) {

        $id_tag = wp_delete_term($param['id'], 'post_tag');

        if ( !is_wp_error( $id_tag ) ) {

            $tag = repurpostWP_normalize_tag($tag_data);
            
            return new WP_REST_Response( $tag, 200 );
        } else {
            return new WP_REST_Response( $id_tag, 400 );
        }

    } else {
        $error = array('message' => 'Taxonomy not found.');
        return new WP_REST_Response( $error, 400 );
    }
    
} // End repurpostWP_delete_tag


function repurpostWP_update_tag( $param ) {

    $tag = array();
    if ( isset( $param["name"] ) ) $tag['name'] = $param['name'];
    if ( isset( $param["slug"] ) ) $tag['slug'] = $param['slug'];
    if ( isset( $param["term_group"] ) ) $tag['term_group'] = $param['term_group'];
    if ( isset( $param["parent"] ) ) $tag['parent'] = $param['parent'];
    if ( isset( $param["description"] ) ) $tag['description'] = $param['description'];

    $id_tag = wp_update_term( $param['id'], 'post_tag', wp_slash( $tag ), true );

    if ( !is_wp_error( $id_tag ) ) {

        $tag_data = get_term( $param['id'], 'post_tag' );
        $tag = repurpostWP_normalize_tag($tag_data);

        return new WP_REST_Response( $tag, 200 );

    } else {
        return new WP_REST_Response( $id_tag, 400 );
    }
    
} // End repurpostWP_update_tag


function repurpostWP_get_tag_list( $param ) {
    
    $args = array(
        'hide_empty' => false
    );

    if ( isset($param['page']) && isset($param['limit'])  ) {

        $args['offset'] = ( $param['page'] - 1 ) * $param['limit'];
        $args['number'] = $param['limit'];

    }

    if ( isset($param['name']) ) $args['name'] = $param['name'];

    if ( isset($param['slug']) ) $args['slug'] = $param['slug'];

    if ( isset($param['parent']) ) $args['parent'] = $param['parent'];

    if ( isset($param['order']) ) $args['order'] = $param['order']; // Default is ASC

    if ( isset($param['search']) ) $args['search'] = $param['search'];  // search in all fields

    if ( isset($param['name__like']) ) $args['name__like'] = $param['name__like'];  // search in name field

    if ( isset($param['description__like']) ) $args['description__like'] = $param['description__like'];  // search in name field

    $tags = get_tags( $args ); // Get the tags

    if (!is_wp_error($tags)) {

        $data_tag = array(); // Show tags data
        foreach ($tags as $index => $tag) {

            $data_tag[ $index ] = repurpostWP_normalize_tag($tag);

        }

        return new WP_REST_Response( $data_tag, 200 );

    } else {
        return new WP_REST_Response( $tags, 400 );
    }

} // End repurpostWP_get_tag_list 


function repurpostWP_create_tag( $param ) {

    $tag = wp_insert_term($param['name'], 'post_tag', wp_slash(array(
        'slug' => $param['slug'],
        'term_group' => $param['term_group'],
        'parent' => $param['parent'],
        'description' => $param['description']
    )),true);

    if (!is_wp_error($tag)) {

        $tag_data = get_term($tag['term_id'], 'post_tag');
        $tag = repurpostWP_normalize_tag($tag_data);

        // Return json data "OK"
        return new WP_REST_Response($tag, 200);

    } else {

        return new WP_REST_Response($tag, 400);

    }
    
} // End repurpostWP_create_tag
