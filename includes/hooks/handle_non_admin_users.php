<?php

namespace WTC\Assets\Hooks;

add_action('init', __NAMESPACE__ . '\handle_non_admin_users');

function handle_non_admin_users() {
   // Show admin bar only for admins
   if (!current_user_can('manage_options')) {
      add_filter('show_admin_bar', '__return_false');
   }
   // Redirect non-admin users trying to access wp-admin to the member page
   if (is_admin() && !current_user_can('administrator') && !(defined('DOING_AJAX') && DOING_AJAX)) {
      wp_redirect(home_url() . '/medlemssida/');
      exit;
   }
}
