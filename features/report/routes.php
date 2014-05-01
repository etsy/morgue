<?php
/**
 * Routes for report
 */
$app->get('/report', function () use ($app) {

    $content = "report/views/report";

    $page_title = "report";
    $show_sidebar = false;


    $days_back = 30;
    $start_date = time() - ($days_back * 86400);
    $end_date = time();

    $results = Postmortem::get_events_by_date($start_date, $end_date);



    if ($results['status'] == Postmortem::OK) {
        $events = $results['values'];
        uasort($events, 'cmp');
    } else {
        $content = 'error';
        $message = $results['error'];
        include "views/page.php";
        return;
    }

    include "views/page.php";
});
