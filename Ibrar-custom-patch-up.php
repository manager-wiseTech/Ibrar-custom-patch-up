<?php
/*
 * Plugin Name:       Ibrar's Custom Patch UPs
 * Description:       This plugin is used to exclude specific category posts from home page.
 * Author:            Ibrar Ayub
 * Version:           1.0.0
*/

if(! defined ('ABSPATH')){
    die;
}

// Code to exclude the specific category posts from the home page
function custom_patch_ups_exclude_category_by_id_from_homepage($query) {
    $theme = wp_get_theme(); // gets the current theme
    if ( 'Astra' == $theme->name || 'Astra' == $theme->parent_theme ) {
        if ($query->is_home() && $query->is_main_query()) {
            $excluded_category_slug = 'feature';
            $category = get_category_by_slug($excluded_category_slug);
        
        if ($category) {
            $excluded_category_id = $category->term_id;
            $query->set('category__not_in', array($excluded_category_id));
        }
      }
    }
}
add_action('pre_get_posts', 'custom_patch_ups_exclude_category_by_id_from_homepage');