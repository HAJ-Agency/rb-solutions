<?php

namespace RBS\INCLUDES\HOOKS;


/**
function mandatory_excerpt($data, $postarr) {
    $excerpt = $data['post_excerpt'];

    if (empty($excerpt)) {
        if ($data['post_status'] === 'publish') {
            add_filter('redirect_post_location', __NAMESPACE__ . '\excerpt_error_message_redirect', 99);
            error_log(print_r(
                [
                    'here' => 'monkeybanana'
                ],
                true
            ));
        }

        $data['post_status'] = 'draft';
    }

    return $data;
}

add_filter('wp_insert_post_data', __NAMESPACE__ . '\mandatory_excerpt', 10, 2);

function excerpt_error_message_redirect($location) {
    error_log(print_r(['here' => 'remove'], true));
    remove_filter('redirect_post_location', __FUNCTION__, 99);
    return add_query_arg('excerpt_required', 1, $location);
}

function excerpt_admin_notice() {
    if (!isset($_GET['excerpt_required'])) return;

    error_log(print_r(['here' => 'there'], true));
    switch (absint($_GET['excerpt_required'])) {
        case 1:
            $message = 'Excerpt is required to publish a post.';
            break;
        default:
            $message = 'Unexpected error';
    }

    echo '<div id="notice" class="error"><p>APAPAPAPAP' . $message . '</p></div>';
}

add_action('admin_notices', __NAMESPACE__ . '\excerpt_admin_notice');
*/