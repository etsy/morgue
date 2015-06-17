<?php

class Calendar {

    function __construct() {
        $config = Configuration::get_configuration("calendar");

        $this->clientId = $config['clientId'];
        $this->apiKey = $config['apiKey'];
        $this->scopes = $config['scopes'];
        $this->id = $config['id'];
    }

}

?>