<?php

function wprm_ajax_filter_upload_dir_thumb( $dirs ) {
    $dirs['subdir'] = '/wprm-templates/thumbnail';
    $dirs['path']   = $dirs['basedir'] . '/wprm-templates/thumbnail';
    $dirs['url']    = $dirs['baseurl'] . '/wprm-templates/thumbnail';
    return $dirs;
}

function upload_thumbnail_ajax() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Forbidden', 403 );
    }

    check_ajax_referer( 'wprm_ajax_nonce', 'security' );

    global $wpdb;

    if ( isset($_FILES['file']) && $_FILES['file']['error'] == 0 ) {
        $file = $_FILES['file'];
        $demo_id = intval( $_POST['demo_id'] );

        add_filter( 'upload_dir', 'wprm_ajax_filter_upload_dir_thumb' );
        $upload_overrides = array( 'test_form' => false );
        $thumb_upload = wp_handle_upload( $file, $upload_overrides );
        remove_filter( 'upload_dir', 'wprm_ajax_filter_upload_dir_thumb' );

        if ( ! isset( $thumb_upload['error'] ) ) {
            $file_name = basename( $thumb_upload['file'] );
            $table_name = $wpdb->prefix . 'wprm_import_export_data';

            $result = $wpdb->update( 
                $table_name, 
                array( 'thumbnail' => $file_name ), 
                array( 'id' => $demo_id )
            );

            if ( $result !== false ) {
                wp_send_json_success(array('new_thumbnail_url' => wprm_get_upload_url( 'thumbnail' ) . '/' . $file_name));
            } else {
                wp_send_json_error('Database update failed.');
            }
        } else {
            wp_send_json_error( $thumb_upload['error'] );
        }
    } else {
        wp_send_json_error('File upload failed.');
    }

    wp_die();
}
add_action( 'wp_ajax_upload_thumbnail', 'upload_thumbnail_ajax' );

function delete_thumbnail_ajax() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Forbidden', 403 );
    }

    check_ajax_referer( 'wprm_ajax_nonce', 'security' );

    global $wpdb;

    if ( isset($_POST['demo_id']) ) {
        $table_name = $wpdb->prefix . 'wprm_import_export_data';
        $demo_id = intval( $_POST['demo_id'] );

        $thumbnail = $wpdb->get_var( $wpdb->prepare( "SELECT thumbnail FROM $table_name WHERE id = %d", $demo_id ) );

        if ( $thumbnail ) {
            $file_path = wprm_get_upload_path( 'thumbnail' ) . '/' . $thumbnail;

            if ( file_exists($file_path) ) {
                unlink($file_path);
            }

            $result = $wpdb->update( 
                $table_name, 
                array( 'thumbnail' => '' ), 
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

    wp_die();
}
add_action( 'wp_ajax_delete_thumbnail', 'delete_thumbnail_ajax' );

function edit_demo_name() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Forbidden', 403 );
    }

    check_ajax_referer( 'wprm_ajax_nonce', 'security' );

    global $wpdb;

    if ( isset($_POST['demo_id']) && isset($_POST['new_demo_name']) ) {
        $table_name = $wpdb->prefix . 'wprm_import_export_data';
        $demo_id = intval( $_POST['demo_id'] );
        $new_demo_name = sanitize_text_field( $_POST['new_demo_name'] );

        $result = $wpdb->update(
            $table_name,
            array( 
                'demoname' => $new_demo_name
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

function delete_demo_template_ajax() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( 'Forbidden', 403 );
    }

    check_ajax_referer( 'wprm_ajax_nonce', 'security' );

    global $wpdb;

    if ( isset($_POST['demo_id']) ) {
        $table_name = $wpdb->prefix . 'wprm_import_export_data';
        $demo_id = intval( $_POST['demo_id'] );

        $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $demo_id ) );

        if ( $row ) {
            $wpdb->delete( $table_name, array( 'id' => $demo_id ) );

            // Only delete files from uploads folder if no other template is using them
            $json_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE filename = %s", $row->filename ) );
            if ( intval( $json_count ) === 0 ) {
                $json_path = wprm_get_upload_path( 'demo' ) . '/' . $row->filename;
                if ( file_exists( $json_path ) ) {
                    unlink( $json_path );
                }
            }

            $thumb_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE thumbnail = %s", $row->thumbnail ) );
            if ( intval( $thumb_count ) === 0 ) {
                $thumb_path = wprm_get_upload_path( 'thumbnail' ) . '/' . $row->thumbnail;
                if ( file_exists( $thumb_path ) ) {
                    unlink( $thumb_path );
                }
            }

            wp_send_json_success();
        } else {
            wp_send_json_error('Template not found.');
        }
    } else {
        wp_send_json_error('Invalid request.');
    }

    wp_die();
}
add_action( 'wp_ajax_delete_demo_template', 'delete_demo_template_ajax' );


 

