<?php

/**
 * Plugin Name: Twitterpated
 * Author: Ryan Nielson
 * Author URI: http://norex.ca/
 * Version: 1.0.0
 * Description: Adds the ability to render twitter feeds using oAuth.
 */

require_once('wptwitterhandler.php');

class Twitterpated {
   
    public function settings_init() {
        // Admin settings
        add_settings_section('twitterpated_admin_settings_section', 'Twitterpated Admin Options', 'twitterpad_admin_settings_callback', 'twitterpated_settings');
        add_settings_field('twitterpated_consumer_key', 'Consumer Key', 'twitterpated_textfield_callback', 'twitterpated_settings', 'twitterpated_admin_settings_section', array('twitterpated_consumer_key'));
        add_settings_field('twitterpated_consumer_secret', 'Consumer Secret', 'twitterpated_textfield_callback', 'twitterpated_settings', 'twitterpated_admin_settings_section', array('twitterpated_consumer_secret'));

        register_setting('twitterpated_admin_settings_section', 'twitterpated_consumer_key');
        register_setting('twitterpated_admin_settings_section', 'twitterpated_consumer_secret');

        // User settings.
        add_settings_section('twitterpated_admin_settings_section', 'Twitterpated Admin Options', 'twitterpad_admin_settings_callback', 'twitterpated_settings');
       
    }

    public function settings_display() {
        ?>

        <div class="wrap">
            <h2>Twitterpated Settings</h2>

            <form method="post" action="options.php"> 
                <?php settings_fields('twitterpated_admin_settings_section'); ?>
                <?php do_settings_sections( 'twitterpated_settings' ); ?>
                <?php submit_button(); ?>
            </form>

            <?php
            if (get_option('twitterpated_consumer_key') && get_option('twitterpated_consumer_secret')) {
                $handler = new WPTwitterHandler();
                $handler->authorize();
            }
            ?>

        </div>


        <?php
    }
}

add_action('admin_menu', 'twitterpated_settings');
function twitterpated_settings() {
    $twitterpated = new Twitterpated();
   // add_submenu_page('options-general.php', 'Twitterpated Settings', 'Twitterpated Settings', 'manage_options', 'twitterpated_settings', array(&$twitterpated, 'settings_display'));

     add_menu_page('Twitterpated Settings', 'Twitterpated Settings', 'manage_options', 'twitterpated_settings', array(&$twitterpated, 'settings_display'));
}

add_action('admin_init', 'twitterpated_init_settings');
function twitterpated_init_settings() {
    $twitterpated = new Twitterpated();
    $twitterpated->settings_init();
}

function twitterpad_admin_settings_callback() {  
    echo '<p>Select which areas of content you wish to display.</p>';  
}

function twitterpated_textfield_callback($args) {
    // Note the ID and the name attribute of the element should match that of the ID in the call to add_settings_field  
    $html = '<input type="text" id="' . $args[0] .'"  name="' . $args[0] .'" value="' . get_option($args[0]) . '" />';  
    // Here, we will take the first argument of the array and add it to a label next to the checkbox  
    $html .= '<label for="' . $args[0] .'"> '  . $args[1] . '</label>';  
    echo $html;  
}

function twitterpated_get_timeline() {
    $handler = new WPTwitterHandler();
    return $handler->user_timeline('twitter_user_timeline', array('count' => 3));
}


add_action('admin_enqueue_scripts', 'twitterpated_add_admin_scripts');
function twitterpated_add_admin_scripts() {
    wp_enqueue_script('oauth_popup_script',  plugins_url('js/jquery.oauthpopup.js' , __FILE__));
    wp_enqueue_script('twitterpated_admin_script',  plugins_url('js/twitterpated.admin.js' , __FILE__));
}

add_action('wp_enqueue_scripts', 'twitterpated_add_javascripts');
function twitterpated_add_javascripts() {
    wp_enqueue_script('twitter_widget_script', 'http://platform.twitter.com/widgets.js'); // Already enqueued on EVERY page by NextGen :S 
}

?>