<?php

class Contact {

    /**
     * get the view url for a given username. The url is
     * constructed from the lookup_url defined in the contact
     * config.
     *
     * @param $username - the username to subsitutre in the lookup_url
     *
     * @returns null or lookup_url with %s substituted by username
     */
    static function get_url_for_user($username, $config = null) {
        $url = null;
        $config = is_null($config) ? Configuration::get_configuration("contact") : $config;
        if (isset($config['lookup_url'])) {
            $url = sprintf($config['lookup_url'], $username);
        }
        return $url;
    }


    /**
     * get the html snippet for a given username. The html code
     * is either the username or a link to the view page for the given
     * username, if a lookup url is defined in the contact config
     *
     * @param $username - the username to subsitutre in the lookup_url
     *
     * @returns $username or <a href="lookup_url" target="_new">$username</a>
     */
    static function get_html_for_user($username, $config = null) {
        $html = "";
        $url = self::get_url_for_user($username, $config);
        if (is_null($url)) {
            $html = $username;
        } else {
            $html = "<a href=\"$url\" target=\"_new\" style=\"text-decoration:none;\">$username</a>";
        }
        return $html;
    }

    /**
     * get the email address for a given username. The email address
     * is the username with the email domain appended to it. If email domain
     * is not specified in the contact config, then null is returned
     *
     * @param $username - the username to subsitutre in the lookup_url
     *
     * @returns $email or null 
     */
    static function get_email_for_user($username, $config = null) {
        $config = is_null($config) ? Configuration::get_configuration("contact") : $config;
        if (isset($config["email_domain"])) {
            return $username . $config["email_domain"];
        } else {
            return null;
        }
    }
}
