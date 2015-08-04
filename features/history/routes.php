<?php

$app->get('/history/:event_id/:history_id', function($event_id, $history_id) use ($app) {
    $event = Postmortem::get_event($event_id);
    $history = Postmortem::get_history_event($history_id);

    $timezone = getUserTimezone();
    $tz = new DateTimeZone($timezone);
    $edited = new DateTime();
    $edited->setTimestamp($history['create_date']);
    $edited->setTimezone($tz);
    $edited = $edited->format('m/d/Y G:ia');
    $edited = " @ " . $edited;

    $content = "history/views/diff";
    $show_sidebar = false;
    $page_title = "Event History";
    include "views/page.php";
});