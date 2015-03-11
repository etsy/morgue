<?php

use Sabre\DAV\Client;

include 'vendor/autoload.php';
/* 
 * Wraps and adapts Sabre webdav client to our use.
 * By design all files will be uploaded to a directory
 * named the same as the username.
 * In that directory we will attempt to create a directory
 * named for the event_id (some number) and images
 * associated with that event will be placed in that directory.
 *
 * Example.  Username: morgue
 *           Event ID: 13
 *           upload file name: a_graph.png
 * will attempt to MKCOL /morgue/13
 * and then PUT our file into /morgue/13/a_graph.png
 */
class Uploader_Webdav {

	public $url;
	public $username;
	public $password;
	public $proxy;

	// Please pass in the appropriate stuff
	public function __construct($options = []) {
		foreach ($options as $key => $val) {
			$this->set($key, $val);
		}
	}

	public function set($key, $value) {
		if (property_exists($this, $key)) {
			$this->$key = $value;
		}
	}

	public function setup() {
		$client = new Client(array(
			'baseUri' => $this->url,
			'userName' => $this->username,
			'password' => $this->password,
			'proxy' => $this->proxy
		));
		$this->client = $client;
	}

    public function send($filepath, $destination_dir = '') {

        // Read the file in from the filesystem
        // TODO: Can we optimize this and just use the tmp file
        // that was uploaded? Probably.
		$fh = fopen($filepath, 'r');
		$content =  fread($fh, filesize($filepath));
		fclose($fh);

        $file_name = basename($filepath);

        if (empty($destination_dir)) {
            $dir_path = "/{$this->username}";
        } else {
            $dir_path = "/{$this->username}/{$destination_dir}";
        }

        $mkcol_response = $this->client->request('MKCOL', $dir_path);
        // TODO: Check response

        if ($mkcol_response['statusCode'] >= 400) {
//            throw new Exception("Upload failed MKCOL");
        } 

		$response = $this->client->request('PUT', "/{$dir_path}/" . $file_name, $content);
		// TODO: check response
        if ($response['statusCode'] >= 400) {
            // something went wrong
            throw new Exception("Upload failed PUTting the file");
        } else {
            print_r($response);
            $location = $response['headers']['location'];
        }
        return $location;

	}
}
