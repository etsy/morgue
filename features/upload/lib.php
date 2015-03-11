<?php

class Uploader {

	public $default_uploader_type = "webdav";

	public $uploaders_path = "upload/lib";

	public function __construct($filepath, $options) {
		// No App in scope here. What about logging?
		error_log("Uploading: " . $filepath);

		// If we don't know, we use the default one.
		if (!isset($options['upload_driver'])) {
			$options['upload_driver'] = $this->default_uploader_type;
		}

		$type = strtolower($options['upload_driver']);
		$class_name = "Uploader_" . ucfirst($type);

		$settings = $options['upload_driver_options'];

		if (!class_exists($class_name)) {
			if ($lib_path = stream_resolve_include_path($this->uploaders_path .
				"/" . $type . ".php")) {
				require_once $lib_path;
			} else {
				throw new Exception("Could not fine uploader lib: {$this->uploaders_path}");
			}
		}

		error_log("Using : " . $type . " for uploads.");
		
		$driver = new $class_name($settings);
		$driver->setup();
		$driver->send($filepath);


		


	}

}
