<?php

$app->get('/events/:id/slack-channels', function($id) use ($app) {
    header("Content-Type: application/json");
    $channels = Slack::get_slack_channels_for_event($id);
    if ($channels["status"] == Slack::ERROR) {
        $app->response->status(404);
        return;
    }
    echo json_encode($channels["values"]);
});

$app->post('/events/:id/slack-channels', function($id) use ($app) {
    header("Content-Type: application/json");
    $channel_id  = $app->request->post('channel_id');
    $channel_name= $app->request->post('channel_name');
    $res = Slack::save_slack_channels_for_event($id, $channel_id, $channel_name);
    if ($res["status"] == Slack::ERROR) {
        $app->response->status(400);
        return;
    }

    $app->response->status(201);
    $channels = Slack::get_slack_channels_for_event($id);
    if ($channels["status"] == Slack::ERROR) {
        $app->response->status(404);
        return;
    }

    echo json_encode($channels["values"]);
});

$app->get('/events/:id/slack-channels/:channel', function($id, $channel) use ($app) {
    header("Content-Type: application/json");
    $chan = Slack::get_channel($channel);
    if ($chan["status"] == Slack::ERROR) {
        $app->response->status(404);
        return;
    }
    echo json_encode($chan["value"]);
});

$app->delete('/events/:id/slack-channels/:channel', function($id, $channel) use ($app) {
    header("Content-Type: application/json");
    $res = Slack::delete_channel($channel);
    if ($res["status"] == Slack::ERROR) {
        $app->response->status(500);
        echo json_encode($res["error"]);
        return;
    }
    $app->response->status(204);
});

$app->get('/events/:id/slack-channels-messages/:starttime/:endtime', function($id, $starttime, $endtime) use ($app) {
    header("Content-Type: application/html");


    $channels = Slack::get_slack_channels_for_event($id);
    if ($channels["status"] == Slack::ERROR) {
        $app->response->status(404);
        return;
    }

    $curlClient = new CurlClient();
    $slack = new Slack($curlClient);

    $channels_for_event= $channels["values"];
    $returnStr = '';
    foreach ($channels_for_event as $channelInfo) {
        $channel_id     = $channelInfo['channel_id'];
        $channel_name   = $channelInfo['channel_name'];

        $message = '';
        $message.='<h4>Conversation from #'.$channel_name.'</h4><div class="messages" id="'.$channel_id.'-message-div">';
        $message.=$slack->get_channel_messages_for_datetime_range($starttime, $endtime, $channel_id);
        $message.='<div>';

        $messageUpdate = Slack::update_slack_channel_message($channel_id, $message);
        if ($messageUpdate["status"] == Slack::ERROR) {
            $app->response->status(500);
            echo json_encode($messageUpdate["error"]);
            return;
        }

        $returnStr.=$message;
    }
    echo $returnStr;
});
