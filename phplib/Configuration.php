<?php

/**
 * a simple way to get configuration
 */
class Configuration {

    /**
     * get the configuration from the JSON files
     *
     * @param name - name of the specific feature to get config for
     *
     * @returns a dictionary object with the config data or an empty array
     */
    static function get_configuration($name = null) {
        $enviroment = getenv('MORGUE_ENVIRONMENT') ?: 'development';
        $configfile = dirname(__FILE__).'/../config/'.$enviroment.'.json';
        $config = json_decode(file_get_contents($configfile), true);
        if (is_null($name)) {
            return $config;
        } else {
            foreach($config["feature"] as $feature) {
                if ($feature['name'] == $name) {
                    return $feature;
                }
            }
            return array();
        }
    }

    /**
     * feature_enabled
     *
     * @param mixed $name
     * @static
     * @access public
     * @return boolean if the named feature is marked as 'enabled' => 'on'
     */
    static function feature_enabled($name = null) {
        if (!$name) {
            return false;
        }
        $c = self::get_configuration($name);
        if ($c['enabled'] === 'on') {
            return true;
        }
        return false;
    }

    static function get_navbar_features() {
        $navbar_features = array();
        $c = self::get_configuration();
        foreach ($c['feature'] as $feature) {

            if (array_key_exists('navbar', $feature) &&
                $feature['navbar'] === 'on' &&
                $feature['enabled'] === 'on') {
                $navbar_features[] = $feature;
            }
        }
        return $navbar_features;
    }
}
