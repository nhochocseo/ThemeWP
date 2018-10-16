<?php
ini_set('memory_limit', '-1');
require get_template_directory() . '/core/init.php';
/********************************************************************
//remove tag width & height in post
********************************************************************/
add_filter( 'post_thumbnail_html', 'remove_width_attribute', 10 );
add_filter( 'image_send_to_editor', 'remove_width_attribute', 10 );
function remove_width_attribute( $html ) {
   $html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
   return $html;
}
/********************************************************************
//Ản link post type ko cần thiết
********************************************************************/
add_action('admin_head', 'wpds_custom_admin_post_css');
function wpds_custom_admin_post_css() {
    global $post_type;
    if ($post_type == 'slider') {
        echo "<style>#edit-slug-box {display:none;}</style>";
    }
}
/********************************************************************
//config excerpt length
********************************************************************/
function custom_excerpt_length( $length ) {
	return 300;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
/********************************************************************
//Allow HTML tags in Widget title
********************************************************************/
function html_widget_title( $var) {
	$var = (str_replace( '[', '<', $var ));
	$var = (str_replace( ']', '>', $var ));
	$var = (str_replace( '__', '"', $var ));
	return $var ;
}
add_filter( 'widget_title', 'html_widget_title' );
/********************************************************************
//register short code url
********************************************************************/
add_filter('widget_text', 'do_shortcode');
// [url_base]
function url_base_function() {
	return get_bloginfo( "url" );
}
add_shortcode('url_base', 'url_base_function');


/********************************************************************
//register my menu in theme
********************************************************************/
add_action( 'init', 'register_my_menus' );
function register_my_menus(){
	register_nav_menus(
	array(
		'main_nav' => 'Main Nav',
		'cat_nav' => 'Danh mục',
		)
	);
}
/********************************************************************
//REGISTER SIDEBAR
********************************************************************/
if (function_exists('register_sidebar')){
	register_sidebar(array(
	'name'=> 'Cột bên',
	'id' => 'sidebar',
	));
}

/********************************************************************
global teaser function
********************************************************************/
function teaser($limit) {
	$excerpt = explode(' ', get_the_excerpt(), $limit);
	if (count($excerpt)>=$limit) {
		array_pop($excerpt);
		$excerpt = implode(" ",$excerpt).'[...]';
	} else {
		$excerpt = implode(" ",$excerpt);
	}
	$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
	return $excerpt.'...';
}
/********************************************************************
Post Views
********************************************************************/
function setpostview($postID){
    $count_key ='views';
    $count = get_post_meta($postID, $count_key, true);
    if($count == ''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    } else {
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}
function getpostviews($postID){
    $count_key ='views';
    $count = get_post_meta($postID, $count_key, true);
    if($count == ''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return $count;
}
/********************************************************************
get link img post
********************************************************************/
function get_link_img_post(){
	global $post;
	preg_match_all('/src="(.*)"/Us',get_the_content(),$matches);
	$link_img_post = $matches[1];
	return $link_img_post;
}

/********************************************************************
check link thumbnail
********************************************************************/
function check_link_thumb($post_id) {
	$img_customfield = get_post_meta($post_id, 'thumb', true);
	$img_attachment_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
	// get thumbnail
	if ($img_customfield) {
		$link_thumb = $img_customfield;
	} elseif ($img_attachment_image) {
		$link_thumb = $img_attachment_image[0];
	} else {
		$link_thumb = "";
	}
	return $link_thumb;
}
/********************************************************************
get link thumbnail
********************************************************************/
function get_link_thumb($post_id) {
	$img_customfield = get_post_meta($post_id, 'thumb', true);
	$img_attachment_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
	$img_uploads = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID', 'numberposts' => 1) );
	
	$post_content = get_post_field('post_content', $post_id);
	$img_post = preg_match_all('/\< *[img][^\>]*src *= *[\"\']{0,1}([^\"\'\ >]*)/',$post_content,$matches);
	$img_default = get_bloginfo('template_directory').'/images/no-thumb.png';
	
	// get thumbnail
	if ($img_customfield) {
		$link_thumb = $img_customfield;
	} elseif ($img_attachment_image) {
		$link_thumb = $img_attachment_image[0];
	} elseif ($img_uploads == true) {
		foreach($img_uploads as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			$link_thumb = $img[0];
		}
	} elseif (count($matches[1]) > 0) {
		$link_thumb = $matches[1][0];
	} else {
		$link_thumb = $img_default;
	}
	return $link_thumb;
}
/********************************************************************
creating thumbnails (no permalink to story, image only)
********************************************************************/
function show_thumb($w,$h,$zc,$cropfrom) {
	global $post;
	$img_customfield = get_post_meta($post->ID, 'thumb', true);
	$img_attachment_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' );
	$img_uploads = get_children( array('post_parent' => $post->ID, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID', 'numberposts' => 1) );
	$img_post = preg_match_all('/\< *[img][^\>]*src *= *[\"\']{0,1}([^\"\'\ >]*)/',get_the_content(),$matches);
	$img_default = get_bloginfo('template_directory').'/images/no-thumb.png';

	$thumbnail = 'thumbnail.php';
	
	// get thumbnail
	if ($img_customfield) {
		print '<img src="'.get_bloginfo('template_directory').'/'.$thumbnail.'?src='.urlencode($img_customfield).'&amp;w='.$w.'&amp;h='.$h.'&amp;zc='.$zc.'&amp;a='.$cropfrom.'" alt="'.get_the_title($post).'" title="'.get_the_title($post).'"/>';
	} elseif ($img_attachment_image) {
		print '<img src="'.get_bloginfo('template_directory').'/'.$thumbnail.'?src='.urlencode($img_attachment_image[0]).'&amp;w='.$w.'&amp;h='.$h.'&amp;zc='.$zc.'&amp;a='.$cropfrom.'" alt="'.get_the_title($post).'" title="'.get_the_title($post).'"/>';
	} elseif ($img_uploads == true) {
		foreach($img_uploads as $id => $attachment) {
			$img = wp_get_attachment_image_src($id, 'full');
			print '<img src="'.get_bloginfo('template_directory').'/'.$thumbnail.'?src='.urlencode($img[0]).'&amp;w='.$w.'&amp;h='.$h.'&amp;zc='.$zc.'&amp;a='.$cropfrom.'" alt="'.get_the_title($post).'" title="'.get_the_title($post).'" />';
		}
	} elseif (count($matches[1]) > 0) {
		print '<img src="'.get_bloginfo('template_directory').'/'.$thumbnail.'?src='.urlencode($matches[1][0]).'&amp;w='.$w.'&amp;h='.$h.'&amp;zc='.$zc.'&amp;a='.$cropfrom.'" alt="'.get_the_title($post).'" title="'.get_the_title($post).'" />';
	} else {
		print '<img src="'.get_bloginfo('template_directory').'/'.$thumbnail.'?src='.urlencode($img_default).'&amp;w='.$w.'&amp;h='.$h.'&amp;zc='.$zc.'&amp;a='.$cropfrom.'" alt="'.get_the_title($post).'" title="'.get_the_title($post).'" />';
	}
}
/********************************************************************
custom post_type slider
********************************************************************/
add_theme_support( 'post-thumbnails' );
function change_woocommerce_menu_title( $translated )
{
    $translated = str_replace( 'Contact', 'Liên hệ', $translated );
    $translated = str_replace( 'Tóm tắt', 'Nội dung mô tả', $translated );
    return $translated;
}
add_filter( 'gettext', 'change_woocommerce_menu_title' );

add_action('wp_ajax_Load_action', 'Load_action');
add_action('wp_ajax_nopriv_Load_action', 'Load_action');
function Load_action() {
    $count = isset($_POST['id']) ? (int)$_POST['id'] : false; ?>
    <?php $getposts = new WP_query(); $getposts->query('post_status=publish&p='.$count.'&post_type=du-an'); ?>
	<?php global $wp_query; $wp_query->in_the_loop = true; ?>
	<?php while ($getposts->have_posts()) : $getposts->the_post(); ?>
	<?php $categories = get_the_terms( get_the_id(), 'danh-muc' ); ?>
		<div id="project">
			<section class="content">
				<div class="block-content">			
					<div class="project-title">
						<h2><?php the_title(); ?></h2>
						<p class="tags"><?php foreach ($categories as $key => $value) { if($key==0) { echo $value->name; } else { echo ' / '.$value->name; } } ?></p>
					</div>
					<article class="post-content">
						<?php the_content(); ?>
					</article>
				</div>
			</section>
		</div>
	<?php endwhile; wp_reset_postdata(); ?>
<?php  die(); }

add_action( 'after_setup_theme', 'wpdocs_theme_setup' );
function wpdocs_theme_setup() {
    add_image_size( 'post-thumb', 300,300, array( 'center', 'center' ) ); 
    add_image_size( 'slider-thumb', 750,340, array( 'center', 'center' ) ); 
}

function Generate_Featured_Image2( $image_url, $post_id  ){
    $upload_dir = wp_upload_dir();
    $image_data = file_get_contents($image_url);
    $filename = basename($image_url);
    if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
    else                                    $file = $upload_dir['basedir'] . '/' . $filename;
    file_put_contents($file, $image_data);

    $wp_filetype = wp_check_filetype($filename, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
    $res2= set_post_thumbnail( $post_id, $attach_id );
}
class Auto_Save_Images{
    function __construct(){    
        
        add_filter( 'content_save_pre',array($this,'post_save_images') );
    }
    
    function post_save_images( $content ){
        if( ($_POST['save'] || $_POST['publish'] )){
            set_time_limit(240);
            global $post;
            $post_id=$post->ID;
            $preg=preg_match_all('/<img.*?src="(.*?)"/',stripslashes($content),$matches);
            if($preg){
                foreach($matches[1] as $image_url){
                    if(empty($image_url)) continue;
                    $pos=strpos($image_url,$_SERVER['HTTP_HOST']);
                    if($pos===false){
                        $res=$this->save_images($image_url,$post_id);
                        $replace=$res['url'];
                        $content=str_replace($image_url,$replace,$content);
                    }
                }
            }
        }
        remove_filter( 'content_save_pre', array( $this, 'post_save_images' ) );
        return $content;
    }
    
    function save_images($image_url,$post_id){
        $file=file_get_contents($image_url);
        $post = get_post($post_id);
        $posttitle = $post->post_title;
        $postname = sanitize_title($posttitle);
        $im_name = "$postname-$post_id.jpg";
        $res=wp_upload_bits($im_name,'',$file);
        $this->insert_attachment($res['file'],$post_id);
        return $res;
    }
    
    function insert_attachment($file,$id){
        $dirs=wp_upload_dir();
        $filetype=wp_check_filetype($file);
        $attachment=array(
            'guid'=>$dirs['baseurl'].'/'._wp_relative_upload_path($file),
            'post_mime_type'=>$filetype['type'],
            'post_title'=>preg_replace('/\.[^.]+$/','',basename($file)),
            'post_content'=>'',
            'post_status'=>'inherit'
        );
        $attach_id=wp_insert_attachment($attachment,$file,$id);
        $attach_data=wp_generate_attachment_metadata($attach_id,$file);
        wp_update_attachment_metadata($attach_id,$attach_data);
        return $attach_id;
    }
}
new Auto_Save_Images();