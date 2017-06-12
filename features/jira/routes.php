<?php

$app->get('/events/:id/tickets', function($id) use ($app) {
    header("Content-Type: application/json");
    $tickets = Jira::get_jira_tickets_for_event($id);
    if ($tickets["status"] == Jira::ERROR) {
        $app->response->status(404);
        return;
    } else {
        $tickets = Jira::merge_jira_tickets($tickets["values"]);
        echo json_encode($tickets);
    }

});

$app->post('/events/:id/tickets/create', function ($id) use ($app) {
    header("Content-Type: application/json");
    $curl = new CurlClient();
    $jira = new JiraClient($curl);
    $project = $app->request()->post('project');
    $summary = $app->request()->post('summary');
    $description = $app->request()->post('description');
    $issuetype = $app->request()->post('issuetype');
    $res = $jira->createJiraTicket($project, $summary, $description, $issuetype);
    if ($res["status"] == Jira::ERROR) {
        $app->response->status(400);
    } else {
        $app->response->status(201);
        if ($tickets["status"] == Jira::ERROR) {
            $app->response->status(404);
            return;
        } else {
            echo json_encode($res);
        }
    }

});
$app->post('/events/:id/tickets', function($id) use ($app) {
    header("Content-Type: application/json");
    $curl = new CurlClient();
    $jira = new JiraClient($curl);
    $tickets = explode(',', $app->request()->post('tickets'));
    $tickets = array_map('trim', $tickets);
    $tickets = array_keys($jira->getJiraTickets($tickets));
    $res = Jira::save_jira_tickets_for_event($id, $tickets);
    if ($res["status"] == Jira::ERROR) {
        $app->response->status(400);
    } else {
        $app->response->status(201);
        $tickets = Jira::get_jira_tickets_for_event($id);
        if ($tickets["status"] == Jira::ERROR) {
            $app->response->status(404);
            return;
        } else {
            $tickets = Jira::merge_jira_tickets($tickets["values"]);
            echo json_encode($tickets);
        }
    }

});
$app->get('/events/:id/tickets/:ticket', function($id, $ticket) use ($app) {
    header("Content-Type: application/json");
    $tick = Jira::get_ticket($ticket);
    if ($tick["status"] == Jira::ERROR) {
        $app->response->status(404);
        return;
    } else {
        echo json_encode($tick["value"]);
    }

});
$app->delete('/events/:id/tickets/:ticket', function($id, $ticket) use ($app) {
    header("Content-Type: application/json");
    $res = Jira::delete_ticket($ticket);
    if ($res["status"] == Jira::ERROR) {
        $app->response->status(500);
        echo json_encode($res["error"]);
    } else {
        $app->response->status(204);
    }

});
