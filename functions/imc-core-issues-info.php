<?php

/**
 * 5.01
 * Add Box with Lat-Lng-Address-Votes
 *
 */

//This imc_prefix will be added before all of our custom fields
$imc_prefix = 'imc_';


//All information about our meta box
$imc_infobox = array(
    'id' => 'imc-infobox',
    'page' => 'imc_issues',
    'context' => 'normal',
    'priority' => 'high',
    'fields' => array(
        array(
            'name' => 'Likes',
            'desc' => 'votes',
            'id' => $imc_prefix . 'likes',
            'type' => 'numeric',
            'std' => ''
        ),
        array(
            'name' => 'Lat',
            'desc' => 'Latitude',
            'id' => $imc_prefix . 'lat',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'Lng',
            'desc' => 'Longitude',
            'id' => $imc_prefix . 'lng',
            'type' => 'text',
            'std' => ''
        ),
        array(
            'name' => 'Address',
            'desc' => 'Address',
            'id' => $imc_prefix . 'address',
            'type' => 'text',
            'std' => ''
        )
    )
);


function imcplus_issues_infobox_add() {
    global $imc_infobox;
    add_meta_box($imc_infobox['id'], __('Issue Location','participace-projekty'), 'imcplus_issues_infobox_show', $imc_infobox['page'], $imc_infobox['context'], $imc_infobox['priority']);
}

add_action('admin_menu', 'imcplus_issues_infobox_add');
// Add meta box with previous information

/************************************************************************************************************************/

/**
 * 5.02
 * Data for Box with Lat-Lng-Address-Votes
 *
 */


// Callback function to show fields in infobox
function imcplus_issues_infobox_show() {
    global $imc_infobox, $post;
    // Use nonce for verification
    echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    echo '<table class="form-table" id="imc-custom-fields-table">';
    foreach ($imc_infobox['fields'] as $field) {
        // get current post meta data
        $meta = get_post_meta($post->ID, $field['id'], true);
        echo '<tr>',
        '<th style="width:20%"><label for="', esc_attr($field['id']), '">', esc_html($field['name']), '</label></th>',
        '<td>';


        switch ($field['type']) {
            case 'text':
                echo '<input type="text" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="', esc_attr($meta ? $meta : $field['std']), '" size="30" style="width:97%" />', '<br />', esc_html($field['desc']);
                break;
            case 'numeric':
                echo '<input type="number" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" value="', esc_attr($meta ? $meta : $field['std']), '" size="30" style="width:97%" />', '<br />', esc_html($field['desc']);
                break;
            case 'textarea':
                echo '<textarea name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '" cols="60" rows="4" style="width:97%">', esc_attr($meta ? $meta : $field['std']), '</textarea>', '<br />', esc_html($field['desc']);
                break;
            case 'select':
                echo '<select name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '">';
                foreach ($field['options'] as $option) {
                    echo '<option ', $meta == $option ? ' selected="selected"' : '', '>', esc_html($option), '</option>';
                }
                echo '</select>';
                break;
            case 'checkbox':
                echo '<input type="checkbox" name="', esc_attr($field['id']), '" id="', esc_attr($field['id']), '"', $meta ? ' checked="checked"' : '', ' />';
                break;

        }
        echo     '</td><td>',
        '</td></tr>';
    }
    echo '</table>';

    ?>

    <input placeholder="<?php echo __('Enter an address','participace-projekty'); ?>" type="text" id="map_input_id" class="IMCBackendInputLargeStyle" />

    <button type="button" onclick="imcFindAddress('map_input_id', true);" class="IMCBackendButtonStyle">
        <span class="dashicons dashicons-admin-site"></span>
        <?php echo _e('Locate address','participace-projekty'); ?>

    </button>

    <div id="map-canvas" class="IMCBackendIssueMapStyle"></div>

    <script>

        jQuery( document ).ready(function() {

            /*Google Maps API*/
            google.maps.event.addDomListener(window, 'load', loadDefaultMapValues);

            function loadDefaultMapValues() {
                "use strict";

		        <?php $map_options = get_option('gmap_settings'); ?>

                var mapId = "map-canvas";
                var inputId = "map_input_id";

                // Checking the saved latlng on custom fields
                var lat = document.getElementById("imc_lat").value;
                var lng = document.getElementById("imc_lng").value;

                if (lat === '' || lng === '' ) {
                    lat = parseFloat('<?php echo floatval($map_options["gmap_initial_lat"]); ?>');
                    lng = parseFloat('<?php echo floatval($map_options["gmap_initial_lng"]); ?>');
                    if (lat === '' || lng === '' ) { lat = 40.1349854; lng = 22.0264538; }
                }

                // Options casting if empty
                var zoom = parseInt("<?php echo intval($map_options["gmap_initial_zoom"], 10); ?>", 10);
                if(!zoom){ zoom = 7; }

                var allowScroll;
                "<?php echo intval($map_options["gmap_mscroll"], 10); ?>" === '1' ? allowScroll = true : allowScroll = false;

                var boundaries = <?php echo json_encode($map_options["gmap_boundaries"]);?> ?
			        <?php echo json_encode($map_options["gmap_boundaries"]);?>: null;

                document.getElementById('map_input_id').value = "<?php echo esc_html($map_options['gmap_initial_address']); ?>";

                imcInitializeMap(lat, lng, mapId, inputId, true, zoom, allowScroll, JSON.parse(boundaries));

                imcFindAddress('map_input_id', false, lat, lng);

            }

        });



    </script>

    <?php
}

/************************************************************************************************************************/

/**
 * 5.03
 * Save Data @ Box with Lat-Lng-Address-Votes
 *
 */


function imcplus_issues_infobox_save($post_id) {
    global $imc_infobox;
    // verify nonce
    if ( !isset( $_POST['mytheme_meta_box_nonce'] ) || !wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))) {
        return $post_id;
    }
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }
    // check permissions
    if ('page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return $post_id;
        }
    } elseif (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }
    foreach ($imc_infobox['fields'] as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    }
}

// Save data from infobox
add_action('save_post', 'imcplus_issues_infobox_save');


/************************************************************************************************************************/

/**
 * 5.04
 * Hide Custom Field panel from imc_issues
 *
 */


function imcplus_custfields_remove() {
    remove_meta_box( 'postcustom' , 'imc_issues' , 'normal' );
}

add_action( 'admin_menu' , 'imcplus_custfields_remove' );


?>
