<?php

class WPRM_Import_Export {

    public function __construct() {
        $this->init_hooks();
	}  

    public function enqueue_style() {
        wp_enqueue_style( 'wprm-import-export', plugin_dir_url( __DIR__ ) . 'admin/assets/css/style.css', array(), '1.0.0' );
    }
	
    public function enqueue_script() {

        wp_enqueue_script( 'wprm-import-export-thumbnail', plugin_dir_url( __DIR__ ) . 'admin/assets/js/edit_thumbnail.js', array(), '1.0.0' );
        $data_array = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('wprm_ajax_nonce')
        );
        
        wp_localize_script('wprm-import-export-thumbnail', 'thumbnaildata', $data_array);
    }
    public function cc_mime_types($mimes) {
        $mimes['json'] = 'application/json';
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    public function init_hooks(){ 
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_style' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_script' ) );
        add_filter( 'upload_mimes', array( $this, 'cc_mime_types') );
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }
    
    public function register_routes() {
		register_rest_route( 'wprmenu-server', 'v1', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'wprm_imp_exp_demo_validate' ),
			'permission_callback' => '__return_true',
		));
        register_rest_route( 'wprmenu-import/v2', '/type=(?P<post_type>[a-zA-Z0-9_-]+)/demo_name=(?P<post_id>\d+)/settings_id=(?P<settings_id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'wprm_single_demo_import' ),
			'permission_callback' => '__return_true',
        )); 
    }

    public function wprm_imp_exp_demo_validate( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wprm_import_export_data';
        $results = $wpdb->get_results( "SELECT * FROM $table_name" );
        $free_items = array();
        $pro_items = array();
        foreach( $results as $row ) {
            $item = array(
                        'demo_id' => $row->id,
                        'settings' => $row->id,
                        'demoname' => $row->demoname,
                        'demo_type' => $row->filetype,
                        'thumbnail' => $row->thumbnail,
                        'filename' => $row->filename,
                        'demo_url' => wprm_get_upload_url( 'demo' ) . '/' . $row->filename,
                        'image_path' => wprm_get_upload_url( 'thumbnail' ) . '/' . $row->thumbnail
                    );
            if($row->filetype == 'Free') {
                $free_items[] = $item;
            } else if($row->filetype == 'Pro') {
                $pro_items[] = $item;
            }
        }

        $response_array = array(
            'message' => 'Successful',
            'Free' => $free_items,
            'Pro' => $pro_items
        );

        $response = new WP_REST_Response( $response_array );
        $response->set_status(200);
        return $response;
    }

    public function wprm_single_demo_import( WP_REST_Request $request ) {
        global $wpdb;
        $post_type = sanitize_text_field( $request->get_param('post_type') );
        $post_id = intval( $request->get_param('post_id') );
        $settings_id = intval( $request->get_param('settings_id') );
        $table_name = $wpdb->prefix . 'wprm_import_export_data';
        
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table_name WHERE filetype = %s AND id = %d",
            $post_type,
            $post_id
        ) );
        
        if ( ! $row || $settings_id !== intval( $row->id ) ) {
            return new WP_Error('not_found', 'Demo template not found.', array('status' => 404));
        }

        $json_path = wprm_get_upload_path( 'demo' ) . '/' . $row->filename;

        if ( ! file_exists( $json_path ) ) {
            return new WP_Error('json_error', 'Template file does not exist.', array('status' => 500));
        }

        $json_content = file_get_contents( $json_path );
        $json_data = array(
            'body'     => $json_content,
            'response' => array( 'code' => 200, 'message' => 'OK' )
        );

        return $json_data;
    }
}
new WPRM_Import_Export();