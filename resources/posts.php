<?php 

function repurpostWP_normalize_post( $post ) {

    $my_post_language_details = apply_filters( 'wpml_post_language_details', NULL, $post->ID );

    $categories = wp_get_object_terms( $post->ID, 'category'); // Get all categories of posts
    $data_category = array();
    foreach ($categories as $index => $category) {
        $data_category[ $index ] = repurpostWP_normalize_category($category);
    }
    $cat = wp_parse_args( $data_category );

    $tags = wp_get_object_terms( $post->ID, 'post_tag'); // Get all tags of posts
    $data_tag = array();
    foreach ($tags as $index => $tag) {
        $data_tag[ $index ] = repurpostWP_normalize_tag($tag);
    }
    $tag = wp_parse_args( $data_tag );

    $post_thumbnail = ( has_post_thumbnail( $post->ID ) ) ? get_the_post_thumbnail_url( $post->ID, 'full' ) : null;
    $post_thumbnail_id = ( has_post_thumbnail( $post->ID ) ) ? get_post_thumbnail_id( $post->ID ) : null;

    $data = array();
    $data[ 'id' ] = $post->ID;
    $data[ 'author' ] = $post->post_author;
    $data[ 'title' ] = $post->post_title;
    $data[ 'content' ] = $post->post_content;
    $data[ 'categories' ] = $cat;
    $data[ 'tags' ] = $tag;
    $data[ 'comment' ] = $post->post_comment;
    $data[ 'status' ] = $post->post_status;
    $data[ 'featured_media' ] = $post_thumbnail_id;
    $data[ 'featured_media_url' ] = $post_thumbnail;
    $data[ 'date' ] = $post->post_date;
    $data[ 'date_gmt' ] = get_gmt_from_date( $post->post_date );
    $data[ 'modified' ] = $post->post_modified;
    $data[ 'modified_gmt' ] = $post->post_modified_gmt;
    $data[ 'excerpt' ] = $post->post_excerpt;
    $data[ 'comment_status' ] = $post->comment_status;
    $data[ 'ping_status' ] = $post->ping_status;
    $data[ 'password' ] = $post->post_password;
    $data[ 'slug' ] = $post->post_name;
    $data[ 'ping' ] = $post->to_ping;
    $data[ 'pinged' ] = $post->pinged;
    $data[ 'menu' ] = $post->menu_order;
    $data[ 'type' ] = $post->post_type;
    $data[ 'mime' ] = $post->post_mime_type;
    $data[ 'link' ] = get_post_permalink($post->ID);
    $data[ 'sticky' ] = is_sticky( $post->ID );

    if ($post->post_type === 'page') {
        $data[ 'link' ] = get_page_link($post->ID);
    } else {
        $data[ 'link' ] = get_post_permalink($post->ID);
    }

    $data[ 'language' ] = null;
    if (is_array($my_post_language_details)) {
        $data[ 'language' ] = $my_post_language_details['language_code'];
    }

    if ( repurpostWP_is_yoast_plugin_active() ) {
        $meta = get_post_meta($post->ID, '', true);

        if (is_array($meta)){
            $data[ 'meta' ] = $meta;
        }
    }

    return $data;

} // End repurpost_normalize_post


