<?php

namespace WTC\Assets\Hooks;

/**
 * Add custom svg icon for core/navigation hamburger
 *    
 * @since 1.0.0
 */
add_filter('render_block', __NAMESPACE__ . '\custom_render_block_core_navigation', 10, 2);

function custom_render_block_core_navigation($block_content, $block) {
   if (
      $block['blockName'] === 'core/navigation' &&
      !is_admin() &&
      !wp_is_json_request()
   ) {
      $new_icon = '<svg width="24" height="16" viewBox="0 0 24 16" fill="none" xmlns="http://www.w3.org/2000/svg">';
      $new_icon .= '<path d="M24 1H-5.96046e-07M24 8H4.23529M24 15H-5.96046e-07" stroke="black" stroke-width="1.5" stroke-linecap="square"/>';
      $new_icon .= '</svg>';
      return preg_replace('/\<svg width(.*?)\<\/svg\>/', $new_icon, $block_content);
   }

   return $block_content;
}
