<?php
/**
 * Routes for upload
 */

// Handle custom static assets.
// Javascript first then CSS.
$app->get('/upload/js/:path' , function ($path) use ($app) {
	// read the file if it exists. Then serve it back.	
	$file = stream_resolve_include_path("upload/assets/js/{$path}");
	if (!$file) {
		$app->response->status(404);
		$app->log->error("couldn't file custom js asset at $path");
		return;
	}
    $thru_file = file_get_contents($file);
	$app->response->header("Content-Type", "application/javascript");
	print $thru_file;
	return;
});
$app->get('/upload/css/:path' , function ($path) use ($app) {
	// read the file if it exists. Then serve it back.	
	$file = stream_resolve_include_path("upload/assets/css/{$path}");
	if (!$file) {
		$app->response->status(404);
		$app->log->error("couldn't file custom css asset at $path");
		return;
	}	
	$thru_file = file_get_contents($file);
	$app->response->header("Content-Type", "text/css");
    print $thru_file;
    return;
});


/*
 * This is to handle images coming in via our dropzone
 *
 * We assume that these images are to be associated with an event.
 */
$app->post('/upload/:id', function($id) use ($app) {
	$ds = DIRECTORY_SEPARATOR; 

	$config = Configuration::get_configuration();
	$upload_base_path = $config['upload_dir'];

	if (!empty($_FILES)) {
		// Step One: Put our uploaded files somewhere
		$tempFile = $_FILES['file']['tmp_name'];
		$targetPath = "{$upload_base_path}{$ds}{$id}{$ds}";
		// Ensure we have somewhere to upload to
		// We're grouping uploades by their associated event so
		// we're making directories here
		shell_exec("mkdir -p $targetPath");
		$targetFile =  $targetPath. $_FILES['file']['name'];

		if ( ! move_uploaded_file($tempFile,$targetFile) ) {
			$app->log->error("Error saving uploaded file.");
			$app->response->status(500);
		}

		$app->log->error("File Uploaded");
		$options = Configuration::get_configuration('upload');


		// Step Two: Send the file somewhere and expect a URL back
        $uploader = new Uploader($options);
        try {
            $response = $uploader->send_file($targetFile, $id);
        } catch (Exception $e) {
            print $e->getMessage();
            $app->log->error($e->getMessage());
            return;
        }
		$location = $response['location'];

        // we should have the $location of our uploaded file
        if (empty($location)) {
            throw new Exception("Upload expected an image location");
        }

		// Step Three: Add the URL of the file as an image for the event

		// Lucky us, we just have to call renderImage on the front
		// end. That will save the image to the the database.
		header("Content-Type: application/json");
		echo json_encode(array("location" => $location));

	} else {
		$app->log->error("Nothing to upload.");
		$app->response->status(400);
		return;
	} 
});


