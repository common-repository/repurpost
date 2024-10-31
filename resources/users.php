<?php

function repurpostWP_normalize_user( $user_data ) {

    $user = array();
    $user[ 'id' ] = $user_data->ID;
    $user[ 'login' ] = $user_data->user_login;
    $user[ 'password' ] = $user_data->user_pass;
    $user[ 'nicename' ] = $user_data->user_nicename;
    $user[ 'email' ] = $user_data->user_email;
    $user[ 'url' ] = $user_data->user_url;
    $user[ 'registered' ] = $user_data->user_registered;
    $user[ 'activation_key' ] = $user_data->user_activation_key;
    $user[ 'status' ] = $user_data->user_status;
    $user[ 'display_name' ] = $user_data->display_name;
    $user[ 'id' ] = $user_data->ID;
    $user[ 'caps' ] = $user_data->caps;
    $user[ 'cap_key' ] = $user_data->cap_key;
    $user[ 'roles' ] = $user_data->roles;
    $user[ 'all_caps' ] = $user_data->allcaps;
    $user[ 'filter' ] = $user_data->filter;
    return $user;
}

function repurpostWP_create_user( $param ) {
    
    $args = array(
        'user_login' => $param['user_login'],
        'user_email' => $param['user_email']
    );

    if ( isset($param['user_pass']) )  
        $args['user_pass'] = password_hash($param['user_pass'],PASSWORD_BCRYPT);

    if ( isset($param['user_nicename']) ) $args['user_nicename'] = $param['user_nicename'];

    if ( isset($param['role']) ) $args['role'] = $param['role']; // default subscriber

    $user = wp_insert_user(wp_slash($args),true);

    if (!is_wp_error($user)) {

        $new_user = get_user_by('id', $user);

        $user = repurpostWP_normalize_user($new_user);

        return new WP_REST_Response($user, 200);

    } else {
        return new WP_REST_Response($user, 400);
    }
 
} // End repurpostWP_create_user


function repurpostWP_get_user_list( $param ) {

    $args = array();

    if ( isset($param['orderby']) ) {
        $args['orderby'] = $param['orderby'];
    } else {
        $args['orderby'] = 'user_nicename';
    }

    if ( isset($param['page']) && isset($param['limit'])  ) {

        $args['offset'] = ( $param['page'] - 1 ) * $param['limit'];
        $args['number'] = $param['limit'];

    } else {

        $args['nopaging'] = true;

    }

    if ( isset($param['order']) ) $args['order'] = $param['order']; // Default is ASC

    if ( isset($param['search']) ) $args['search'] = $param['search'];  // search users by email address, URL, ID, username or display_name
    
    if ( isset($param['login']) ) $args['login'] = $param['login'];

    if ( isset($param['role']) ) $args['role'] = $param['role']; // Limit the returned users to the role specified

    if ( isset($param['role__in']) ) $args['role__in'] = $param['role__in']; // Limit the returned users that have one of the specified roles. 

    if ( isset($param['role__not_in']) ) {
        $args['role__not_in'] = $param['role__not_in']; //  Exclude users who have any of the specified roles.
    } else {
        $args['role__not_in'] = 'subscriber';
    }


    $user_list = get_users( $args );

    if (!is_wp_error($user_list)) {

        $data_user = array();
        $index = 0;
        foreach($user_list as $user) {
            $permitted_users = get_option('repurpostWP_blogusers');
            if(empty($permitted_users) || in_array('all', $permitted_users)) {
                $data_user[ $index ] = repurpostWP_normalize_user( $user );
                $index++;
            } elseif(!empty($permitted_users) && in_array($user->ID, $permitted_users)) {
                $data_user[ $index ] = repurpostWP_normalize_user( $user );
                $index++;
            }
        }

        return new WP_REST_Response( $data_user, 200 );

    } else {
        return new WP_REST_Response( $user_list, 400 );
    }
    
} // End repurpostWP_get_user_list


function repurpostWP_get_user( $param ) {

    $user = get_user_by('id', $param['id']);

    if (!empty($user)) {

        if( !is_wp_error($user) ) { // Check if the user exists

            $user_data = repurpostWP_normalize_user($user);

            return new WP_REST_Response( $user_data, 200 ); // Return user data

        } else {
            return new WP_REST_Response( $user, 400 ); // Return Error
        }

    } else {
        $error = array('message' => 'User not found.');
        return new WP_REST_Response( $error, 400 );
    }
    
} // End repurpostWP_get_user


function repurpostWP_update_user( $param ) {
        
    $user = array();
    $user['ID'] = $param['id'];
    if ( isset( $param["user_login"] ) ) $user['user_login'] = $param['user_login'];
    if ( isset( $param["user_pass"] ) ) $user['user_pass'] = $param['user_pass'];
    if ( isset( $param["user_nicename"] ) ) $user['user_nicename'] = $param['user_nicename'];
    if ( isset( $param["user_email"] ) ) $user['user_email'] = $param['user_email'];
    if ( isset( $param['role'] ) ) $user['role'] = $param['role'];

    $id_user = wp_update_user(wp_slash( $user ), true );

    if ( !is_wp_error( $id_user ) ) {

        $new_user = get_user_by('id', $id_user);
        $user = repurpostWP_normalize_user($new_user);

        return new WP_REST_Response( $user, 200 );

    } else {
        return new WP_REST_Response( $id_user, 400 );
    }

} // End repurpostWP_update_user


function repurpostWP_delete_user( $param ) {
    
    $user = get_user_by('id', $param['id']);
    
    if (!empty($user)) { // Check that the label is empty

        $id_user = wp_delete_user($param['id']);

        if ( !is_wp_error( $id_user ) ) {

            $user = repurpostWP_normalize_user($user);

            return new WP_REST_Response( $user, 200 );
        } else {
            return new WP_REST_Response( $id_user, 400 );
        }

    } else {
        $error = array("mesage" => "User not found.");
        return new WP_REST_Response($error, 400);
    }
    
} // End repurpostWP_delete_user