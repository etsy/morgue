<?php

class Uploader {

	public $default_uploader_type = "webdav";

    public $uploaders_path = "upload/lib";

    private $driver;

	public function __construct($options) {
		// No App in scope here. What about logging?

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
        $this->driver = $driver;

    }

    public function send_file($file_path, $event_id) {
        $location = $this->driver->send($file_path, '1');
        return $location;
    }

}
