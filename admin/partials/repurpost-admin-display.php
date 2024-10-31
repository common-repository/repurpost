<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.repurpost.com/
 * @since      2.0.0
 *
 * @package    Repurpost
 * @subpackage Repurpost/admin/partials
 */


// Add new admin_menu and Call function menu_repurpost
function content_menu_repurpost()
{ //Show a title page

    $token = get_option('repurpostWP_token');

    if (!current_user_can('manage_options')) {
        wp_die(__('You don\'t have the required permissions to access this page.'));
    }

    require_once plugin_dir_path(__FILE__) . 'repurpost-admin-page.php';
}

// Add new menu
function repurpostWP_custom_menu()
{
    add_menu_page(
        'Repurpost',
        'Repurpost',
        'manage_options',
        'repurpostWP_dashboard',
        'content_menu_repurpost',
        'dashicons-awards'
    );
}
add_action('admin_menu', 'repurpostWP_custom_menu');

// Change Token
if (is_admin() && isset($_POST['generateToken'])) { // TODO: sanitize_text_field($_POST['generateToken']);
    if (class_exists('RepurpostWP_token')) {
        $token = new RepurpostWP_token();
        update_option('repurpostWP_token', $token->generateToken());
    }
}

// Update Advanced Options
if (is_admin() && isset($_POST['referrerLock'])) { // TODO: sanitize_text_field($_POST['referrerLock']);
    if (isset($_POST['repurpostWP_avanced_options']['repurpostWP_referrer_status'])) { // TODO: sanitize_text_field($_POST['repurpostWP_avanced_options']['repurpostWP_referrer_status']);
        update_option('repurpostWP_referrer_status', 1);
    } else {
        update_option('repurpostWP_referrer_status', 0);
    }
    if (isset($_POST['repurpostWP_avanced_options']['repurpostWP_blogusers'])) { // TODO: sanitize_text_field($_POST['repurpostWP_avanced_options']['repurpostWP_blogusers']);
        update_option('repurpostWP_blogusers', $_POST['repurpostWP_avanced_options']['repurpostWP_blogusers']);
    } else {
        update_option('repurpostWP_blogusers', 0);
    }
}
