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
            'ajax_url' => admin_url('admin-ajax.php')
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
			'methods'  => 'GET',
			'callback' => array( $this, 'wprm_imp_exp_demo_validate' ),
		));
        register_rest_route( 'wprmenu-import/v2', '/type=(?P<post_type>[a-zA-Z0-9_-]+)/demo_name=(?P<post_id>\d+)/settings_id=(?P<settings_id>\d+)', array(
            'methods'  => 'GET',
            'callback' => array( $this, 'wprm_single_demo_import' ),
        )); 
    }

    public function wprm_imp_exp_demo_validate( WP_REST_Request $request ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'json_data';
        $results = $wpdb->get_results( "SELECT * FROM $table_name" );
        $server_data = array();
        foreach( $results as $row ) {
            $item = array(
                        'demo_id' => $row->id,
                        'settings' => $row->id,
                        'demoname' => $row->demoname,
                        'demo_type' => $row->filetype,
                        'thumbnail' => $row->thumbnail,
                        'filename' => $row->filename,
                        'demo_url' => plugin_dir_url( __DIR__ ). 'admin/demo/' . $row->filename,
                        'image_path' => plugin_dir_url( __DIR__ ). 'admin/thumbnail/' . $row->thumbnail
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
        $post_type = $request->get_param('post_type');
        $post_id = $request->get_param('post_id');
        $settings_id = $request->get_param('settings_id');
        $table_name = $wpdb->prefix . 'json_data';
        $results = $wpdb->get_results( "SELECT * FROM $table_name" );
        $response = array();
        
        foreach( $results as $row ) {
            // $item = array(
            //     'demo_id' => $row->id,
            //     'settings' => $row->id,
            //     'demoname' => $row->demoname,
            //     'demo_type' => $row->filetype,
            //     'thumbnail' => $row->thumbnail,
            //     'filename' => $row->filename,
            //     'demo_url' => plugin_dir_url( __DIR__ ). 'admin/demo/' . $row->filename,
            //     'image_path' => plugin_dir_url( __DIR__ ). 'admin/thumbnail/' . $row->thumbnail
            // );
            
            if( $post_type == $row->filetype && $post_id == $row->id && $settings_id == $row->id ) {
                $json_url = plugin_dir_url( __DIR__ ). 'admin/demo/' . $row->filename;
                
                $response = wp_safe_remote_get($json_url); // Use wp_safe_remote_get() to retrieve remote data

                if (is_wp_error($response)) {
                    return new WP_Error('json_error', 'Failed to fetch JSON data.', array('status' => 500));
                }
            
                $json_data = $response;
            }
        }
        return $json_data;
    }
}
new WPRM_Import_Export();