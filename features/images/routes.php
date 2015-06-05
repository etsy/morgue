<?php

$app->get('/events/:id/images', function($id) use ($app) {
    header("Content-Type: application/json");
    $images = Images::get_images_for_event($id);
    if ($images["status"] == Images::ERROR) {
        $app->response->status(404);
        return;
    } else {
        $output = json_encode($images["values"]);
        echo str_replace("\\/", "/", $output);
    }
});
$app->post('/events/:id/images', function($id) use ($app) {
    header("Content-Type: application/json");
    $images = $app->request->post('images');
    $images = explode(",", $images);
    $images = array_map('trim', $images);
    $res = Images::save_images_for_event($id, $images);
    if ($res["status"] == Images::ERROR) {
        $app->response->status(400);
    } else {
        $app->response->status(201);
        $images = Images::get_images_for_event($id);
        if ($images["status"] == Images::ERROR) {
            $app->response->status(404);
            return;
        } else {
            $output = json_encode($images["values"]);
            echo str_replace("\\/", "/", $output);
        }
    }
});
$app->get('/events/:id/images/:img', function($id, $img) use ($app) {
    header("Content-Type: application/json");
    $image = Images::get_image($img);
    if ($image["status"] == Images::ERROR) {
        $app->response->status(404);
        return;
    } else {
        echo json_encode($image["value"]);
    }

});
$app->delete('/events/:id/images/:image', function($id, $image) use ($app) {
    header("Content-Type: application/json");
    $res = Images::delete_image($image);
    if ($res["status"] == Images::ERROR) {
        $app->response->status(500);
        echo json_encode($res["error"]);
    } else {
        $app->response->status(204);
    }
});
