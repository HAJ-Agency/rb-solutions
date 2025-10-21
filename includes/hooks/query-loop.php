<?php

namespace WTC\Assets\Hooks;

/**
 * Customize the core/post-terms block to add data attributes.
 * This is applied to the categories displayed on each post in the query loop.
 *
 * @param string $block_content Default post terms content.
 * @param array  $block Parsed block.
 * @return string
 */
function customize_core_post_terms_block($block_content, $block) {
   if (($block['blockName'] === 'core/post-terms') && !is_single()) {

      $dom = new \DOMDocument();
      $dom->loadHTML($block_content);
      $content = element_to_object($dom->documentElement);
      $terms = $content['children_html'][0]['children_body'][0] ?? null;

      // Build the new block_content
      $block_content = '<div class="' . $terms['class'] . '" style="' . $terms['style'] . '">';
      foreach ($terms['children_div'] as $term) {
         if ($term['tag'] != 'span') {
            $term_id = get_term_by('slug', sanitize_title($term['html']), 'category')->term_id;
            $block_content .= '<span class="cat-item-' . $term_id . '" data-cat-slug="' . sanitize_title($term['html']) . '">' . $term['html'] . '</span>';
         }
      }
      $block_content .= '</div>';

      return $block_content;
   }

   return $block_content;
}

\add_filter('render_block', __NAMESPACE__ . '\customize_core_post_terms_block', 10, 2);

/**
 * Customize the core/categories block to add data attributes.
 * This is applied to the categories filter in the query loop.
 *
 * @param string $block_content Default categories content.
 * @param array  $block Parsed block.
 * @return string
 */
function customize_core_categories_block($block_content, $block) {

   if (($block['blockName'] === 'core/categories') && (str_contains($block['attrs']['className'], 'posts__filter'))) {
      $dom = new \DOMDocument();
      $dom->loadHTML($block_content);
      $content = element_to_object($dom->documentElement);
      $list = $content['children_html'][0]['children_body'][0] ?? null;

      $block_content = '<ul class="' . $list['class'] . '" style="' . $list['style'] . '">';
      $block_content .= '<li class="cat-item cat-item-0 black active" data-cat-id="" data-cat-count="">' . __('Visa alla', 'wtcgbf') . '</li>';

      foreach ($list['children_ul'] as $li) {
         $count = str_replace(['(', ')'], '', $li['html']);
         $cat_id = str_replace('cat-item cat-item-', '', $li['class']);

         // Check if this term has a sticky post
         // If so, we need to remove the sticky post from the count
         $count = array_map(function ($post_id) use ($count) {
            return is_sticky($post_id) ? --$count : $count;
         }, get_posts([
            'category' => $cat_id,
            'fields'   => 'ids',
            'numberposts' => -1,
         ]));

         // If this category has no posts, we skip it
         if ($count[0] > 0) {            
            $block_content .= '<li class="' . $li['class'] . '" data-cat-id="' . $cat_id . '" data-cat-slug="' . sanitize_title($li['children_li'][0]['html']) . '" data-cat-count="' . trim($count[0]) . '">';
            $block_content .= $li['children_li'][0]['html'];
            $block_content .= '</li>';
         }
      }
      $block_content .= '</ul>';

      return $block_content;
   }

   return $block_content;
}

\add_filter('render_block', __NAMESPACE__ . '\customize_core_categories_block', 10, 2);

/**
 * Replace the pagination block with a View More button.
 *
 * @param string $block_content Default pagination content.
 * @param array  $block Parsed block.
 * @return string
 */
function query_pagination_render_block($block_content, $block) {
   if ($block['blockName'] === 'core/query-pagination') {
      $btn = '<div class="wp-block-buttons is-content-justification-center is-layout-flex wp-block-buttons-is-layout-flex">';
      $btn .= '<div class="wp-block-button is-style-primary-dark posts__show-more-button">';
      $btn .= '<a class="wp-block-button__link wp-element-button" data-total-posts="%d">%s</a>';
      $btn .= '</div>';

      $total_posts = \wp_count_posts()->publish;
      $text = \esc_html__('Visa fler', 'world-trade-center');

      $block_content = sprintf($btn, $total_posts, $text);
   }

   return $block_content;
}

\add_filter('render_block', __NAMESPACE__ . '\query_pagination_render_block', 10, 2);

/**
 * AJAX function render more posts.
 * Fires when the "Load More" button is clicked or when a filter is applied.
 *
 * @return void
 */
