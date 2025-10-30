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
   wp_enqueue_style('custom', get_stylesheet_directory_uri() . ENV_TYPE . '/css/editor.css', [], filemtime(get_stylesheet_directory() . ENV_TYPE . '/css/screen.css'));
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
      error_log(print_r(['transition_post_status_callback' => $post->post_type], true));
      $metaboxes->update_json_file();
   endif;
}
/**
add_shortcode('machines_json', __NAMESPACE__ . '\machines_json_callback');

function machines_json_callback() {
   ob_start();

   $machines = [];

   $query = new \WP_Query([
      'post_type' => 'machines',
      'posts_per_page' => -1,
      'return' => 'ids'
   ]);

   if ($query->have_posts()) :
      while ($query->have_posts()) :
         $query->the_post();
         $id = get_the_ID();
         $thumbnail_id = get_post_thumbnail_id($id);
         $machines[] = (object) [
            'id' => $id,
            'title' => get_the_title($id),
            'excerpt' => get_the_excerpt($id),
            'permalink' => get_the_permalink($id),
            'meta_fields' => (object) [
               'designation' => get_post_meta($id, 'designation', true),
               'quality' => get_post_meta($id, 'quality', true),
               'status' => get_post_meta($id, 'status', true),
               'year' => get_post_meta($id, 'year', true),
            ],
            'featured_image' => (object) [
               'id' => $thumbnail_id,
               'src' => (object) [
                  'thumbnail' => wp_get_attachment_image_url($thumbnail_id, 'thumbnail'),
                  'medium' => wp_get_attachment_image_url($thumbnail_id, 'medium'),
                  'medium_large' => wp_get_attachment_image_url($thumbnail_id, 'medium_large'),
                  'large' => wp_get_attachment_image_url($thumbnail_id, 'large'),
                  'full' => wp_get_attachment_image_url($thumbnail_id, 'full'),
               ]
            ]
         ];
      endwhile;
   endif;

   echo '<pre>';
   print_r($machines);
   echo '</pre>';

   return ob_get_clean();
}

add_action('save_post_machines', __NAMESPACE__ . '\save_post_machines_callback', 3, 999);

function save_post_machines_callback(int $post_id, \WP_POST $post, bool $update) {
   error_log(print_r('saving json', true));
   $uploads = wp_upload_dir();
   
   $machines = [];

   $query = new \WP_Query([
      'post_type' => 'machines',
      'posts_per_page' => -1,
      'return' => 'ids'
   ]);

   if ($query->have_posts()) :
      while ($query->have_posts()) :
         $query->the_post();
         $id = get_the_ID();
         $thumbnail_id = get_post_thumbnail_id($id);
         $machines[] = (object) [
            'id' => $id,
            'title' => get_the_title($id),
            'excerpt' => get_the_excerpt($id),
            'permalink' => get_the_permalink($id),
            'meta_fields' => (object) [
               'designation' => get_post_meta($id, 'designation', true),
               'quality' => get_post_meta($id, 'quality', true),
               'status' => get_post_meta($id, 'status', true),
               'year' => get_post_meta($id, 'year', true),
            ],
            'featured_image' => (object) [
               'id' => $thumbnail_id,
               'src' => (object) [
                  'thumbnail' => wp_get_attachment_image_url($thumbnail_id, 'thumbnail'),
                  'medium' => wp_get_attachment_image_url($thumbnail_id, 'medium'),
                  'medium_large' => wp_get_attachment_image_url($thumbnail_id, 'medium_large'),
                  'large' => wp_get_attachment_image_url($thumbnail_id, 'large'),
                  'full' => wp_get_attachment_image_url($thumbnail_id, 'full'),
               ]
            ]
         ];
      endwhile;
   endif;

   $upload_dir = wp_get_upload_dir(); // set to save in the /wp-content/uploads folder
   $file_name = 'machines.json';
   $save_path = $upload_dir['basedir'] . '/data/' . $file_name;

   $f = fopen($save_path, "w"); //if json file doesn't gets saved, comment this and uncomment the one below
   //$f = @fopen( $save_path , "w" ) or die(print_r(error_get_last(),true)); //if json file doesn't gets saved, uncomment this to check for errors
   fwrite($f, json_encode($machines));
   fclose($f);
    
}
 */
