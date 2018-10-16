<?php

function theme_settings_page(){
?>
	<div class="option_header">
	    <div class="title-header">Cài đặt trang</div>
	    <form method="post" action="options.php" class="form_option">
	    	<div>

		  <!-- Nav tabs -->
		  <ul class="nav nav-tabs menu-setting" role="tablist">
		    <li role="presentation"><a class="active" href="#home" aria-controls="home" role="tab" data-toggle="tab">Trang chủ</a></li>
		    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
		    <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Messages</a></li>
		    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
		  </ul>

		  <!-- Tab panes -->
		  <div class="tab-content">
		    <div role="tabpanel" class="tab-pane active" id="home">
				<?php
		           settings_fields("section");
		           do_settings_sections("theme-options");      
		           submit_button(); 
		        ?>  
		    </div>
		    <div role="tabpanel" class="tab-pane" id="profile">..s.</div>
		    <div role="tabpanel" class="tab-pane" id="messages">..ád.</div>
		    <div role="tabpanel" class="tab-pane" id="settings">..d.</div>
		  </div>
		</div>
	                
	    </form>
	</div>
<?php
}
function add_theme_menu_item()
{
    add_menu_page("Cài đặt trang", "Cài đặt trang", "manage_options", "theme-panel", "theme_settings_page", null, 99);
}
add_action("admin_menu", "add_theme_menu_item");

function display_twitter_element()
{
?>
    <input type="text" name="twitter_url" class="input_text" id="twitter_url" value="<?php echo get_option('twitter_url'); ?>" />
<?php
}

function display_facebook_element()
{
?>
    <input type="text" name="facebook_url" class="input_text" id="facebook_url" value="<?php echo get_option('facebook_url'); ?>" />
<?php
}

function hien_quang_cao()
{
	?>
		<input type="checkbox" name="theme_layout" value="1" <?php checked(1, get_option('theme_layout'), true); ?> /> 
	<?php
}

function display_theme_panel_fields()
{
	add_settings_section("section", null, null, "theme-options");
	
	add_settings_field("twitter_url", "Twitter Profile Url", "display_twitter_element", "theme-options", "section");
    add_settings_field("facebook_url", "Facebook Profile Url", "display_facebook_element", "theme-options", "section");
    add_settings_field("theme_layout", "Hiện quảng cáo ?", "hien_quang_cao", "theme-options", "section");

    register_setting("section", "twitter_url");
    register_setting("section", "facebook_url");
    register_setting("section", "theme_layout");
}

add_action("admin_init", "display_theme_panel_fields");
