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
?>