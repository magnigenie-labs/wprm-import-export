<?php

class WPRM_Admin_Import_Export {
  
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'wpr_menu_plugin_top_menu' ) );
    }

    public function wpr_menu_plugin_top_menu(){
        add_menu_page(
            __( 'WPR Menu Demo', 'textdomain' ), 
            __( 'WPR Menu Demo', 'textdomain' ), 
            'manage_options', 
            'wpr-menu-demo', 
            array( $this, 'wprm_imp_exp_menu_output' ),
            plugin_dir_url( __FILE__ ).'/img/icon.png' 
        );
        add_submenu_page(
            'wpr-menu-demo', 
            'Add New', 
            'Add New Demo File', 
            'manage_options', 
            'wpr-menu-add-demo', 
            array( $this, 'wpr_menu_demo_add_callback' )
        );
    }
    
    public function filter_upload_dir_demo( $dirs ) {
        $dirs['subdir'] = '/wprm-templates/demo';
        $dirs['path']   = $dirs['basedir'] . '/wprm-templates/demo';
        $dirs['url']    = $dirs['baseurl'] . '/wprm-templates/demo';
        return $dirs;
    }

    public function filter_upload_dir_thumb( $dirs ) {
        $dirs['subdir'] = '/wprm-templates/thumbnail';
        $dirs['path']   = $dirs['basedir'] . '/wprm-templates/thumbnail';
        $dirs['url']    = $dirs['baseurl'] . '/wprm-templates/thumbnail';
        return $dirs;
    }

    public function wpr_menu_demo_add_callback() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'textdomain' ) );
        }

        if ( isset( $_POST['submit'] ) ) {
            if ( ! isset( $_POST['wprm_demo_nonce'] ) || ! wp_verify_nonce( $_POST['wprm_demo_nonce'], 'wprm_save_demo_action' ) ) {
                wp_die( esc_html__( 'Security check failed.', 'textdomain' ) );
            }

            if ( ! empty( $_FILES['json_file']['name'] ) && ! empty( $_FILES['json_img_file']['name'] ) ) {
                $upload_overrides = array( 'test_form' => false );

                // Securely handle JSON file upload
                add_filter( 'upload_dir', array( $this, 'filter_upload_dir_demo' ) );
                $json_upload = wp_handle_upload( $_FILES['json_file'], $upload_overrides );
                remove_filter( 'upload_dir', array( $this, 'filter_upload_dir_demo' ) );

                // Securely handle thumbnail file upload
                add_filter( 'upload_dir', array( $this, 'filter_upload_dir_thumb' ) );
                $thumb_upload = wp_handle_upload( $_FILES['json_img_file'], $upload_overrides );
                remove_filter( 'upload_dir', array( $this, 'filter_upload_dir_thumb' ) );

                if ( ! isset( $json_upload['error'] ) && ! isset( $thumb_upload['error'] ) ) {
                    $json_filename = basename( $json_upload['file'] );
                    $thumb_filename = basename( $thumb_upload['file'] );

                    global $wpdb;
                    $tablename = $wpdb->prefix . 'wprm_import_export_data';
                    $wpdb->insert( $tablename, array(
                        'demoname'  => sanitize_text_field( $_POST['name'] ),
                        'filetype'  => sanitize_text_field( $_POST['type'] ),
                        'thumbnail' => $thumb_filename,
                        'filename'  => $json_filename,
                    ));
                    echo '<div class="notice notice-success is-dismissible"><p>Demo uploaded successfully!</p></div>';
                } else {
                    $error_msg = isset( $json_upload['error'] ) ? $json_upload['error'] : $thumb_upload['error'];
                    echo '<div class="notice notice-error is-dismissible"><p>Upload failed: ' . esc_html( $error_msg ) . '</p></div>';
                }
            }
        }
        ?>
        <h2>Upload Demo</h2>
        <div class="wprmenu-adddemo-parent-wrap">
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field( 'wprm_save_demo_action', 'wprm_demo_nonce' ); ?>
                <div class="form-input py-2">
                    <div class="form-group">
                        <label for="name">Demo Name : </label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required>
                    </div> 
                    <div class="form-group">
                        <label>Demo Type : </label>
                        <input type="radio" id="free" name="type" value="Free" checked>
                        <label for="free">Free</label>
                        <input type="radio" id="pro" name="type" value="Pro">
                        <label for="pro">Pro</label>
                    </div>                                 
                    <div class="form-group">
                        <label for="json_img_file">Thumbnail Image : </label>
                        <input type="file" id="json_img_file" name="json_img_file" class="form-control" accept=".jpg,.jpeg,.png" title="Upload Thumbnail" required />
                    </div>                                
                    <div class="form-group">
                        <label for="json_file">Demo File : </label>
                        <input type="file" id="json_file" name="json_file" class="form-control" accept=".json" title="Upload Demo File" required />
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btnRegister" name="submit" value="Submit">
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
    
    public function wprm_imp_exp_menu_output() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'textdomain' ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'wprm_import_export_data';
        $results = $wpdb->get_results( "SELECT * FROM $table_name" );
        ?>
        <div class="wprmenu-demo-parent-wrap">
            <div class="wprmenu-free_demo-wrap">
                <h2>Free Demos</h2>
                <?php
                foreach($results as $row) :
                    if( $row->filetype == "Free" ) :
                        $image_link = wprm_get_upload_url( 'thumbnail' ) . '/' . $row->thumbnail;
                        ?>
                        <div class="gallery">
                            <a target="_blank" href="<?php echo esc_url( $image_link ); ?>">
                                <img src="<?php echo esc_url( $image_link ); ?>" alt="<?php echo esc_attr( $row->demoname ); ?>" width="600" height="400">
                            </a>
                            <div class="desc" id="demo-name-<?php echo esc_attr( $row->id ); ?>">
                                <span class="demo-name-text"><?php echo esc_html( $row->demoname ); ?></span>
                                <input type="text" class="edit-demo-name-input" data-demo-id="<?php echo esc_attr( $row->id ); ?>" value="<?php echo esc_attr( $row->demoname ); ?>" style="display:none;">
                            </div>
                            
                            <div class="actions">
                                <button class="edit-demo-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>">Edit</button>
                                <button class="save-demo-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>" style="display:none;">Save</button>
                                <a href="<?php echo esc_url( wprm_get_upload_url( 'demo' ) . '/' . $row->filename ); ?>" class="button download-demo-button" download="<?php echo esc_attr( $row->filename ); ?>">Download</a>
                                <button class="button delete-demo-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>">Delete</button>
                            </div>
                             
                            <div class="actions">
                                <input type="file" id="file-input-<?php echo esc_attr( $row->id ); ?>" class="file-input" data-demo-id="<?php echo esc_attr( $row->id ); ?>" accept="image/*">
                                <button class="upload-thumbnail-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>">Set as New Thumbnail</button>
                                <button class="delete-thumbnail-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>">Delete Thumbnail</button>
                            </div>
                        </div>
                        <?php
                    endif;
                endforeach;
                ?>
            </div>

            <div class="wprmenu-pro_demo-wrap">
                <h2>Pro Demos</h2>
                <?php
                foreach($results as $row) :
                    if( $row->filetype == "Pro" ) :
                        $image_link = wprm_get_upload_url( 'thumbnail' ) . '/' . $row->thumbnail;
                        ?>
                        <div class="gallery">
                            <a target="_blank" href="<?php echo esc_url( $image_link ); ?>">
                                <img src="<?php echo esc_url( $image_link ); ?>" alt="<?php echo esc_attr( $row->demoname ); ?>" width="600" height="400">
                            </a>
                            <div class="desc" id="demo-name-<?php echo esc_attr( $row->id ); ?>">
                                <span class="demo-name-text"><?php echo esc_html( $row->demoname ); ?></span>
                                <input type="text" class="edit-demo-name-input" data-demo-id="<?php echo esc_attr( $row->id ); ?>" value="<?php echo esc_attr( $row->demoname ); ?>" style="display:none;">
                            </div>
                            
                            <div class="actions">
                                <button class="edit-demo-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>">Edit</button>
                                <button class="save-demo-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>" style="display:none;">Save</button>
                                <a href="<?php echo esc_url( wprm_get_upload_url( 'demo' ) . '/' . $row->filename ); ?>" class="button download-demo-button" download="<?php echo esc_attr( $row->filename ); ?>">Download</a>
                                <button class="button delete-demo-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>">Delete</button>
                            </div>
                             
                            <div class="actions">
                                <input type="file" id="file-input-<?php echo esc_attr( $row->id ); ?>" class="file-input" data-demo-id="<?php echo esc_attr( $row->id ); ?>" accept="image/*">
                                <button class="upload-thumbnail-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>">Set as New Thumbnail</button>
                                <button class="delete-thumbnail-button" data-demo-id="<?php echo esc_attr( $row->id ); ?>">Delete Thumbnail</button>
                            </div>
                        </div>
                        <?php
                    endif;
                endforeach;
                ?>
            </div>
        </div>
        <?php
    }
}
new WPRM_Admin_Import_Export();