function query_pagination_render_more_query() {
   $block = json_decode(stripslashes($_GET['attrs']), true);
   $paged = absint($_GET['paged'] ?? 1);
   $type = stripslashes($_GET['type']);

   if ($block) {
      $temp_offset = $block['attrs']['query']['offset'];
      switch ($type):
         case 'load_more':
            $block['attrs']['query']['offset'] += $block['attrs']['query']['perPage'] * $paged;
            $block['attrs']['query']['taxQuery'] = $block['attrs']['query']['taxQuery'] ?? [];
            break;
         case 'filter':
            $block['attrs']['query']['taxQuery'] = $block['attrs']['query']['taxQuery'] ?? [];
            $block['attrs']['query']['perPage'] = $block['attrs']['query']['perPage'] * $paged;
            // $block['attrs']['query']['offset'] = 0;
            break;
      endswitch;

      /**
       * Filter the query loop block query vars.
       * 
       * @see https://developer.wordpress.org/reference/hooks/query_loop_block_query_vars/
       */
      \add_filter('query_loop_block_query_vars', function ($query) {
         $query['post_status'] = 'publish';
         return $query;
      }, 10, 2);

      echo render_block($block);
   }

   exit;
}

add_action('wp_ajax_query_render_more_pagination', __NAMESPACE__ . '\query_pagination_render_more_query');
add_action('wp_ajax_nopriv_query_render_more_pagination', __NAMESPACE__ . '\query_pagination_render_more_query');

/**
 * Add data attributes to the query block to describe the block query.
 *
 * @param string $block_content Default query content.
 * @param array  $block Parsed block.
 * @return string
 */
function query_render_block($block_content, $block) {
   global $wp_query;


   if ($block['blockName'] === 'core/query') {
      $query_id      = $block['attrs']['queryId'];
      $container_end = strpos($block_content, '>');
      $inherit       = $block['attrs']['query']['inherit'] ?? false;

      // Account for inherited query loops      
      if ($inherit && $wp_query && isset($wp_query->query_vars) && is_array($wp_query->query_vars)) {
         // If Default Query Type is set, we need to set the pages attributes to the block.
         $vars = query_replace_vars($wp_query->query_vars);
         $vars['pages'] = $wp_query->max_num_pages;
         $block['attrs']['query'] = $vars;
      } else {
         // If Custom Query Type is set, we need to replace the pages, perPage and offset vars
         $vars = query_replace_vars($wp_query->query_vars);
         $vars['pages'] = $wp_query->max_num_pages;
         $vars['perPage'] = $block['attrs']['query']['perPage'] ?? $vars['perPage'];
         $vars['sticky'] = $block['attrs']['query']['sticky'] ?? $vars['sticky'];
         $block['attrs']['query'] = $vars;
      }

      $paged = absint($_GET['query-' . $query_id . '-page'] ?? 1);

      $block_content = substr_replace($block_content, ' data-paged="' . esc_attr($paged) . '" data-attrs="' . esc_attr(json_encode($block)) . '"', $container_end, 0);
   }

   return $block_content;
}

\add_filter('render_block', __NAMESPACE__ . '\query_render_block', 10, 2);

/**
 * Replace WP_Query vars format with block attributes format
 *
 * @param array $vars WP_Query vars.
 * @return array
 */
function query_replace_vars($vars) {
   $updated_vars = [
      'postType' => $vars['post_type'] ?? 'post',
      'perPage'  => $vars['posts_per_page'] ?? get_option('posts_per_page', 10),
      'pages'    => $vars['pages'] ?? 0,
      'offset'   => $vars['offset'] ?? 0,
      'order'    => $vars['order'] ?? 'DESC',
      'orderBy'  => $vars['order_by'] ?? '',
      'author'   => $vars['author'] ?? '',
      'search'   => $vars['search'] ?? '',
      'exclude'  => $vars['exclude'] ?? array(),
      'sticky'   => $vars['sticky'] ?? '',
      'inherit'  => false
   ];

   // get the search term from the query string
   $search_term = get_query_var('s');
   if ($search_term) {
      $updated_vars['search'] = $search_term;
   }

   if (\array_key_exists('cat', $vars) && !empty($vars['cat'])) {
      $updated_vars['taxQuery']['category'] = [$vars['cat']];
   }

   if (array_key_exists('tag', $vars) && !empty($vars['tag'])) {
      $updated_vars['taxQuery']['post_tag'] = [$vars['tag']];
   }

   return $updated_vars;
}

function element_to_object($element) {

   $obj = ["tag" => $element->tagName];
   $tag = $element->tagName;
   foreach ($element->attributes as $attribute) {
      $obj[$attribute->name] = $attribute->value;
   }
   foreach ($element->childNodes as $subElement) {
      if ($subElement->nodeType == XML_TEXT_NODE) {
         (trim($subElement->wholeText)) ? $obj["html"] = $subElement->wholeText : false;
      } else {
         $obj["children_" . $tag][] = element_to_object($subElement);
      }
   }

   return $obj;
}