function repurpostWP_get_post_list( $param ) {

    $args = array();

    if ( isset($param['post_type']) ) {
        $args['post_type'] = $param['post_type'];
    } else {
        $args['post_type'] = 'post';
    }

    if ( isset($param['post_status']) ) {
        $args['post_status'] = $param['post_status'];
    } else {
        $args['post_status'] = array(
            'publish', // published post or page
            'pending', // post is pending review
            'draft', // a post in draft status
            // 'auto-draft', // a newly created post, with no content
            'future', // a post to publish in the future
            'private', // not visible to users who are not logged in
            // 'inherit', // a revision. see get_children.
            // 'trash'
        );    
    }

    if ( isset($param['page']) && isset($param['limit'])  ) {

        $args['paged'] = $param['page'];
        $args['posts_per_page'] = $param['limit'];

    } else {

        $args['nopaging'] = true;

    }

    if ( isset($param['orderby']) ) $args['orderby'] = $param['orderby']; // Default is 'DATE'

    if ( isset($param['order']) ) $args['order'] = $param['order']; // Default is DESC

    if ( isset($param['post_author']) ) $args['post_author'] = $param['post_author'];

    if ( isset($param['title']) )  $args['title'] = $param['title'];

    if ( isset($param['tags']) ) {

        $tag = array( // Consult multiple taxonomies with the parameter name, slug or term_group of tags and categories
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'post_tag',
                    'field' => 'term_id',
                    'terms' => (array)$param['tags']
                ),
                array(
                    'taxonomy' => 'post_tag',
                    'field' => 'name',
                    'terms' => (array)$param['tags']
                ),
                array(
                    'taxonomy' => 'post_tag',
                    'field' => 'slug',
                    'terms' => (array)$param['tags']
                )
            )
        );
        $args = array_merge( $args, $tag);

    }

    if ( isset($param['categories']) ) {

        $category = array( // Consult multiple taxonomies with the parameter name, slug or term_group of tags and categories
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => (array)$param['categories']
                ),
                array(
                    'taxonomy' => 'category',
                    'field' => 'name',
                    'terms' => (array)$param['categories']
                ),
                array(
                    'taxonomy' => 'category',
                    'field' => 'slug',
                    'terms' => (array)$param['categories']
                )
            )
        );
        $args = array_merge( $args, $category);

    }

    $posts_list = get_posts( $args );

    if (!is_wp_error($posts_list)) {

        $data_post = array();
        foreach($posts_list as $index => $posts) {

            $data_post[ $index ] = repurpostWP_normalize_post($posts);

        }

        return new WP_REST_Response($data_post, 200);

    } else {

        return new WP_REST_Response($data, 400);

    }

} // End repurpostWP_get_post_list


function repurpostWP_get_post( $param ) {
    
    // if ID is set
    if( isset( $param['id'] ) ) {
        //$id = pll_get_post($param['id'], 'es');
        $post = get_post( $param['id'] );
    }
    
    if (!empty($post)) {

        // Check if the post exists
        if( !is_wp_error($post) ) {

            $post_id = repurpostWP_normalize_post($post);

            return new WP_REST_Response( $post_id, 200 );

        } else {

            return new WP_REST_Response( $post, 400 );

        }

    } else {
        $error = array("message" => "There is no posts.");
        return new WP_REST_Response($error, 400);
    }

} // End repurpostWP_get_post


function repurpostWP_create_post( $param ) { // Add New Post

    $tag_ids = array();
    foreach( (array)$param['tags'] as $tag ) {
        $tag = get_term($tag, 'post_tag');
        $tag_ids[] = $tag->name;
    }

    $cat_ids = array();
    foreach( (array)$param['categories'] as $category ) {
        $category = get_term($category, 'category');
        $cat_ids[] = $category->term_id;
    }

    // unfiltered_html kse remove filters
    kses_remove_filters();

    if ( !isset($param['excerpt']) ) {
        $param['excerpt'] = '';
    }

    $id_post = wp_insert_post(wp_slash(array(
        'post_author' => $param['author'],
        'post_content' => $param['content'],
        'post_title' => $param['title'],
        'post_excerpt' => $param['excerpt'],
        'post_status' => $param['status'],
        'post_password' => $param['password'],
        'post_type' => $param['type'],
        'post_category' => $cat_ids,
        'tags_input' => $tag_ids,
        'post_name' => $param['slug']
    )), true);

    if (!is_wp_error($id_post)) {

        $post = get_post( $id_post );
        if (isset($param['media_id']) && is_numeric($param['media_id'])) {
            set_post_thumbnail( $id_post, $param['media_id'] );
        }
        if (isset($param['sticky']) && is_numeric($param['sticky'])) {
            stick_post( $id_post );
        }

        $data = repurpostWP_normalize_post($post);

        // Return json data "OK"
        return new WP_REST_Response( $data, 200 );
    } else {
        return new WP_REST_Response( $id_post, 400 );
    }
        
} // End repurpostWP_create_post


