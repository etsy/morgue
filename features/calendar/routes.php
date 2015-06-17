<?php

$app->get('/calendar', function () use ($app) {

        $content = "calendar/views/calendar_page";
        $show_sidebar = false;
        $page_title = "Post Mortem Calendar";
        
        include 'views/page.php';
});