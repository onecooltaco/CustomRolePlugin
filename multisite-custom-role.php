<?php

/**
 * Plugin Name:  Multisite Custom Role
 * Version:  1.0
 * Description:  Customize WordPress User Roles.
 * Author:  Jeremy Leggat
 * Author URI:  https://cronkite.asu.edu
 * GitHub Plugin URI: https://github.com/onecooltaco/Multisite-Custom-Role
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  custom-role
 */

// this code runs only during plugin activation and never again

/*
This code runs on every site in the network
when the plugin is Network Activated
*/
function add_multisite_custom_role($network_wide)
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-multisite-custom-role.php';
 
    if (is_multisite() && $network_wide) {
        // run the code for all sites in a Multisite network
        foreach (get_sites(['fields' => 'ids']) as $blog_id) {
            switch_to_blog($blog_id);
                Multisite_Custom_Role::activate();
        }
            restore_current_blog();
    }
}
register_activation_hook(__FILE__, 'add_multisite_custom_role');

// run the code once again when a new site is created
function add_multisite_custom_role_new_site($blog_id)
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-multisite-custom-role.php';
    // check whether the plugin is active for the network
    if (is_plugin_active_for_network('Multisite-Custom-Role/multisite-custom-role.php')) {
        switch_to_blog($blog_id);
        Multisite_Custom_Role::activate();
        restore_current_blog();
    }
}
add_action('wpmu_new_blog', 'add_multisite_custom_role_new_site');

function custom_admin_caps($caps, $cap, $user_id, $args)
{
 
    foreach ($caps as $key => $capability) {
        if ($capability != 'do_not_allow') {
            continue;
        }
 
        switch ($cap) {
            case 'edit_css':
            case 'unfiltered_html':
                // Disallow unfiltered_html for all users, even admins and super admins.
                if (defined('DISALLOW_UNFILTERED_HTML') && DISALLOW_UNFILTERED_HTML) {
                    $caps[] = 'do_not_allow';
                } else {
                    $caps[] = 'unfiltered_html';
                }
                break;
            case 'edit_files':
            case 'edit_plugins':
            case 'edit_themes':
                // Disallow the file editors.
                if (defined('DISALLOW_FILE_EDIT') && DISALLOW_FILE_EDIT) {
                    $caps[] = 'do_not_allow';
                } elseif (! wp_is_file_mod_allowed('capability_edit_themes')) {
                    $caps[] = 'do_not_allow';
                } else {
                    $caps[] = $cap;
                }
                break;
            case 'update_plugins':
            case 'delete_plugins':
            case 'install_plugins':
            case 'upload_plugins':
            case 'update_themes':
            case 'delete_themes':
            case 'install_themes':
            case 'upload_themes':
            case 'update_core':
                // Disallow anything that creates, deletes, or updates core, plugin, or theme files.
                // Files in uploads are excepted.
                if (! wp_is_file_mod_allowed('capability_update_core')) {
                    $caps[] = 'do_not_allow';
                } elseif ('upload_themes' === $cap) {
                    $caps[] = 'install_themes';
                } elseif ('upload_plugins' === $cap) {
                    $caps[] = 'install_plugins';
                } else {
                    $caps[] = $cap;
                }
                break;
        }
    }
 
    return $caps;
}
add_filter('map_meta_cap', 'custom_admin_caps', 1, 4);
