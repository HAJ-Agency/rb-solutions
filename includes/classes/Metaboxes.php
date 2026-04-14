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
    public $selected_status, $selected_quality, $selected_year, $selected_machine_type;

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
        $this->selected_machine_type = get_post_meta($post->ID, 'machine_type', true) ? get_post_meta($post->ID, 'machine_type', true) : 'Machine Type';
        $this->selected_quality = get_post_meta($post->ID, 'quality', true) ? get_post_meta($post->ID, 'quality', true) : 'New';
        $this->selected_status = get_post_meta($post->ID, 'status', true) ? get_post_meta($post->ID, 'status', true) : 'For Sale';
        $this->selected_year = get_post_meta($post->ID, 'year', true) ? get_post_meta($post->ID, 'year', true) : 1970;
?>
        <div>
            <div>
                <label>Machine Type:</label>                
                <select name="extra[machine_type]">
                    <option value="">-- Select --</option>
                    <option value="Ballast Cleaning machine" <?php selected($this->selected_machine_type, 'Ballast Cleaning machine') ?>>Ballast Cleaning machine</option>
                    <option value="Ballast Profiling machine" <?php selected($this->selected_machine_type, 'Ballast Profiling machine') ?>>Ballast Profiling machine</option>
                    <option value="Multipurpose machine" <?php selected($this->selected_machine_type, 'Multipurpose machine') ?>>Multipurpose machine</option>
                    <option value="Other" <?php selected($this->selected_machine_type, 'Other') ?>>Other</option>
                    <option value="Renovation machine" <?php selected($this->selected_machine_type, 'Renovation machine') ?>>Renovation machine</option>
                    <option value="Stabilizing machine" <?php selected($this->selected_machine_type, 'Stabilizing machine') ?>>Stabilizing machine</option>
                    <option value="Tamping machine" <?php selected($this->selected_machine_type, 'Tamping machine') ?>>Tamping machine</option>
                </select>
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
        return $post_id;
    }
}
