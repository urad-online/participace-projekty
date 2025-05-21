jQuery(document).ready(function($) {
    var mediaUploader;

    // Add new update item
    $('#add-new-update-button').on('click', function() {
        var template = $('#project-update-item-template').html();
        // Generate a unique ID for the new item (simple timestamp-based for frontend uniqueness)
        var uniqueId = 'new_' + Date.now(); 
        var newItemHtml = template.replace(/{id}/g, uniqueId);
        $('#project-updates-container').append(newItemHtml);
    });

    // Delete update item
    $('#project-updates-container').on('click', '.delete-update-button', function() {
        if (confirm(imcProjectUpdates.confirmDeletion)) {
            $(this).closest('.project-update-item').remove();
        }
    });

    // Handle photo upload/change
    $('#project-updates-container').on('click', '.upload-photo-button', function(e) {
        e.preventDefault();
        var $button = $(this);
        var targetId = $button.data('target-id');

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: imcProjectUpdates.mediaUploaderTitle,
            button: {
                text: imcProjectUpdates.mediaUploaderButton
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#update_photo_id_' + targetId).val(attachment.id);
            var thumbnailUrl = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
            $('#photo_preview_' + targetId).html('<img src="' + thumbnailUrl + '" style="max-width:150px; height:auto;">');
            $button.siblings('.remove-photo-button').show();
        });

        mediaUploader.open();
    });

    // Handle photo removal
    $('#project-updates-container').on('click', '.remove-photo-button', function() {
        var $button = $(this);
        var targetId = $button.data('target-id');
        $('#update_photo_id_' + targetId).val('0'); // Set to 0 or empty to indicate removal
        $('#photo_preview_' + targetId).html('');
        $button.hide();
    });
});
