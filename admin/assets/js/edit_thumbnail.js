jQuery(document).ready(function($) {
    $('.upload-thumbnail-button').click(function(e) {
        e.preventDefault();

        var demoID = $(this).data('demo-id');
        var fileInput = $('#file-input-' + demoID)[0].files[0];
        console.log(demoID);
        // Ensure a file is selected
        if (fileInput) {
            var formData = new FormData();
            formData.append('file', fileInput);
            formData.append('demo_id', demoID);
            formData.append('action', 'upload_thumbnail');
            formData.append('security', thumbnaildata.nonce);

            // Perform the AJAX request to upload the file
            $.ajax({
                url: ajaxurl,  // WordPress's AJAX URL
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Thumbnail updated successfully!');
                        // Update the thumbnail image dynamically
                        $('#thumbnail-' + demoID).attr('src', response.data.new_thumbnail_url);
                         location.reload();
                    } else {
                        alert('Error updating thumbnail.');
                    }
                }
            });
        } else {
            alert('Please select a file to upload.');
        }
    });
    
    // Delete thumbnail button click event
    $('.delete-thumbnail-button').click(function(e) {
        e.preventDefault();

        var demoID = $(this).data('demo-id');

        // Confirm the delete action
        if (confirm('Are you sure you want to delete this thumbnail?')) {
            // Perform the AJAX request to delete the thumbnail
            $.ajax({
                url: ajaxurl,  // WordPress's AJAX URL
                type: 'POST',
                data: {
                    action: 'delete_thumbnail',
                    demo_id: demoID,
                    security: thumbnaildata.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert('Thumbnail deleted successfully!');
                        // Optionally remove the image from the frontend
                        $('#thumbnail-' + demoID).remove();  // Remove the image element
                        location.reload();
                    } else {
                        alert('Error deleting thumbnail.');
                    }
                }
            });
        }
    });
     
    
    // Click event to show edit inputs for demo name and thumbnail
    $('.edit-demo-button').click(function() {
        var demoID = $(this).data('demo-id');

        // Show the demo name input and thumbnail URL input
        $('#demo-name-' + demoID + ' .demo-name-text').hide();
        $('#demo-name-' + demoID + ' .edit-demo-name-input').show();
        $('#edit-thumbnail-' + demoID).show();
        $('.save-demo-button[data-demo-id="' + demoID + '"]').show(); // Show Save button
        $(this).hide(); // Hide Edit button
    });

    // Save button click event to save the edited demo name and thumbnail
    $('.save-demo-button').click(function(e) {
        e.preventDefault();
        
        var demoID = $(this).data('demo-id');
        var newDemoName = $('#demo-name-' + demoID + ' .edit-demo-name-input').val();
        // var newThumbnail = $('#edit-thumbnail-' + demoID + ' .edit-thumbnail-input').val();

        // Perform AJAX request to save the new demo name and thumbnail
        $.ajax({
            url: ajaxurl,  // WordPress's AJAX URL
            type: 'POST',
            data: {
                action: 'edit_demo_and_thumbnail',
                demo_id: demoID,
                new_demo_name: newDemoName,
                security: thumbnaildata.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Demo updated successfully!');
                    
                    // Update frontend with new values
                    $('#demo-name-' + demoID + ' .demo-name-text').text(newDemoName).show();
                    // $('#thumbnail-' + demoID).attr('src', plugin_dir_url + 'thumbnail/' + newThumbnail);
                    $('#demo-name-' + demoID + ' .edit-demo-name-input').hide();
                    $('#edit-thumbnail-' + demoID).hide();
                    $('.save-demo-button[data-demo-id="' + demoID + '"]').hide();
                    $('.edit-demo-button[data-demo-id="' + demoID + '"]').show(); // Show Edit button again
                } else {
                    alert('Error updating demo.');
                }
            }
        });
    });

    
});


