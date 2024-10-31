<?php
/* CHECK PLUGIN_TOKEN & INFO */
require_once plugin_dir_path( __FILE__ ) . '../token/validate.php'; // Validate the token with the database.


/* REQUESTS WP */
require_once plugin_dir_path( __FILE__ ) . '../../resources/posts.php'; // Make requests for posts

require_once plugin_dir_path( __FILE__ ) . '../../resources/users.php'; // Make requests for users

require_once plugin_dir_path( __FILE__ ) . '../../resources/tags.php'; // Make requests for tags

require_once plugin_dir_path( __FILE__ ) . '../../resources/categories.php'; // Make requests for categories

require_once plugin_dir_path( __FILE__ ) . '../../resources/medias.php'; // Make requests for medias

require_once plugin_dir_path( __FILE__ ) . '../../resources/wp-query.php'; // Make wp query

/* YOAST_SEO API */
require_once plugin_dir_path( __FILE__ ) . '../yoast_seo/metatags.php';

/* POLYLANG PLUGIN INFO */
require_once plugin_dir_path( __FILE__ ) . '../polylang/polylang-info.php';


/* CHECK PLUGIN_TOKEN */

/*
*  Add new route to the URL [GET] /repurpost/info
*/ 
register_rest_route('repurpost', '/info', array(
	'methods' => 'GET',
	'callback' => 'repurpostWP_plugin_info',
	'permission_callback' => function () {
		return true; // required for public routes
	  }
));

/*
*  Add new route to the URL [POST] /repurpost/token
*/ 
register_rest_route('repurpost', '/token', array(
	'methods' => 'POST',
	'callback' => 'repurpostWP_validate_token',
	'permission_callback' => function () {
		return true; // required for public routes
	  }
));


/*
*  Add new route to the URL /repurpost/post
*  Show all Posts Database [GET & POST] /repurpost/post
*/
register_rest_route('repurpost', '/post', array(
	array(
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_post_list',
		'args' => array(
			'page' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'limit' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'title' => array(
				'required' => false
			),
			'tax_query' => array( // Consult multiple taxonomies with the parameter name, slug or term_group of tags and categories
				'required' => false
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array (
		'methods' => 'POST',
		'callback' => 'repurpostWP_create_post',
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
) );

// Show post by ID [GET] /repurpost/post/:id
register_rest_route('repurpost', '/post/(?P<id>\d+)', array(
	array( // Show a Post with ID [GET] /repurpost/post/:id
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_post',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array( // Update a Post with ID [PUT] /repurpost/post/:id
		'methods' => 'PUT',
		'callback' => 'repurpostWP_update_post',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array( // Delete a Post with ID [DELETE] /repurpost/post/:id
		'methods' => 'DELETE',
		'callback' => 'repurpostWP_delete_post',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));


/*
*  Add new route to the URL /repurpost/categories
*  Show all Categories Database [GET & POST] /repurpost/category 
*/
register_rest_route('repurpost', '/category', array(
	array(
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_category_list',
		'args' => array(
			'page' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'limit' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'name' => array(
				'required' => false
			),
			'slug' => array(
				'required' => false
			),
			'parent' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array(
		'methods' => 'POST',
		'callback' => 'repurpostWP_create_category',
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));

// Show category by ID [GET] /repurpost/category/:id
register_rest_route('repurpost', '/category/(?P<id>\d+)', array(
	array(
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_category',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array( // Update a Category with ID [PUT] /repurpost/category/:id
		'methods' => 'PUT',
		'callback' => 'repurpostWP_update_category',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array( // Delete a Category with ID [DELETE] /repurpost/category/:id
		'methods' => 'DELETE',
		'callback' => 'repurpostWP_delete_category',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));


/*
*  Add new route to the URL /repurpost/tag
*  Show all Tags Database [GET & POST] /repurpost/tag 
*/
register_rest_route('repurpost', '/tag', array(
	array(
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_tag_list',
		'args' => array(
			'page' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'limit' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'name' => array(
				'required' => false
			),
			'slug' => array(
				'required' => false
			),
			'parent' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array (
		'methods' => 'POST',
		'callback' => 'repurpostWP_create_tag',
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));

// Show tag by ID [GET] /repurpost/tag/:id
register_rest_route('repurpost', '/tag/(?P<id>\d+)', array(
	array(
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_tag',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array( // Update a tag with ID [PUT] /repurpost/tag/:id
		'methods' => 'PUT',
		'callback' => 'repurpostWP_update_tag',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array( // Delete a tag with ID [DELETE] /repurpost/tag/:id
		'methods' => 'DELETE',
		'callback' => 'repurpostWP_delete_tag',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));


/*
*  Add new route to the URL /repurpost/user
*  Show all Users
*/
register_rest_route('repurpost', '/user', array(
	array(
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_user_list',
		'args' => array(
			'page' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'limit' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'login' => array(
				'required' => false
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));

// Show tag by ID [GET] /repurpost/user/:id
register_rest_route('repurpost', '/user/(?P<id>\d+)', array(
	array(
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_user',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));


/*
*  Add new route to the URL /repurpost/media
*  Show all Media Database [GET & POST] /repurpost/media 
*/
register_rest_route('repurpost', '/media', array(
	array (
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_media_list',
		'args' => array(
			'page' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'limit' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'title' => array(
				'required' => false
			),
			'name' => array(
				'required' => false
			),
			'parent' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'mime' => array(
				'required' => false
			),
			'author' => array(
				'required' => false,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array (
		'methods' => 'POST',
		'callback' => 'repurpostWP_create_media',
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));

// Show media by ID [GET & PUT] /repurpost/media/:id
register_rest_route('repurpost', '/media/(?P<id>\d+)', array(
	array( // Get a media with ID [GET] /repurpost/media/:id
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_media',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array( // Put a media with ID [PUT] /repurpost/media/:id
		'methods' => 'PUT',
		'callback' => 'repurpostWP_update_media',
		'args' => array(
			'id' => array(
				'required' => true, 
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array( // Delete a media with ID [DELETE] /repurpost/media/:id
		'methods' => 'DELETE',
		'callback' => 'repurpostWP_delete_media',
		'args' => array(
			'id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));



/* YOAST_SEO API */

register_rest_route('repurpost', '/metatags', array(
	array( // Get a title with ID [GET] /repurpost/seotitle/:id
		'methods' => 'GET',
		'callback' => 'repurpostWP_get_yoast_meta',
		'args' => array(
			'post_id' => array( 
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'meta' => array(
				'default' => ''
			)
			),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	),
	array( // Put a title with ID [PUT] /repurpost/seotitle/:id
		'methods' => 'PUT',
		'callback' => 'repurpostWP_update_yoast_meta',
		'args' => array(
			'post_id' => array(
				'required' => true,
				'validate_callback' => function( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
			'meta' => array(
				'required' => true
			),
			'meta_value' => array(
				'required' => true
			)
		),
		'permission_callback' => function () {
			return repurpostWP_validate_requests();
		  }
	)
));


/*
*  Polylang Info
*/ 
register_rest_route('repurpost', '/polylang', array(
	'methods' => 'GET',
	'callback' => 'repurpostWP_polylang_plugin_info',
	'permission_callback' => function () {
		return repurpostWP_validate_requests();
	  }
));