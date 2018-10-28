<?php
/*
 * Plugin Name: WordPress Router API
 * Version: 0.0.1
 * Plugin URI: http://brodmountain.studio
 * Description: A plugin to expose WordPress router-like functionality to the WP REST API.
 * Author: Paul Bredenberg
 * Author URI: http://brodmountain.studio
 * Requires at least: 4.9.8
 * Tested up to: 4.9.8
 *
 * Text Domain: wordpress-plugin-template
 *
 * @package WordPress
 * @author Paul Bredenberg
 * @since 0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class WordPressRouterAPI {

    /**
     * Constructor function
     */
    public function __construct () {
        add_action( 'rest_api_init', array( $this, 'init' ));
    }

    /**
     * Init method in which custom REST endpoints can be routed to their
     * related method.
     */
    public function init() {
        register_rest_route( 'wp-router-api/v1', '/by/path', 
            array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_object_by_path'),
            )
        );
    }

    public function get_object_by_path($request) {
        $parameters = $request->get_query_params();
        $path = $parameters['path'];

        return array (
            'parameters' => $parameters,
            'path' => $path,
            'post' => $this->get_obj_by_path($path),
        );
    }

    /**
     * Accepts a string with the provided path and returns a post object,
     * or null if not found.
     */
    private function get_obj_by_path($path) {
        $object = null;
        $page = get_page_by_path($path);
        // If a page is not found, iterate through the current sites registered post types
        // to attempt a match for the provided path.
        if (!$page) {
            foreach(get_post_types() as $post_type) {
                $page = get_page_by_path(basename( untrailingslashit($path) ), OBJECT, $post_type);
                if ($page) {
                    $object = $page;
                }
            }
        } else {
            $object = $page;
        }
        // Get this posts children.
        $object->children = get_children($object->ID);
        // For good measure lets get all the custom fields too.
        $object->fields = get_post_custom($object->ID);
        return $object;
    }
}

if ( ! function_exists ( 'wp_router_api_init' ) ) {

    function wp_router_api_init() {
        $initializer = new WordPressRouterAPI();
    }

    add_action( 'init', 'wp_router_api_init' );
}
