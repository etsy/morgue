<?php

class MorgueAuth {

    /**
     * wraper function to call an auth implementation if there is one and
     * return the default user if not
     *
     * @returns auth data as a dictionary
     */
    static function get_auth_data() {
        if (function_exists("morgue_get_user_data")) {
            $admin_data =  morgue_get_user_data();
        } else {
            $admin_data = array("username" => "morgue_user");
        }
        return $admin_data;
    }
}
