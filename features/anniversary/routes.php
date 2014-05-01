<?php
/**
 * Routes for Anniversay Feature
 */

$app->get('/anniversary', function () use ($app) {

    $content = "anniversary/views/anniversary";

    $show_sidebar = false;
    $page_title = "Today in Post Mortem History";
    $today = date("Y-m-d", time());

    $get_date = trim($app->request()->get('date'));
    if ($get_date) {
        $get_date = date("Y-m-d", strtotime($get_date));
        $today = $get_date;
    }

    $conn = Persistence::get_database_object();
    $pm_ids = Anniversary::get_ids($today, $conn);
    $human_readable_date = date("F jS", strtotime($today));
    $pms = array();

    if ($pm_ids['status'] === 0) {
        foreach ($pm_ids['values'] as $k => $v) {
            $pm = Persistence::get_postmortem($v['id'], $conn);
            $pms[] = $pm;
        }
    } else {
        $message = $pm_ids['error'];
        $content = "error";
        include 'views/page.php';
        return;
    }

    if (count($pms)) {
        // get the tags for each PM we found so we can display them
        foreach ($pms as $k => $pm) {
            $tags = Postmortem::get_tags_for_event($pm['id'], null);
            $pms[$k]['tags'] = $tags['values'];
        }
    }

    $pms = $pms;
    include  'views/page.php';

});


