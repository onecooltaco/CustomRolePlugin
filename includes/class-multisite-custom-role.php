<?php

class Multisite_Custom_Role
{
    public static function activate()
    {
        // get the Administrator role's object from WP_Role class
        $administrator = get_role('administrator');

        // a list of plugin-related capabilities to add to the Administrator role
        $caps = array(
                  'install_plugins',
                  'activate_plugins',
                  'edit_plugins',
                  'delete_plugins'
        );

        // add all the capabilities by looping through them
        foreach ($caps as $cap) {
            $administrator->add_cap($cap);
        }
    }
}
