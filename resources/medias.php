<?php

function repurpostWP_normalize_media( $media_data ) {
    $media = array();
    $media[ 'id' ] = $media_data->ID;
    $media[ 'author' ] = $media_data->post_author;
    $media[ 'date' ] = $media_data->post_date;
    $media[ 'date_gmt' ] = $media_data->post_date_gmt;
    $media[ 'modified' ] = $media_data->post_modified;
    $media[ 'modified_gmt' ] = $media_data->post_modified_gmt;
    $media[ 'content' ] = $media_data->post_content;
    $media[ 'title' ] = $media_data->post_title;
    $media[ 'excerpt' ] = $media_data->post_excerpt;
    $media[ 'status' ] = $media_data->post_status;
    $media[ 'comment_status' ] = $media_data->comment_status;
    $media[ 'ping_status' ] = $media_data->ping_status;
    $media[ 'password' ] = $media_data->post_password;
    $media[ 'slug' ] = $media_data->post_name;
    $media[ 'ping' ] = $media_data->to_ping;
    $media[ 'pinged' ] = $media_data->pinged;
    $media[ 'modified' ] = $media_data->post_modified;
    $media[ 'content_filtered' ] = $media_data->post_content_filtered;
    $media[ 'guid' ] = $media_data->guid;
    $media[ 'menu' ] = $media_data->menu_order;
    $media[ 'type' ] = $media_data->post_type;
    $media[ 'mime_type' ] = $media_data->post_mime_type;
    $media[ 'comment' ] = $media_data->comment_count;
    $media[ 'filter' ] = $media_data->filter;
    return $media;
}

function repurpostWP_create_media() {

    if (empty($_FILES['file']) ) {
        $error = array("message" => "File are required.");
        return new WP_REST_Response($error, 400);
    } else {
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/media.php' );

            $files = $_FILES['file'];
            $uploaded_media = array();
            foreach ((array)$files['name'] as $key => $value) {
                if ($files['name'][$key]) {
                    $file = array(
                        'name' => sanitize_file_name($files['name'][$key]),
                        'type' => sanitize_text_field($files['type'][$key]),
                        'tmp_name' => esc_url($files['tmp_name'][$key]),
                        'error' => sanitize_text_field($files['error'][$key]),
                        'size' => is_numeric($files['size'][$key])
                    );
                    $_FILES = array('uploadfile' => $file);
                    $media_id = media_handle_upload('uploadfile', 0);
                    $media_data = get_post($media_id);
                    $media_normalized = repurpostWP_normalize_media($media_data);
                    array_push($uploaded_media, $media_normalized);
                }
            }

            return new WP_REST_Response($uploaded_media, 200);

        } else {
            $error = array("mesage" => "File not found.");
            return new WP_REST_Response($error, 400);
        }

    }

}


function repurpostWP_get_media_list( $param ) {
    
    require_once ABSPATH . WPINC . '/media.php';

    $id_parent = $medias_list->ID;
    $type = $medias_list->post_mime_type;
    $args = array(
        'post_parent' => $id_parent,
        'post_type'   => 'attachment',
        'mime_type' => $type,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );

    if ( isset($param['page']) && isset($param['limit'])  ) {

        $pages = array(
            'paged' => $param['page'],
            'posts_per_page' => $param['limit']
        );
        $args = array_merge( $args, $pages);

    } else {

        $pages = array( 'nopaging' => true );
        $args = array_merge( $args, $pages);

    }

    if ( isset($param['title']) ) {
        
        $title = array( 'title' => $param['title'] );
        $args = array_merge( $args, $title);

    }

    if ( isset($param['name']) ) {
        
        $name = array( 'name' => $param['name'] );
        $args = array_merge( $args, $name);

    }

    if ( isset($param['parent']) ) {
        
        $parent = array( 'post_parent' => $param['parent'] );
        $args = array_merge( $args, $parent);

    }

    if ( isset($param['mime']) ) {
        
        $mime = array( 'post_mime_type' => $param['mime'] );
        $args = array_merge( $args, $mime);

    }

    if ( isset($param['author']) ) {
        
        $author = array( 'author' => $param['author'] );
        $args = array_merge( $args, $author);

    }

    $medias_list = get_posts($args);

    if (!is_wp_error($medias_list)) {

        $data_media = array();
        foreach( $medias_list as $index => $medias) { // Show all medias data
            
            $data_media[ $index ] = repurpostWP_normalize_media( $medias );
            
        }

        return new WP_REST_Response($data_media, 200);

    } else {
        return new WP_REST_Response($medias_list, 400);
    }

}


function repurpostWP_update_media( $param ) {

    $media = array();
    $media['ID'] = $param['id'];
    if ( isset( $param["title"] ) ) $media['post_title'] = $param['title'];
    if ( isset( $param["content"] ) ) $media['post_content'] = $param['content'];
    if ( isset( $param["name"] ) ) $media['post_name'] = $param['name'];
    if ( isset( $param["excerpt"] ) ) $media['post_excerpt'] = $param['excerpt'];
    if ( isset( $param["author"] ) ) $media['post_author'] = $param['author'];

    $media_id = wp_update_post(wp_slash( $media ), true );

    if ( !is_wp_error( $media_id ) ) {

        $media_update = get_post($media_id);
        $media = repurpostWP_normalize_media( $media_update );

        return new WP_REST_Response( $media, 200 );

    } else {
        return new WP_REST_Response( $media_id, 400 );
    }
    
}


function repurpostWP_delete_media( $param ) {

    $media = get_post($param['id']);

    if(!empty($media)) {
    
        $media_id = wp_delete_post( wp_slash( $param['id'] ), true );

        if ( !is_wp_error( $media_id ) ) {

            $data_media = repurpostWP_normalize_media( $media_id );

            return new WP_REST_Response($data_media, 200);

        } else {
            return new WP_REST_Response($media_id, 400);
        }
        
    } else {
        $error = array('message' => 'Media not found.');
        return new WP_REST_Response( $error, 400 );
    }

}


function repurpostWP_get_media( $param ) {
    
    $media = get_post($param['id']); // Get the media
    
    if ( !empty($media) ) { // Check that the label is empty

        if (!is_wp_error($media)) {
            
            $data_media = repurpostWP_normalize_media( $media );

            return new WP_REST_Response($data_media, 200);

        } else {
            return new WP_REST_Response($media, 400);
        }

    } else {
        $error = array("message" => "There is no images.");
        return new WP_REST_Response($error, 400);
    }
    
}