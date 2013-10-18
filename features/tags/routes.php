<?php

$app->post('/events/:id/tags', function($id) use ($app) {
    header("Content-Type: application/json");
    $tags = $app->request()->post('tags');
    $tags = explode(",", $tags);
    $tags = array_map('trim', $tags);
    $tags = array_map('strtolower', $tags);
    $res = Postmortem::save_tags_for_event($id, $tags);
    if ($res["status"] == Postmortem::ERROR) {
        $app->response()->status(400);
    } else {
        $app->response()->status(201);
        $tags = Postmortem::get_tags_for_event($id);
        if ($tags["status"] == Postmortem::ERROR) {
            $app->response()->status(404);
            return;
        } else {
            $output = json_encode($tags["values"]);
            echo str_replace("\\/", "/", $output);
        }
    }
});
$app->delete('/events/:event_id/tags/:tag_id', function($event_id, $tag_id) use ($app) {
    header("Content-Type: application/json");
    $res = Postmortem::delete_tag($tag_id, $event_id);
    if ($res["status"] == Postmortem::ERROR) {
        $app->response()->status(500);
        echo json_encode($res["error"]);
    } else {
        $app->response()->status(204);
    }

});