function repurpostWP_update_post( $param ) {
    
    if (repurpostWP_validate_requests()) {
        
        if (isset($param['id'])) {

            $tag_ids = array();
            foreach( (array)$param['tags'] as $tag ) {
                $tag = get_term($tag, 'post_tag');
                $tag_ids[] = $tag->name;
            }
    
            $cat_ids = array();
            foreach( (array)$param['categories'] as $category ) {
                $category = get_term($category, 'category');
                $cat_ids[] = $category->term_id;
            }
    
            $tags_size = sizeof($tag_ids);
            $categories_size = sizeof($cat_ids);
            
            $post_list = array();
            $post_list['ID'] = $param['id'];
            if ( isset( $param["title"] ) ) $post_list['post_title'] = $param['title'];
            if ( isset( $param["author"] ) ) $post_list['post_author'] = $param['author'];
            if ( isset( $param["content"] ) ) $post_list['post_content'] = $param['content'];
            if ( isset( $param["date"] ) ) $post_list['post_date'] = $param['date'];
            if ( isset( $param["excerpt"] ) ) $post_list['post_excerpt'] = $param['excerpt'];
            if ( isset( $param["type"] ) ) $post_list['post_type'] = $param['type'];
            if ( isset( $param["status"] ) ) $post_list['post_status'] = $param['status'];
            if ( isset( $param["password"] ) ) $post_list['post_password'] = $param['password'];
            if ( isset( $param["slug"] ) ) $post_list['post_name'] = $param['slug'];
            if ( isset( $param["categories"] ) && $categories_size != 0 ) $post_list['post_category'] = $cat_ids;
            if ( isset( $param["tags"] ) && $tags_size != 0  ) $post_list['tags_input'] = $tag_ids;

            if ($tags_size == 0) {
                wp_delete_object_term_relationships( $param['id'], 'post_tag' );
            }
            
            if ($categories_size == 0) {
                wp_delete_object_term_relationships( $param['id'], 'category' );
            }

            if (isset($param['media_id']) && is_numeric($param['media_id'])) {
                switch ($param['media_id']) {
                    case 0:
                        delete_post_thumbnail($param['id']);
                        break;
                    default:
                        set_post_thumbnail( $param['id'], $param['media_id'] );
                        break;
                }   
            }

            if (isset($param['sticky']) && is_numeric($param['sticky'])) {
                switch ($param['sticky']) {
                    case 0:
                        unstick_post( $param['id'] );
                    break;
                    case 1:
                        stick_post( $param['id'] );
                        break;
                }
            }

            if ((sizeof($post_list, 0) == 1) && !isset($param['sticky']) && !isset($param['media_id'])) {

                $error = array("message" => "There are no declared fields.");
                return new WP_REST_Response($error, 400);

            }
            
            // unfiltered_html kse remove filters
            kses_remove_filters();

            $id_post = wp_update_post( wp_slash( $post_list ), true );

            if ( !is_wp_error( $id_post ) ) {

                $post = get_post( $id_post );
                $data = repurpostWP_normalize_post( $post );

            } else {
                return new WP_REST_Response( $id_post, 400 );
            }

        }

        return new WP_REST_Response( $data, 200 );

    }
}


function repurpostWP_delete_post( $param ) {
        
    $post = get_post($param['id']);
    
    if(!empty($post)) {

        $data = repurpostWP_normalize_post($post);
        $id_post = wp_delete_post( wp_slash( $param['id'] ), true );

        if ( !is_wp_error( $id_post ) ) {

            return new WP_REST_Response( $data, 200 );
        } else {
            return new WP_REST_Response( $id_post, 400 );
        }

    } else {
        $error = array('message' => 'Post empty.');
        return new WP_REST_Response( $error, 400 );
    }
    
} // End repurpostWP_delete_post