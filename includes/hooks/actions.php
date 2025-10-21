<?php

namespace RBS\INCLUDES\HOOKS;

add_action('wp_enqueue_scripts', __NAMESPACE__ . '\rbs_assets_enqueue', 99);

function rbs_assets_enqueue() {
   $env = \str_contains($_SERVER['HTTP_HOST'], '.local') || \str_contains($_SERVER['HTTP_HOST'], 'localhost') ? '/dev' : '/build';
   wp_enqueue_style('rbs-style', get_stylesheet_directory_uri() . $env . '/css/base.css', [], filemtime(get_stylesheet_directory() . $env . '/css/base.css'));
}
