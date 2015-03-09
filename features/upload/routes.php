<?php
/**
 * Routes for upload
 */

$app->get('/upload/js/:path' , function ($path) use ($app) {
	die($path);
	// read the file if it exists. Then serve it back.	
	$file = stream_resolve_include_path("upload/assets/js/{$path}");
	if (!$file) {
		$app->response()->status(404);
		$app->getLog()->error("couldn't file custom js asset at $path");
		return;
	}
	$thru_file = file_get_contents($file);
	// $app->response()->headers()->set('Content-Type', 'application/javascript');	
	print $thru_file;
	return;
});



$app->get('/uploadX/', function () use ($app) {

    $content = "views/error";
    $page_title = "upload";
	$message = "This path doesn't accept GET";
    $show_sidebar = false;

    include "views/page.php";
});


// This is for images coming from dropzone
$app->post('/upload/:id', function($id) use ($app) {
	$ds = DIRECTORY_SEPARATOR; 

	$config = Configuration::get_configuration();
	$upload_base_path = $config['upload_dir'];

	if (!empty($_FILES)) {
		$tempFile = $_FILES['file']['tmp_name'];
		$targetPath = "{$upload_base_path}{$ds}{$id}{$ds}";
		// Ensure we have somewhere to upload to
		// We're grouping uploades by their associated event so
		// we're making directories here
		shell_exec("mkdir -p $targetPath");
		$targetFile =  $targetPath. $_FILES['file']['name'];
		if ( ! move_uploaded_file($tempFile,$targetFile) ) {
			$app->getLog()->error("Error saving uploaded file.");
			$app->response()->status(500);
		}
	} else {
		$app->getLog()->error("Nothign to upload.");
		$app->response()->status(500);
		return;
	} 
});


