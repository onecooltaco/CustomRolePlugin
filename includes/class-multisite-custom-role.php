<?php

class Multisite_Custom_Role
{
    public static function activate()
    {
        // get the Administrator role's object from WP_Role class
        $administrator = get_role('administrator');

        // a list of plugin-related capabilities to add to the Administrator role
        $caps = array(
            'manage_network',
            'manage_network_plugins',
            'manage_network_themes',
            'install_plugins',
            'install_themes',
            'update_plugins',
            'update_themes',
            'upload_plugins',
            'upload_themes',
            'activate_plugins',
            'activate_themes',
            'edit_plugins',
            'edit_themes',
            'delete_plugins',
            'delete_themes'
        );

        // add all the capabilities by looping through them
        foreach ($caps as $cap) {
            $administrator->add_cap($cap);
        }
    }
}
