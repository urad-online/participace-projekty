<?php
/**
 * Handles the 'Project Updates' meta box for imc_issues custom post type.
 */

// Hook to add meta boxes
add_action( 'add_meta_boxes', 'imc_add_project_updates_meta_box' );

/**
 * Register the 'Project Updates' meta box.
 */
function imc_add_project_updates_meta_box() {
    add_meta_box(
        'imc_project_updates', // Unique ID
        __( 'Project Updates', 'participace-projekty' ), // Box title
        'imc_project_updates_meta_box_html', // Content callback, must be of type callable
        'imc_issues', // Post type
        'normal', // Context
        'high' // Priority
    );
}

/**
 * Display the project updates meta box.
 *
 * @param WP_Post $post The current post object.
 */
function imc_project_updates_meta_box_html( $post ) {
    wp_nonce_field( 'imc_save_project_updates_meta', 'imc_project_updates_nonce' );

    $updates = get_post_meta( $post->ID, 'pb_project_updates', true );
    if ( ! is_array( $updates ) ) {
        $updates = array();
    }
    ?>
    <div id="project-updates-container">
        <?php if ( ! empty( $updates ) ) : ?>
            <?php foreach ( $updates as $index => $update ) : 
                $update_id = isset( $update['id'] ) ? esc_attr( $update['id'] ) : uniqid( 'update_' );
                $photo_id = isset( $update['photo_id'] ) ? intval( $update['photo_id'] ) : 0;
                $photo_thumbnail_src = $photo_id ? wp_get_attachment_thumb_url( $photo_id ) : '';
            ?>
            <div class="project-update-item" data-id="<?php echo $update_id; ?>">
                <hr>
                <h4><?php printf( __( 'Update #%s', 'participace-projekty' ), $index + 1 ); ?></h4>
                <p>
                    <label for="update_date_<?php echo $update_id; ?>"><?php _e( 'Date:', 'participace-projekty' ); ?></label>
                    <input type="date" id="update_date_<?php echo $update_id; ?>" name="pb_project_updates[<?php echo $update_id; ?>][date]" value="<?php echo isset( $update['date'] ) ? esc_attr( $update['date'] ) : ''; ?>" class="widefat">
                </p>
                <p>
                    <label for="update_text_<?php echo $update_id; ?>"><?php _e( 'Text:', 'participace-projekty' ); ?></label>
                    <textarea id="update_text_<?php echo $update_id; ?>" name="pb_project_updates[<?php echo $update_id; ?>][text]" class="widefat" rows="5"><?php echo isset( $update['text'] ) ? esc_textarea( $update['text'] ) : ''; ?></textarea>
                </p>
                <div class="photo-uploader-area">
                    <label><?php _e( 'Photo:', 'participace-projekty' ); ?></label>
                    <div class="update-photo-preview" id="photo_preview_<?php echo $update_id; ?>">
                        <?php if ( $photo_thumbnail_src ) : ?>
                            <img src="<?php echo esc_url( $photo_thumbnail_src ); ?>" style="max-width:150px; height:auto;">
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="pb_project_updates[<?php echo $update_id; ?>][photo_id]" id="update_photo_id_<?php echo $update_id; ?>" value="<?php echo $photo_id; ?>">
                    <button type="button" class="button upload-photo-button" data-target-id="<?php echo $update_id; ?>"><?php _e( 'Upload/Change Photo', 'participace-projekty' ); ?></button>
                    <button type="button" class="button remove-photo-button" data-target-id="<?php echo $update_id; ?>" style="<?php echo $photo_id ? '' : 'display:none;'; ?>"><?php _e( 'Remove Photo', 'participace-projekty' ); ?></button>
                </div>
                <input type="hidden" name="pb_project_updates[<?php echo $update_id; ?>][id]" value="<?php echo $update_id; ?>">
                <p>
                    <button type="button" class="button button-danger delete-update-button"><?php _e( 'Delete Update', 'participace-projekty' ); ?></button>
                </p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <button type="button" id="add-new-update-button" class="button button-primary"><?php _e( 'Add New Update', 'participace-projekty' ); ?></button>

    <script type="text/template" id="project-update-item-template">
        <div class="project-update-item" data-id="{id}">
            <hr>
            <h4><?php _e( 'New Update', 'participace-projekty' ); ?></h4>
            <p>
                <label for="update_date_{id}"><?php _e( 'Date:', 'participace-projekty' ); ?></label>
                <input type="date" id="update_date_{id}" name="pb_project_updates[{id}][date]" value="" class="widefat">
            </p>
            <p>
                <label for="update_text_{id}"><?php _e( 'Text:', 'participace-projekty' ); ?></label>
                <textarea id="update_text_{id}" name="pb_project_updates[{id}][text]" class="widefat" rows="5"></textarea>
            </p>
            <div class="photo-uploader-area">
                <label><?php _e( 'Photo:', 'participace-projekty' ); ?></label>
                <div class="update-photo-preview" id="photo_preview_{id}"></div>
                <input type="hidden" name="pb_project_updates[{id}][photo_id]" id="update_photo_id_{id}" value="0">
                <button type="button" class="button upload-photo-button" data-target-id="{id}"><?php _e( 'Upload/Change Photo', 'participace-projekty' ); ?></button>
                <button type="button" class="button remove-photo-button" data-target-id="{id}" style="display:none;"><?php _e( 'Remove Photo', 'participace-projekty' ); ?></button>
            </div>
            <input type="hidden" name="pb_project_updates[{id}][id]" value="{id}">
            <p>
                <button type="button" class="button button-danger delete-update-button"><?php _e( 'Delete Update', 'participace-projekty' ); ?></button>
            </p>
        </div>
    </script>
    <?php
}

