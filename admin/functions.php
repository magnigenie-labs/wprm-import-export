<?php

function upload_thumbnail_ajax() {
    global $wpdb;

    // Check if the file is set and there are no errors
    if ( isset($_FILES['file']) && $_FILES['file']['error'] == 0 ) {
        $file = $_FILES['file'];
        $demo_id = intval( $_POST['demo_id'] );

        // Define the upload directory
        $upload_dir = plugin_dir_path(__FILE__) . 'thumbnail/';
        $file_name = basename( $file['name'] );
        $target_file = $upload_dir . $file_name;

        // Move the uploaded file to the target directory
        if ( move_uploaded_file($file['tmp_name'], $target_file) ) {
            $table_name = $wpdb->prefix . 'json_data';

            // Update the database with the new thumbnail file name
            $result = $wpdb->update( 
                $table_name, 
                array( 'thumbnail' => $file_name ), 
                array( 'id' => $demo_id )
            );

            if ( $result !== false ) {
                // Return the new thumbnail URL
                wp_send_json_success(array('new_thumbnail_url' => plugin_dir_url(__FILE__) . 'thumbnail/' . $file_name));
            } else {
                wp_send_json_error('Database update failed.');
            }
        } else {
            wp_send_json_error('Failed to move the uploaded file.');
        }
    } else {
        wp_send_json_error('File upload failed.');
    }

    wp_die(); // Required to terminate the AJAX request
}
add_action( 'wp_ajax_upload_thumbnail', 'upload_thumbnail_ajax' );

function delete_thumbnail_ajax() {
    global $wpdb;

    if ( isset($_POST['demo_id']) ) {
        $table_name = $wpdb->prefix . 'json_data';
        $demo_id = intval( $_POST['demo_id'] );

        // Get the current thumbnail file path from the database
        $thumbnail = $wpdb->get_var( $wpdb->prepare( "SELECT thumbnail FROM $table_name WHERE id = %d", $demo_id ) );

        if ( $thumbnail ) {
            // Define the thumbnail file path
            $file_path = plugin_dir_path(__FILE__) . 'thumbnail/' . $thumbnail;

            // Check if the file exists and delete it
            if ( file_exists($file_path) ) {
                unlink($file_path);
            }

            // Update the database to remove the thumbnail reference
            $result = $wpdb->update( 
                $table_name, 
                array( 'thumbnail' => '' ), // Set the thumbnail field to empty
                array( 'id' => $demo_id )
            );

            if ( $result !== false ) {
                wp_send_json_success();
            } else {
                wp_send_json_error('Failed to update the database.');
            }
        } else {
            wp_send_json_error('Thumbnail not found.');
        }
    } else {
        wp_send_json_error('Invalid request.');
    }

    wp_die(); // Terminate the AJAX request
}
add_action( 'wp_ajax_delete_thumbnail', 'delete_thumbnail_ajax' );

// Function to handle editing demo name
function edit_demo_name() {
    global $wpdb;

    if ( isset($_POST['demo_id']) && isset($_POST['new_demo_name']) ) {
        $table_name = $wpdb->prefix . 'json_data';
        $demo_id = intval( $_POST['demo_id'] );
        $new_demo_name = sanitize_text_field( $_POST['new_demo_name'] );
        // $new_thumbnail = sanitize_text_field( $_POST['new_thumbnail'] );

        // Update the demo name and thumbnail in the database
        $result = $wpdb->update(
            $table_name,
            array( 
                'demoname' => $new_demo_name
                // 'thumbnail' => $new_thumbnail
            ),
            array( 'id' => $demo_id )
        );

        if ( $result !== false ) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Database update failed.');
        }
    } else {
        wp_send_json_error('Invalid request.');
    }

    wp_die();
}
add_action( 'wp_ajax_edit_demo_and_thumbnail', 'edit_demo_name' );


 

