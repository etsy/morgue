<?php

$app->get('/events/:id/tldr', function($id) use ($app) {
        $event = Postmortem::get_event($id);
        
        if (is_null($event["id"])) {
            $app->response->status(404);
            return;
        }
        header("Content-Type: application/json");
        echo json_encode(array("tldr" => $event["tldr"]));
});
