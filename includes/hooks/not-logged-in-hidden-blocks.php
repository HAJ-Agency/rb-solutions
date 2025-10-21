<?php

namespace WTC\Assets\Hooks;

/**
 * Hide content of group blocks with the "hiddenContent" attribute
 * if the user is not logged in.
 *
 * @param string $block_content Default block content.
 * @param array  $block Parsed block.
 * @return string
 */
function hideContentForLoggedOutUsers($block_content, $block) {

   // Only do check on core/group blocks
   if (($block['blockName'] === 'core/group')) :
      // Check if the hiddenContent attribute is set and if it's true
      if (isset($block['attrs']['hiddenContent']) && $block['attrs']['hiddenContent'] === true) :
         // Is the visitor logged in? If so then show the block content. If not then we do not show anything.
         if (\is_user_logged_in()) :
            return $block_content;
         else :
            return '';
         endif;
      else:
         return $block_content;
      endif;

   else:
      return $block_content;
   endif;

   return $block_content;
}
\add_filter('render_block', __NAMESPACE__ . '\hideContentForLoggedOutUsers', 10, 2);
