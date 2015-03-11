<?php

use Sabre\DAV\Client;

include 'vendor/autoload.php';
/* 
 * Wraps and adapts Sabre webdav client to our use.
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

	public function send($filepath) {
		$fh = fopen($filepath, 'r');
		$content =  fread($fh, filesize($filepath));
		fclose($fh);

		$file_name = basename($filepath);

		$response = $this->client->request('PUT', '/webdav/' . $file_name, $content);
		// TODO: check response
		print_r($response);
		

	}
}
