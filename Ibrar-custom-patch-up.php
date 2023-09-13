<?php
/*
 * Plugin Name:       Ibrar's Custom Patch UPs
 * Description:       This plugin is used to exclude specific category posts from home page.
 * Author:            Ibrar Ayub
 * Version:           1.0
*/

require 'plugin-update-checker-master/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/manager-wiseTech/Ibrar-custom-patch-up.git',
	__FILE__,
	'Ibrar-custom-patch-up'
);

//Set the branch that contains the stable release.
$myUpdateChecker->setBranch('main');

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('your-token-here');


// This is security check
if(! defined ('ABSPATH')){
    die;
}

// Add the settings page 
function custom_patch_ups_select_the_category_settings_page() {
    add_options_page('Custom Patch UPs Settings', 'Custom Patch UPs', 'manage_options', 'custom-patch-ups-settings', 'custom_patch_ups_exclude_posts_settings_form');
}
add_action('admin_menu', 'custom_patch_ups_select_the_category_settings_page');

// Settings for to select the category
function custom_patch_ups_exclude_posts_settings_form() {
    ?>
    <div class="wrap">
        <h2>Custom Patch UPs Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('custom_patch_ups_settings'); ?>
            <?php do_settings_sections('custom-patch-ups-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select Category to Exclude Posts from Home Page</th>
                    <td>
                        <?php
                        $selected_category = get_option('custom_patch_ups_category');
                        wp_dropdown_categories(array(
                            'show_option_none' => 'Select a category',
                            'name' => 'custom_patch_ups_category',
                            'orderby' => 'name',
                            'selected' => $selected_category,
                        ));
                        ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function custom_patch_ups_exclude_posts_register_settings() {
    register_setting('custom_patch_ups_settings', 'custom_patch_ups_category');
}
add_action('admin_init', 'custom_patch_ups_exclude_posts_register_settings');


// Code to exclude the specific category posts from the home page
function custom_patch_ups_exclude_posts_by_category_from_homepage($query) {
    $theme = wp_get_theme();
    if ('Astra' == $theme->name || 'Astra' == $theme->parent_theme) {
        if ($query->is_home() && $query->is_main_query()) {
            $selected_category = get_option('custom_patch_ups_category');
            if ($selected_category) {
                $query->set('category__not_in', array($selected_category));
            }
        }
    }
}
add_action('pre_get_posts', 'custom_patch_ups_exclude_posts_by_category_from_homepage');
