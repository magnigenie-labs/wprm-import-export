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
    
    public function wpr_menu_demo_add_callback() {
        ?>
        <h2>Upload Demo</h2>	<div class="wprmenu-adddemo-parent-wrap">
        <form method="post" enctype="multipart/form-data">
            <div class="form-input py-2">
                <div class="form-group">
                    <label for="name">Demo Name : </label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required>
                </div> 
                <div class="form-group">
                    <label>Demo Type : </label>
                    <input type="radio" id="free" name="type" value="Free">
                    <label for="free">Free</label>
                    <input type="radio" id="pro" name="type" value="Pro">
                    <label for="pro">Pro</label>
                </div>                                 
                <div class="form-group">
                    <label for="json_img_file">Thumnail Image : </label>
                    <input type="file" id="json_img_file" name="json_img_file" class="form-control" accept=".jpg,.jpeg,.png" title="Upload Thumbnail"/>
                </div>                                
                <div class="form-group">
                    <label for="json_file">Demo File : </label>
                    <input type="file" id="json_file" name="json_file" class="form-control" accept=".json" title="Upload Demo File"/>
                </div>
                <div class="form-group">
                    <input type="submit" class="btnRegister" name="submit" value="Submit">
                </div>
            </div>
        </form> 		</div>
        <?php
        if (isset($_POST['submit'])) :
    
            if ( isset($_FILES['json_file']['name']) && isset($_FILES['json_img_file']['name']) ) :
        
                move_uploaded_file($_FILES['json_img_file']['tmp_name'], __DIR__.'/thumbnail/'. $_FILES["json_img_file"]['name']);
                move_uploaded_file($_FILES['json_file']['tmp_name'], __DIR__.'/demo/'. $_FILES["json_file"]['name']);
                global $wpdb;
                $tablename = $wpdb->prefix.'json_data';
                $wpdb->insert( $tablename, array(
                    'demoname' => $_POST['name'],
                    'filetype' => $_POST['type'],
                    'thumbnail' => $_FILES["json_img_file"]['name'],
                    'filename' => $_FILES["json_file"]['name'],
                ));
            endif;
        endif;
    }
    
    public function wprm_imp_exp_menu_output() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'json_data';
        $results = $wpdb->get_results( "SELECT * FROM $table_name" );
        ?>	<div class="wprmenu-demo-parent-wrap">
        <div class="wprmenu-free_demo-wrap">
        <h2>Free Demos</h2>
        <?php
        foreach($results as $row) :
            $image_link = plugin_dir_url( __FILE__ ).'thumbnail/'.$row->thumbnail;
           
            if( $row->filetype == "Free" ) :
                ?>
                <div class="gallery">
                <a target="_blank" href="<?php echo $image_link; ?>">
                <img src="<?php echo $image_link; ?>" alt="<?php echo $row->demoname; ?>" width="600" height="400">
                </a>
                <!--<div class="desc"><?php //echo $row->demoname; ?></div>-->
                    
                    <!-- Display demo name -->
                    <div class="desc" id="demo-name-<?php echo $row->id; ?>">
                        <span class="demo-name-text"><?php echo $row->demoname; ?></span>
                        <input type="text" class="edit-demo-name-input" data-demo-id="<?php echo $row->id; ?>" value="<?php echo $row->demoname; ?>" style="display:none;">
                    </div>
                    
                    <div class="actions">
                        <button class="edit-demo-button" data-demo-id="<?php echo $row->id; ?>">Edit</button>
                        <button class="save-demo-button" data-demo-id="<?php echo $row->id; ?>" style="display:none;">Save</button>
                    </div>
                    
                     <!--Edit Thumbnail Button -->
                     
                    <div class="actions">
                        <input type="file" id="file-input-<?php echo $row->id; ?>" class="file-input" data-demo-id="<?php echo $row->id; ?>" accept="image/*">
                        <button class="upload-thumbnail-button" data-demo-id="<?php echo $row->id; ?>">Set as New Thumbnail</button>
                         <button class="delete-thumbnail-button" data-demo-id="<?php echo $row->id; ?>">Delete Thumbnail</button>
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
            $image_link = plugin_dir_url( __FILE__ ).'thumbnail/'.$row->thumbnail;
            if( $row->filetype == "Pro" ) :
                ?>
                <div class="gallery">
                <a target="_blank" href="<?php echo $image_link; ?>">
                <img src="<?php echo $image_link; ?>" alt="<?php echo $row->demoname; ?>" width="600" height="400">
                </a>
                <!--<div class="desc"><?php //echo $row->demoname; ?></div>-->
                
                <!-- Display demo name -->
                    <div class="desc" id="demo-name-<?php echo $row->id; ?>">
                        <span class="demo-name-text"><?php echo $row->demoname; ?></span>
                        <input type="text" class="edit-demo-name-input" data-demo-id="<?php echo $row->id; ?>" value="<?php echo $row->demoname; ?>" style="display:none;">
                    </div>
                    
                    <div class="actions">
                        <button class="edit-demo-button" data-demo-id="<?php echo $row->id; ?>">Edit</button>
                        <button class="save-demo-button" data-demo-id="<?php echo $row->id; ?>" style="display:none;">Save</button>
                    </div>
                    
                     <!--Edit Thumbnail Button -->
                     
                    <div class="actions">
                        <input type="file" id="file-input-<?php echo $row->id; ?>" class="file-input" data-demo-id="<?php echo $row->id; ?>" accept="image/*">
                        <button class="upload-thumbnail-button" data-demo-id="<?php echo $row->id; ?>">Set as New Thumbnail</button>
                         <button class="delete-thumbnail-button" data-demo-id="<?php echo $row->id; ?>">Delete Thumbnail</button>
                    </div>
                </div>
                <?php
            endif;
        endforeach;
        ?>
        </div>		</div>
        <?php
    }
}
new WPRM_Admin_Import_Export();