// Hook to save post meta
add_action( 'save_post_imc_issues', 'imc_save_project_updates_meta_data' );

/**
 * Save the project updates meta data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function imc_save_project_updates_meta_data( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST['imc_project_updates_nonce'] ) ) {
        return;
    }
    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['imc_project_updates_nonce'], 'imc_save_project_updates_meta' ) ) {
        return;
    }
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // Check the user's permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Check if the 'pb_project_updates' data is set
    if ( ! isset( $_POST['pb_project_updates'] ) || ! is_array( $_POST['pb_project_updates'] ) ) {
        // If no updates are submitted (e.g., all were deleted), save an empty array or delete meta.
        update_post_meta( $post_id, 'pb_project_updates', array() );
        return;
    }

    $submitted_updates = $_POST['pb_project_updates'];
    $sanitized_updates = array();

    foreach ( $submitted_updates as $unique_id => $update_data ) {
        // Ensure the unique ID from the key matches the one in the data for consistency, though not strictly necessary if using $unique_id
        if ( ! isset( $update_data['id'] ) || $update_data['id'] !== $unique_id ) {
            // Potentially log this inconsistency or handle error
            continue; 
        }

        // If a field is marked for deletion (e.g. by JS adding a hidden input like [delete_marker]), skip it.
        // For now, we assume any update entry present in POST should be saved unless its text is empty.
        if ( empty( $update_data['text'] ) && empty( $update_data['date'] ) && empty( $update_data['photo_id'] ) ) {
            continue; // Skip entirely empty entries
        }

        $sanitized_update = array(
            'id'       => sanitize_text_field( $update_data['id'] ), // Sanitize the unique ID
            'date'     => sanitize_text_field( $update_data['date'] ), // Basic sanitization, consider date validation if needed
            'text'     => sanitize_textarea_field( $update_data['text'] ),
            'photo_id' => isset( $update_data['photo_id'] ) ? intval( $update_data['photo_id'] ) : 0,
        );
        $sanitized_updates[] = $sanitized_update;
    }

    // Save the sanitized updates array to post meta
    update_post_meta( $post_id, 'pb_project_updates', $sanitized_updates );
}

/**
 * Enqueue admin scripts for the project updates meta box.
 */
add_action( 'admin_enqueue_scripts', 'imc_project_updates_admin_scripts' );

function imc_project_updates_admin_scripts( $hook_suffix ) {
    global $post_type;

    // Only load on the imc_issues edit screen.
    if ( ( $hook_suffix == 'post.php' || $hook_suffix == 'post-new.php' ) && $post_type == 'imc_issues' ) {
        // Enqueue WordPress media scripts.
        wp_enqueue_media();

        // Enqueue the custom JavaScript file.
        wp_enqueue_script(
            'imc-project-updates-admin-js',
            plugin_dir_url( __FILE__ ) . '../js/imc-project-updates-admin.js', // Adjusted path
            array( 'jquery', 'wp-i18n' ), // Add wp-i18n for localization in JS if needed for more complex scenarios
            filemtime( plugin_dir_path( __FILE__ ) . '../js/imc-project-updates-admin.js' ), // Versioning
            true // Load in footer
        );

        // Localize script with translatable strings for JavaScript.
        wp_localize_script(
            'imc-project-updates-admin-js',
            'imcProjectUpdates', // Object name in JavaScript
            array(
                'confirmDeletion'    => __( 'Are you sure you want to delete this update?', 'participace-projekty' ),
                'mediaUploaderTitle' => __( 'Choose or Upload Photo', 'participace-projekty' ),
                'mediaUploaderButton'=> __( 'Use this photo', 'participace-projekty' ),
                // Add any other strings your JS might need
            )
        );
    }
}
?>
