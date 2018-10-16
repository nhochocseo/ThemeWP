<?php
function CaoDung_plugin_activation() {
        $plugins = array(
                array(
                        'name' => 'Contact Form 7',
                        'slug' => 'contact-form-7',
                        'required' => true
                )
        );
        $configs = array(
                'menu' => 'hk_plugin_install',
                'has_notice' => true,
                'dismissable' => false,
                'is_automatic' => true
        );
        tgmpa( $plugins, $configs );
}
add_action('tgmpa_register', 'CaoDung_plugin_activation');
function include_add_upload_image() {
	/*
	 * I recommend to add additional conditions just to not to load the scipts on each page
	 * like:
	 * if ( !in_array('post-new.php','post.php') ) return;
	 */
	if ( ! did_action( 'wp_enqueue_media' ) ) {
		wp_enqueue_media();
	}
 
 	wp_enqueue_script( 'include_add_upload_image', get_stylesheet_directory_uri() . '/core/admin/js/add_image.js', array('jquery'), null, false );
}
 
add_action( 'admin_enqueue_scripts', 'include_add_upload_image' );


?>