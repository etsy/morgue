<?php

class CurlClient {

    function get($url, array $params = null, $user_pass = null, $proxy = null,
                 $timeout = 10) {
        $query_string = empty($params)
                      ? ''
                      : '?' . http_build_query($params);
        $ch = curl_init($url . $query_string);
        $options = array(
            CURLOPT_HTTPGET => true,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => $timeout,
        );
        if ($user_pass) {
            if ($user_pass != ":") {
                $options[CURLOPT_USERPWD] = $user_pass;
            }
        }
        if ($proxy) {
            $options[CURLOPT_PROXY] = $proxy;
        }
        curl_setopt_array($ch, $options);
        $result = trim(curl_exec($ch));
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status_code != 200) {
            error_log("Got unexpected HTTP status code $status_code from $url");
        }
        curl_close($ch);
        return $result;
    }

    function post($url, array $params = null, $user_pass = null, $proxy = null, $timeout = 10) {
        //open connection
        $query_string = empty($params)
                      ? ''
                      : '?' . http_build_query($params);
        $ch = curl_init($url . $query_string);

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_HEADER, 0);
        curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=UTF-8'));
        curl_setopt($ch,CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch,CURLOPT_USERPWD, $user_pass);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //execute post
        $result = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($status_code != 200) {
            error_log("Got unexpected HTTP status code $status_code from $url");
        }
        //close connection
        curl_close($ch);

        return $result;
    }
}
