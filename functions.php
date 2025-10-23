<?php

namespace RBS;

/**
 * Include the autoloader
 *
 * @since 1.0.0
 */

require_once __DIR__ . '/vendor/autoload.php';

// include get_stylesheet_directory() . '/assets/hooks/change-hamburger.php';
// include get_stylesheet_directory() . '/assets/hooks/handle_non_admin_users.php';
// include get_stylesheet_directory() . '/assets/hooks/not-logged-in-hidden-blocks.php';
// include get_stylesheet_directory() . '/assets/hooks/query-loop.php';
// include \get_stylesheet_directory() . '/assets/shortcodes/display_memberpages_content.php';
// include \get_stylesheet_directory() . '/assets/cpt/member_pages.php';

// add_action('enqueue_block_editor_assets', __NAMESPACE__ . '\wtc_block_editor_assets', 99);

// function wtc_block_editor_assets() {
//    wp_enqueue_style('memberpages', get_stylesheet_directory_uri() . '/build/css/member-pages.css', [], filemtime(get_stylesheet_directory() . '/build/css/member-pages.css'));
//    // wp_enqueue_style('jquery-ui', get_stylesheet_directory_uri() . '/assets/js/jquery-ui-1.14.1.custom/jquery-ui.min.css');
//    wp_enqueue_script('jquery-ui', get_stylesheet_directory_uri() . '/assets/js/jquery-ui-1.14.1.custom/jquery-ui.min.js', ['jquery']);
//    wp_enqueue_script('memberpages', get_stylesheet_directory_uri() . '/src/js/member-pages.js', ['jquery', 'jquery-ui'], filemtime(get_stylesheet_directory() . '/src/js/member-pages.js'));
// }
