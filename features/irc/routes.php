<?php

$app->get('/events/:id/channels', function($id) use ($app) {
    header("Content-Type: application/json");
    $channels = Irc::get_irc_channels_for_event($id);
    if ($channels["status"] == Irc::ERROR) {
        $app->response->status(404);
        return;
    } else {
        echo json_encode($channels["values"]);
    }

});
$app->post('/events/:id/channels', function($id) use ($app) {
    header("Content-Type: application/json");
    $channels = $app->request->post('channels');
    $channels = explode(",", $channels);
    $channels = array_map('trim', $channels);
    $res = Irc::save_irc_channels_for_event($id, $channels);
    if ($res["status"] == Irc::ERROR) {
        $app->response->status(400);
    } else {
        $app->response->status(201);
        $channels = Irc::get_irc_channels_for_event($id);
        if ($channels["status"] == Irc::ERROR) {
            $app->response->status(404);
            return;
        } else {
            echo json_encode($channels["values"]);
        }
    }

});
$app->get('/events/:id/channels/:channel', function($id, $channel) use ($app) {
    header("Content-Type: application/json");
    $chan = Irc::get_channel($channel);
    if ($chan["status"] == Irc::ERROR) {
        $app->response->status(404);
        return;
    } else {
        echo json_encode($chan["value"]);
    }

});
$app->delete('/events/:id/channels/:channel', function($id, $channel) use ($app) {
    header("Content-Type: application/json");
    $res = Irc::delete_channel($channel);
    if ($res["status"] == Irc::ERROR) {
        $app->response->status(500);
        echo json_encode($res["error"]);
    } else {
        $app->response->status(204);
    }

});

