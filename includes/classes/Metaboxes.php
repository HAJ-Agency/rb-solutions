<?php

/** 
 * ToDo: Fix so this one is autoloaded in composer with namespace
 * @see https://wp-kama.com/2511/creating-custom-fields-metabox
 * @see https://developer.wordpress.org/reference/functions/add_meta_box/
 * 
 */

namespace RBS\INCLUDES\CLASSES;

$metaboxes = new Metaboxes('machines');
$metaboxes->init();

class Metaboxes {
    static $meta_prefix = '';
    public $post_type;
    public $selected_status, $selected_quality, $selected_year, $selected_designation;

    public function __construct($post_type) {
        $this->post_type = $post_type;
    }

    public function init() {
        add_action('add_meta_boxes', [$this, 'add_metabox']);
        add_action('save_post_' . $this->post_type, [$this, 'save_metabox']);
    }

    public function add_metabox() {
        add_meta_box('extra_fields', 'Extra Fields', [$this, 'extra_fields_box_callback'], $this->post_type, 'normal', 'high');
    }

    public function extra_fields_box_callback($post) {
        $this->selected_designation = get_post_meta($post->ID, 'designation', true) ? get_post_meta($post->ID, 'designation', true) : 'Designation';
        $this->selected_quality = get_post_meta($post->ID, 'quality', true) ? get_post_meta($post->ID, 'quality', true) : 'New';
        $this->selected_status = get_post_meta($post->ID, 'status', true) ? get_post_meta($post->ID, 'status', true) : 'For Sale';
        $this->selected_year = get_post_meta($post->ID, 'year', true) ? get_post_meta($post->ID, 'year', true) : 1970;
?>
        <div>
            <div>
                <label>Designation:</label>
                <input type="text" name="extra[designation]" value="<?= $this->selected_designation;  ?>" />
            </div>
            <div>
                <strong>Status:</strong>
                <label>
                    <input type="radio" name="extra[status]" value="For Sale" <?php checked($this->selected_status, 'For Sale') ?> checked="checked" />
                    <span>For Sale</span>
                </label>
                <label>
                    <input type="radio" name="extra[status]" value="For Rent" <?php checked($this->selected_status, 'For Rent') ?> />
                    <span>For Rent</span>
                </label>
            </div>

            <div>
                <strong>Quality:</strong>
                <label>
                    <input type="radio" name="extra[quality]" value="New" <?php checked($this->selected_quality, 'New') ?> checked="checked" />
                    <span>New</span>
                </label>
                <label>
                    <input type="radio" name="extra[quality]" value="Pre-owned" <?php checked($this->selected_quality, 'Pre-owned') ?> />
                    <span>Pre-owned</span>
                </label>
            </div>

            <div>
                Year of Manufacture:
                <select name="extra[year]">
                    <?php
                    for ($year = 1970; $year <= date('Y'); $year++) :
                    ?>
                        <option value="<?= $year ?>" <?php selected($this->selected_year, $year) ?>><?= $year ?></option>
                    <?php
                    endfor;
                    ?>
                </select>
            </div>
        </div>
        <input type="hidden" name="extra_fields_nonce" value="<?= wp_create_nonce('extra_fields_nonce_id') ?>" />
<?php
    }

    public function save_metabox($post_id) {
        if (
            empty($_POST['extra'])
            || ! wp_verify_nonce($_POST['extra_fields_nonce'], 'extra_fields_nonce_id')
            || wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)
        ) {
            return false;
        }

        $extra = $_POST['extra'];

        // All is OK! Now, we need to save/delete the data
        // Clear all data
        $extra = array_map('sanitize_text_field', $extra);
        foreach ($extra as $key => $value) {
            // delete the field if the value is empty
            if (! $value) {
                delete_post_meta($post_id, $key);
            } else {                
                update_post_meta($post_id, $key, $value); // add_post_meta() works automatically
            }
        }
error_log(print_r('save_metabox', true));
        $this->update_json_file();

        return $post_id;
    }

    public function update_json_file() {
        error_log(print_r('saving json', true));

        $machines = [];

        $query = new \WP_Query([
            'post_type' => 'machines',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'return' => 'ids',
        ]);

        if ($query->have_posts()) :
            while ($query->have_posts()) :
                $query->the_post();
                $id = get_the_ID();
                $excerpt = !empty(get_the_excerpt($id)) ? get_the_excerpt($id) : 'Lorem Ipsum Doloris';
                $thumbnail_id = get_post_thumbnail_id($id);

                $thumbnail_url = wp_get_attachment_image_url($thumbnail_id, 'thumbnail') ? wp_get_attachment_image_url($thumbnail_id, 'thumbnail') : 'http://rb-solutions.local/wp-content/uploads/2025/10/placeholder-machine-150x150.png';
                $medium_url = wp_get_attachment_image_url($thumbnail_id, 'medium') ? wp_get_attachment_image_url($thumbnail_id, 'medium') : 'http://rb-solutions.local/wp-content/uploads/2025/10/placeholder-machine-300x225.png';
                $medium_large_url = wp_get_attachment_image_url($thumbnail_id, 'medium_large') ? wp_get_attachment_image_url($thumbnail_id, 'medium_large') : 'http://rb-solutions.local/wp-content/uploads/2025/10/placeholder-machine-768x577.png';
                $large_url = wp_get_attachment_image_url($thumbnail_id, 'large') ? wp_get_attachment_image_url($thumbnail_id, 'large') : 'http://rb-solutions.local/wp-content/uploads/2025/10/placeholder-machine-1024x768.png';
                $full_url = wp_get_attachment_image_url($thumbnail_id, 'full') ? wp_get_attachment_image_url($thumbnail_id, 'full') : 'http://rb-solutions.local/wp-content/uploads/2025/10/placeholder-machine.png';

                $machines[] = (object) [
                    'id' => $id,
                    'title' => get_the_title($id),
                    'excerpt' => $excerpt,
                    'date' => get_post_timestamp($id),
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
                            'thumbnail' => $thumbnail_url,
                            'medium' => $medium_url,
                            'medium_large' => $medium_large_url,
                            'large' => $large_url,
                            'full' => $full_url,
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
}
