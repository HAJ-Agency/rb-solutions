<?php

namespace WTC\Assets\Shortcodes;

add_shortcode('display_memberpages_content', __NAMESPACE__ . '\display_memberpages_content_callback');
add_shortcode('display_member_name', function () {
   ob_start();
?>
   <p class="user-display-name has-neutral-white-color has-text-color has-link-color has-news-gothic-std-font-family" style="letter-spacing:1px;line-height:1;text-transform:uppercase">
      <?= get_userdata(get_current_user_id())->display_name; ?>
   </p>
<?php
   return ob_get_clean();;
});

function display_memberpages_content_callback() {
   ob_start();

   /**
    * Query to get all direct child pages of post type "memberpages"
    */
   $args = [
      'order'           => 'ASC',
      'orderby'         => 'parent',
      'post_status'     => 'publish',
      'post_type'       => 'memberpages',
      'posts_per_page'  => -1
   ];
   $query = new \WP_Query($args);

   $posts = [];
   if ($query->have_posts()) :
      /**
       * Get all the data from the query and organize it in a multidimensional array
       * with parents and their children
       */
      if ($query->have_posts()) :
         while ($query->have_posts()) :
            $query->the_post();
            $postId = get_the_ID();
            $post = get_post($postId);
            $hasParent = $post->post_parent !== 0;
            $parentId = $hasParent ? $post->post_parent : 0;

            if ($hasParent) {
               $posts[$parentId]['children'][] = [
                  'hasParent' => $hasParent,
                  'parentId' => $parentId,
                  'id'    => $postId,
                  'tags' => get_the_tags($postId) ? array_map(function ($tag) {
                     return [$tag->name, $tag->term_id];
                  }, get_the_tags($postId)) : [],
                  'title' => get_the_title(),
               ];
            } else {
               /**
                * Get all of this posts childrens tag ids
                */
               $childrenIds = get_children(['post_parent' => $postId, 'post_type' => 'memberpages', 'fields' => 'ids']);

               /**
                * Get all tags objects for each child and map them to an array
                * of arrays with tag name and id.
                */
               $childrenAllTags = array_map(function ($childId) {
                  return array_map(function ($tag) {
                     return [$tag->name, $tag->term_id];
                  }, get_the_tags($childId) ?: []);
               }, $childrenIds);

               /**
                * Merge all arrays of tags into one and remove duplicates
                */
               $childrenUniqueTags = array_unique(array_merge(...$childrenAllTags), SORT_REGULAR);

               $posts[$postId] = [
                  'hasParent' => false,
                  'id'    => $postId,
                  'menuOrder' => get_post_field('menu_order', $postId),
                  'title' => get_the_title(),
                  'childrenTags' => $childrenUniqueTags,
               ];
            }

         endwhile;
         wp_reset_postdata();
      endif;
   endif;

?>
   <div class="memberpages-content">
      <div id="wtc-tabs--parent">
         <?php
         $parentCount = 0;

         /**
          * Rename $posts to $parents for clarity
          */
         $parents = $posts;

         $totalParents = count($parents);

         /** 
          * Sort the parents by their menu order
          */
         usort($parents, function ($a, $b) {
            return $a['menuOrder'] <=> $b['menuOrder'];
         });

         foreach ($parents as $parent) :
            echo $parentCount === 0 ? '<ul class="wtc-tabs--main">' : '';
            echo displayTab('#', 'wtc-tabs--parent-' . $parent['id'], $parent['title']);
            $parentCount++;
            echo $parentCount === $totalParents ? '</ul>' : '';
         endforeach;

         /**
          * Get only the children posts
          */
         $children[] = array_map(function ($post) {
            return $post['children'] ?? [];
         }, array_filter($posts, function ($post) {
            return $post['hasParent'] === false;
         }));

         foreach ($children[0] as $parentId => $childrenCollection) :

            /**
             * Get only the children that belong to this parent
             */
            $children = array_map(function ($child) use ($parentId) {
               return $child['parentId'] == $parentId ? $child : null;
            }, $childrenCollection);

            /**
             * Sort the children alphabetically by title key
             */
            array_multisort(array_column($children, 'title'), SORT_ASC, $children);

            $doTheseChildrenHaveTags = array_filter($children, function ($child) {
               return !empty($child['tags']);
            });

            $allTags = array_unique(array_merge(...array_map(function ($child) {
               return $child['tags'];
            }, $doTheseChildrenHaveTags)), SORT_REGULAR);

            echo '<div id="wtc-tabs--parent-' . $parentId . '">';

            renderPostContent($parentId);

            $chunksClass = (sizeof($children) && sizeof($children) > 1) ? ' has-two-columns' : '';

            echo '<div id="wtc-tabs--child-' . $parentId . '" class="wtc-tabs--child' . $chunksClass . '" data-parentId="' . $parentId . '">';

            /**
             * If the children have tags, create a list with the tags
             * and filter the children based on the selected tag.
             */
            if ($doTheseChildrenHaveTags) :
               echo '<ul class="wtc-tabs--tags">';

               foreach ($allTags as $tag) :
                  echo displayTab('#', 'wtc-tabs--child-' . $parentId . '-' . $tag[1], $tag[0]);
               endforeach;

               echo '</ul>';

               echo '<div class="wtc-tabs--content">';
               foreach ($allTags as $tag) :
                  $tagTermId = $tag[1];
                  echo '<div id="wtc-tabs--child-' . $parentId . '-' . $tagTermId . '" class="wtc-tabs--children">';

                  /**
                   * Filter the children to only those that have the current tag
                   */
                  $theseChildren = array_filter($children, function ($child) use ($tagTermId) {
                     return !empty($child['tags']) && in_array($tagTermId, array_map(function ($tag) {
                        return $tag[1];
                     }, $child['tags']));
                  });

                  makeChildrenIntoAccordions($theseChildren, $parentId);

                  echo '</div>';
               endforeach;
               echo '</div>';
            else :

               if (sizeof($children)) :
                  if (sizeof($children) > 1) :
                     /**
                      * Split the children into groups of 2 for a two column layout
                      */
                     $chunks = array_chunk($children, 2);

                     foreach ($chunks as $key => $chunk) :
                        makeChildrenIntoAccordions($chunk, $parentId . '-' . $key, ' chunk');
                     endforeach;

                  else :

                     echo '<div class="wtc-tabs--content">';
                     makeChildrenIntoAccordions($children, $parentId);
                     echo '</div>';

                  endif;

               endif;

            endif;

            echo '</div>';
            echo '</div>';

         endforeach;
         ?>
      </div>
   <?php
   return ob_get_clean();
}

