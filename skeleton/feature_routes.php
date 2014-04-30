<?php
/**
 * Routes for %%FEATURE%%
 */
$app->get('/%%FEATURE%%', function () use ($app) {

    $content = "%%FEATURE%%/views/%%FEATURE%%";
    $page_title = "%%FEATURE%%";
    $show_sidebar = false;

    include "views/page.php";
});
