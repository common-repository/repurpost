<?php

if (!function_exists('is_plugin_active')) {
    function is_plugin_active($plugin)
    {
        return in_array($plugin, (array) get_option('active_plugins', array()));
    }
}

function repurpostWP_is_polylang_plugin_active()
{
    return is_plugin_active('polylang/polylang.php');
}

function repurpostWP_is_yoast_plugin_active()
{
    $active = is_plugin_active('wordpress-seo/wp-seo.php') || is_plugin_active('wordpress-seo-premium/wp-seo-premium.php');
    return $active;
}

// Returns a file size limit in bytes based on the PHP upload_max_filesize
// and post_max_size
function repurpostWP_file_upload_max_size()
{
    static $max_size = -1;

    if ($max_size < 0) {
        // Start with post_max_size.
        $post_max_size = repurpostWP_parse_size(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = repurpostWP_parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

function repurpostWP_parse_size($size)
{
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}


function repurpostWP_info_detail() {
    //$yoast_data = get_option('wpseo');
    $message = array(
        'blog_name' => get_bloginfo("name"),
        'blog_language' => get_bloginfo("language"),
        'blog_url' => get_bloginfo("url"), // The Blog home (URL) (set in Settings > General)
        'wp_version' => get_bloginfo("version"),
        'php_version' => phpversion(),
        'plugin_version' => REPURPOST_VERSION,
        'pollylang_active' => repurpostWP_is_polylang_plugin_active(),
        'yoast_active' => repurpostWP_is_yoast_plugin_active(),
        'upload_max_size' => repurpostWP_file_upload_max_size()
     //   'yoast_version' => $yoast_data['version']
    ); 

    return $message;
}

function repurpostWP_remote_ip_address() {
    if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }
    return $ip_address;
}

function repurpostWP_remote_host() {
    return gethostbyaddr(repurpostWP_remote_ip_address());
}

function repurpostWP_validate_remote_host() {
    $remote_host = repurpostWP_remote_host();
    if ($remote_host == dns_get_record('api.repurpost.com')[0]['target']) {
        return true;
    } else {
        return false;
    }
}

function repurpostWP_is_valid_token() {
    $tokenDB = get_option('repurpostWP_token');
    $token = $_GET['token']; // TODO: sanitize_text_field($_GET['token']);

    if ($token == $tokenDB) {
        return true;
    } else {
        return false;
    }
}

/**
 *  Validate token
 *
 * @since    2.0.0
 */

function repurpostWP_validate_token() {
    if (repurpostWP_is_valid_token() ) {
        $message = repurpostWP_info_detail();

        return new WP_REST_Response($message, 200);
    } else {
        $error = array('message' => 'ERROR_TOKEN');

        return new WP_REST_Response($error, 403);
    }
}

/**
 *  Validate token in requests
 *
 * @since    2.0.0
 */

function repurpostWP_validate_requests() {
    $referrerSecurityOption = get_option('repurpostWP_referrer_status');
    $isValidToken = repurpostWP_is_valid_token();

    if ($referrerSecurityOption == true) {
        $isValidHost = repurpostWP_validate_remote_host();

        return $isValidHost && $isValidToken;
    }
    return $isValidToken;
}


/**
 *  Show plugin info
 *
 * @since    2.0.0
 */

function repurpostWP_plugin_info() {
    $message = repurpostWP_info_detail();

    return new WP_REST_Response($message, 200);
}
