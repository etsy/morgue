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
		foreach ($options as $key => $value) {
			if (property_exists($this, $key)) {
				$this->$key = $value;
			}
		}
		$this->create_client();
	}

	private function create_client() {
		$this->client = new Client(array(
			'baseUri' => $this->url,
			'userName' => $this->username,
			'password' => $this->password,
			'proxy' => $this->proxy
		));
	}

	private function read_file($filepath) {
		$fh = fopen($filepath, 'r');
		$content =  fread($fh, filesize($filepath));
		fclose($fh);
		return $content;
	}

	// Creates the directory into which we will PUT our file
	private function make_destination($destination_dir) {
        if (empty($destination_dir)) {
            $dir_path = "{$this->username}";
        } else {
            $dir_path = "{$this->username}/{$destination_dir}";
        }

		$mkcol_response = $this->client->request('MKCOL', $dir_path);

		if ($mkcol_response['statusCode'] >= 400) {
			error_log("WebDAV Driver: {$mkcol_response['statusCode']} on MKCOL");
			// It kinda doesn't like creating a dir that exists
			// but only kinda.
		}
		return $dir_path;
	}


    public function send($filepath, $destination_dir = '') {

        // Can we optimize this and just use the tmp file
        // that was uploaded? Probably.
		$content = $this->read_file($filepath);

        $file_name = basename($filepath);
		$file_name = rawurlencode($file_name);

		$dir_path = $this->make_destination($destination_dir);

		$response = $this->client->request('PUT', "/{$dir_path}/" . $file_name, $content);
		$status = $response['statusCode'];
		error_log("WebDAV Driver: {$status} on PUT");

        if ($status >= 400) {
            throw new Exception("Upload failed PUTting the file");
        } else {
		}

		// Which response code when a new file is really 
		/* A status code of 201 means a new file was created.
		 * In that case there will also be a location value.
		 *
		 * A status code of 204 means a file of the same name
		 * already exists.
		 */
		if ($status == 204) {
			$location = $this->url . "/" .
				$dir_path . "/" . $file_name;			
		} else {
			$location = $response['headers']['location'];
		}

		return array(
			"location"	=> $location,
			"status"	=> $status
		);

	}

	private function process_response($response) {

	}
}
