<?php 
	global $hk_options; 
	add_action('wp_dashboard_setup', 'remove_redux_dashboard');
	function remove_redux_dashboard() {
        remove_meta_box('redux_dashboard_widget', 'dashboard', 'side', 'high');
        remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'side', 'high');
	}
	add_action('admin_init', 'rw_remove_dashboard_widgets');
    function rw_remove_dashboard_widgets() {
        remove_meta_box('dashboard_right_now', 'dashboard', 'normal'); // right now
        remove_meta_box('dashboard_activity', 'dashboard', 'normal');// WP 3.8
        remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // recent comments
        remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal'); // incoming links
        remove_meta_box('dashboard_plugins', 'dashboard', 'normal'); // plugins
        remove_meta_box('dashboard_quick_press', 'dashboard', 'normal'); // quick press
        remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal'); // recent drafts
        remove_meta_box('dashboard_primary', 'dashboard', 'normal'); // wordpress blog
        remove_meta_box('dashboard_secondary', 'dashboard', 'normal'); // other wordpress news
	}

	add_action( 'admin_enqueue_scripts', 'load_custom_wp_admin_style' );
	function load_custom_wp_admin_style(){
		wp_enqueue_style( 'admin_css', get_bloginfo('template_directory'). '/core/admin/style-admin.css' );
		wp_enqueue_style( 'option_css', get_bloginfo('template_directory'). '/core/admin/css/option.css' );
	}

	add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
    function my_custom_dashboard_widgets() {
        global $wp_meta_boxes;
        wp_add_dashboard_widget('custom_help_widget', 'Giới thiệu', 'custom_dashboard_help');
    }
     function custom_dashboard_help() { ?>
	    <h2>Chào mừng đến với Hệ thống Quản Trị Website</h2>
	    <p><strong>THÔNG TIN WEBSITE</strong></p>
	    <P><?php echo bloginfo( 'name' ); ?></p>
	    <p><strong>NHÀ PHÁT TRIỂN</strong></p>
	    <p>Hệ thống được phát triển bởi <a href="http://fb.com/nhochocseo"><strong>Cao Văn Dũng</strong></a></p> 
	<?php }

    add_filter('admin_title', 'my_admin_title', 10, 2);
	function my_admin_title($admin_title, $title){
	    return $title.' &lsaquo; '.get_bloginfo('name').' &lsaquo; '.'';
	}

	/** Footer **/
	function remove_footer_admin () {
	    echo '';
	}
	add_filter('admin_footer_text', 'remove_footer_admin');

	function login_css() {
    wp_enqueue_style( 'login_css', get_bloginfo('template_directory'). '/core/admin/login.css' );
	}
	add_action('login_head', 'login_css');
?>