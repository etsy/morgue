<?php
/**
 * Routes for Anniversay Feature
 */

$app->get('/api/anniversary', function () use ($app) {
	/* 
	 * JSON boolean if there is an anniversary today or not
	 */
    $today = gmdate("Y-m-d", time());
    $get_date = trim($app->request()->get('date'));
    if ($get_date) {
        $get_date = gmdate("Y-m-d", strtotime($get_date));
        $today = $get_date;
    }

    $conn = Persistence::get_database_object();
	$pm_ids = Anniversary::get_ids($today, $conn);
	if ($pm_ids['status'] === Anniversary::OK) {
		$anivs = count($pm_ids['values']);
	} else {
		$anivs = 0;
	}
	header("Content-Type: application/json");
	echo json_encode(array("anniversaries_today" => $anivs));
});



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

$app->get('/anniversary/mail', function () use ($app) {
    $config =  Configuration::get_configuration('anniversary');
    if ($config['enabled'] !== 'on') {
        return;
    }
    if (empty($config['mailto'])) {
        return;
    }


    $content = "anniversary/views/anniversary-mail";

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

    if (count($pms)) {

        ob_start();
        include $content. ".php";
        $out = ob_get_contents();
        ob_end_clean();

        print $out;

        $to = $config['mailto'];
        $subject = "$subject";
        $message = $out;
        $headers = "From: {$config['mailfrom']}\r\n";
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "Return-Path: {$config['mailfrom']}\r\n";

        $ok = mail($to, $subject, $message, $headers, "-f{$config['mailfrom']}");

        print  "Mail sent. {$ok}";
    } else {
        print "Nothing broke on this date.";
    }

});


// TODO: Can we add this to all feature routes? At least in the skeleton?
// Handle custom static assets.
// Javascript first then CSS.
$app->get('/anniversary/js/:path' , function ($path) use ($app) {
	// read the file if it exists. Then serve it back.	
	$file = stream_resolve_include_path("anniversary/assets/js/{$path}");
	if (!$file) {
		$app->response()->status(404);
		$app->getLog()->error("couldn't file custom js asset at $path");
		return;
	}
    $thru_file = file_get_contents($file);
	$app->response()->header("Content-Type", "application/javascript");
	print $thru_file;
	return;
});
$app->get('/anniversary/css/:path' , function ($path) use ($app) {
	// read the file if it exists. Then serve it back.	
	$file = stream_resolve_include_path("anniversary/assets/css/{$path}");
	if (!$file) {
		$app->response()->status(404);
		$app->getLog()->error("couldn't file custom css asset at $path");
		return;
	}	
	$thru_file = file_get_contents($file);
	$app->response()->header("Content-Type", "text/css");
    print $thru_file;
    return;
});




