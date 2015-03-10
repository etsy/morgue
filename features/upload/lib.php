<?php

class Uploader {

	public function __construct($filepath, $options) {

		// $app->getLog()->error("INSIDE");
		// $app->getLog()->error(print_r($options, 1));
		error_log( print_r($options, 1));
		error_log($filepath);

	}

}
