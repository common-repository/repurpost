<?php

function repurpostWP_normalize_category( $cat_data ) {

    $category = array(); // Show category data
    $category['id'] = $cat_data->term_id;
    $category['name'] = $cat_data->name;
    $category['slug'] = $cat_data->slug;
    $category['term_group'] = $cat_data->term_group;
    $category['taxonomy'] = $cat_data->taxonomy;
    $category['description'] = $cat_data->description;
    $category['parent'] = $cat_data->parent;
    $category['count'] = $cat_data->count;
    $category['filter'] = $cat_data->filter;
    return $category;

} // End repurpostWP_normalize_category


function repurpostWP_create_category( $param ) {

    $category = wp_insert_term($param['name'], 'category', wp_slash(array(
        'slug' => $param['slug'],
        'term_group' => $param['term_group'],
        'parent' => $param['parent'],
        'description' => $param['description']
    )),true);

    if (!is_wp_error($category)) {

        $category_data = get_term($category['term_id'], 'category');
        $category = repurpostWP_normalize_category($category_data);

        return new WP_REST_Response($category, 200);

    } else {

        return new WP_REST_Response($category, 400);

    }
    
} // End repurpostWP_create_category


function repurpostWP_get_category_list( $param ) {

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

    $categories = get_categories( $args );

    if (!is_wp_error($categories)) {

        $data_category = array(); // Show categories data
        foreach ($categories as $index => $category) {

            $data_category[ $index ] = repurpostWP_normalize_category($category);

        }

        return new WP_REST_Response( $data_category, 200 );

    } else {
        return new WP_REST_Response( $categories, 400 );
    }
    
} // End repurpostWP_get_category_list


function repurpostWP_update_category( $param ) {

    $category = array();
    if ( isset( $param["name"] ) ) $category['name'] = $param['name'];
    if ( isset( $param["slug"] ) ) $category['slug'] = $param['slug'];
    if ( isset( $param["term_group"] ) ) $category['term_group'] = $param['term_group'];
    if ( isset( $param["parent"] ) ) $tag['parent'] = $param['parent'];
    if ( isset( $param["description"] ) ) $tag['description'] = $param['description'];

    $id_category = wp_update_term( $param['id'], 'category', wp_slash( $category ), true );

    if ( !is_wp_error( $id_category ) ) {

        $category_data = get_term( $param['id'], 'category' );
        $category = repurpostWP_normalize_category($category_data);
        return new WP_REST_Response( $category, 200 );

    } else {
        return new WP_REST_Response( $id_category, 400 );
    }
    
} // End repurpostWP_update_category


function repurpostWP_delete_category( $param ) {
    
    $category_data = get_term($param['id'], 'category');

    if(!empty($category_data)) {

        $id_category = wp_delete_term($param['id'], 'category');

        if ( !is_wp_error( $id_category ) ) {

            $category = repurpostWP_normalize_category($category_data);

            return new WP_REST_Response( $category, 200 );
        } else {
            return new WP_REST_Response($id_category, 400);
        }

    } else {
        $error = array('message' => 'Taxonomy not found.');
        return new WP_REST_Response( $error, 400 );
    }
    
} // End repurpostWP_delete_category


function repurpostWP_get_category( $param ) {

    $category_data = get_term($param['id'], 'category');

    if (!empty($category_data)) {
    
        if( !is_wp_error($category) ) { // Check if the category exists

            $category = repurpostWP_normalize_category($category_data);

            return new WP_REST_Response( $category, 200 );

        } else {
            return new WP_REST_Response( $category, 400 );
        }

    } else {
        $error = array('message' => 'Taxonomy not found.');
        return new WP_REST_Response( $error, 400 );
    }
    
} // End repurpostWP_get_category