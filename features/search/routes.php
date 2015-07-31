<?php

$app->get('/search', function() use ($app) {
        $q = $app->request->get('q');
        $q = urldecode($q);

        if($q === null || $q === "" || $q === "\"\""){
            $app->redirect('/');
        } else {
            $results = Search::perform($q);
            $content = "search/views/search";
            $show_sidebar = false;
            $page_title = "Search Results";
            include "views/page.php";
        }
});