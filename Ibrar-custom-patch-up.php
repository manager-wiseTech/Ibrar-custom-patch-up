<?php
/*
 * Plugin Name:       Ibrar Custom Patch Up
 * Description:       This plugin is used to exclude specific category posts from home page.
 * Author:            Ibrar Ayub
 * Version:           1.0.0
*/

require 'plugin-update-checker-master/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://github.com/manager-wiseTech/Ibrar-custom-patch-up/',
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
function custom_patch_ups_select_the_category_settings_page()
{
    add_options_page('Custom Patch UPs Settings', 'Custom Patch UPs', 'manage_options', 'custom-patch-ups-settings', 'custom_patch_ups_exclude_posts_settings_form');
}
add_action('admin_menu', 'custom_patch_ups_select_the_category_settings_page');

// Settings for selecting multiple categories
function custom_patch_ups_exclude_posts_settings_form()
{
    ?>
    <div class="wrap">
        <h2>Custom Patch UP Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('custom_patch_ups_settings'); ?>
            <?php do_settings_sections('custom-patch-ups-settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Select Categories to Exclude Posts from Home Page</th>
                    <td>
                        <?php
                        $selected_categories = get_option('custom_patch_ups_categories', array());

                        $all_categories = get_categories();

                        foreach ($all_categories as $category) {
                            $checked = in_array($category->term_id, (array)$selected_categories) ? 'checked' : '';
                            echo '<div class="col-md-4"><input type="checkbox" id="category_' . $category->term_id . '" name="custom_patch_ups_categories[]" value="' . $category->term_id . '" ' . $checked . '><label for="category_' . $category->term_id . '">' . $category->name . '</label></div>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function custom_patch_ups_exclude_posts_register_settings()
{
    register_setting('custom_patch_ups_settings', 'custom_patch_ups_categories');
}
add_action('admin_init', 'custom_patch_ups_exclude_posts_register_settings');

// Code to exclude the specific category posts from the home page
function custom_patch_ups_exclude_posts_by_category_from_homepage($query)
{
    $theme = wp_get_theme();
    if ('Astra' == $theme->name || 'Astra' == $theme->parent_theme) {
        if ($query->is_home() && $query->is_main_query()) {
            $selected_categories = get_option('custom_patch_ups_categories');
            if ($selected_categories && is_array($selected_categories) && count($selected_categories) > 0) {
                $query->set('category__not_in', $selected_categories);
            }
        }
    }
}
add_action('pre_get_posts_custom_exclude', 'custom_patch_ups_exclude_posts_by_category_from_homepage');