function makeChildrenIntoAccordions($children, $parentId, $classSuffix = '') {
   if (empty($children)) return;
   echo '<div id="wtc-accordion-' . $parentId . '" class="wtc-accordion' . $classSuffix . '">';
   foreach ($children as $child) :
      echo '<h5>' . $child['title'] . '</h5>';
      renderPostContent($child['id'], true);
   endforeach;
   echo '</div>';
}

/**
 * Helper function to create a tab list item
 * 
 * @param string $tabLink The link to the tab content
 * @param string $tabTitle The title of the tab
 * @return string The HTML for the tab list item
 */
function displayTab($selector = "#", $tabLink, $tabTitle) {
   return '<li><a href="' . $selector . $tabLink . '">' . $tabTitle . '</a></li>';
}

/**
 * Render the post content by parsing the blocks and applying the_content filter
 * to each block.
 * 
 * @param int $postId The ID of the post to render
 * @return void
 */
function renderPostContent($postId, $wrapInDiv = false) {
   $postContent = get_the_content(null, false, $postId);
   $parsedContent = \parse_blocks($postContent);
   if ($wrapInDiv) {
      echo '<div class="post-content">';
   }
   foreach ($parsedContent as $block) :
      echo \do_shortcode(\render_block($block));
   endforeach;

   if ($wrapInDiv) {
      echo '</div>';
   }
}
