<?php

namespace RBS\INCLUDES\HOOKS;

use RBS\INCLUDES\CLASSES\Metaboxes;

define('ENV_TYPE', (WP_ENVIRONMENT_TYPE === 'development' ?  '/dev' : '/build'));

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\wp_enqueue_scripts_cb', 99);

function wp_enqueue_scripts_cb() {
   wp_enqueue_script('mixitup', get_stylesheet_directory_uri() . '/assets/mixitup/mixitup.min.js', []);
   wp_enqueue_script('mixitup-pagination', get_stylesheet_directory_uri() . '/assets/mixitup/mixitup-pagination.min.js', []);
   wp_enqueue_script('mixitup-multifilter', get_stylesheet_directory_uri() . '/assets/mixitup/mixitup-multifilter.min.js', []);
   wp_enqueue_style('custom-fr', get_stylesheet_directory_uri() . ENV_TYPE . '/css/screen.css', [], filemtime(get_stylesheet_directory() . ENV_TYPE . '/css/screen.css'));
   wp_enqueue_script('custom-fr', get_stylesheet_directory_uri() . ENV_TYPE . '/js/screen.js',  ['jquery'], '1.0.0');
}

add_action('admin_enqueue_scripts', __NAMESPACE__ . '\admin_enqueue_scripts_cb', 99);

function admin_enqueue_scripts_cb() {
   wp_enqueue_style('custom-admin', get_stylesheet_directory_uri() . ENV_TYPE . '/css/editor.css', [], filemtime(get_stylesheet_directory() . ENV_TYPE . '/css/editor.css'));
}

add_action('enqueue_block_assets', __NAMESPACE__ . '\enqueue_block_assets_cb');

function enqueue_block_assets_cb() {
   wp_enqueue_script('custom', get_stylesheet_directory_uri() . ENV_TYPE . '/js/editor.js',  ['wp-blocks', 'wp-dom-ready', 'wp-edit-post'], '1.0.0');
}

add_action('init', __NAMESPACE__ . '\add_button_custom_styles_cb', 999);

function add_button_custom_styles_cb() {
   if (function_exists('register_block_style')) {
      \register_block_style(
         'core/button',
         [
            'name'  => 'orange-default',
            'label' => __('Orange Default', "rb-solutions"),
            'is_default' => true,
         ]
      );
      \register_block_style(
         'core/button',
         [
            'name'  => 'gray-default',
            'label' => __('Gray Default', "rb-solutions"),
         ]
      );
      \register_block_style(
         'core/button',
         [
            'name'  => 'gray-wide',
            'label' => __('Gray Wide', "rb-solutions"),
         ]
      );
      \register_block_style(
         'core/button',
         [
            'name'  => 'text-only',
            'label' => __('Text Only', "rb-solutions"),
         ]
      );
   }
}

add_action('transition_post_status', __NAMESPACE__ . '\transition_post_status_callback', 10, 3);

function transition_post_status_callback($new_status, $old_status, $post) {
   if ($post->post_type == "machines") :
      if ($old_status == $new_status) :
         return;
      endif;
      $metaboxes = new Metaboxes('machines');      
      $metaboxes->update_json_file();
   endif;
}