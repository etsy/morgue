<?php
require_once 'phplib/CurlClient.php';
require_once 'phplib/Postmortem.php';
require_once 'phplib/Configuration.php';
require_once 'phplib/Auth.php';

require_once 'vendor/autoload.php';

@include 'phplib/deploy_version.php';

if (!defined('MORGUE_VERSION')) {
    define('MORGUE_VERSION', '');
}

$config = Configuration::get_configuration();

if (!$config) {
	$message = "Could not parse configuration file.";
	$content = "error";
	error_log("ERROR: " . $message);
	include __DIR__.'/views/page.php';
	die();
}
$app = new Slim();
$app->config('debug', true);

$app->getLog()->setEnabled(true);

if ($config['environment'] == "development") {
	$app->getLog()->setLevel(4);
} else {
	$app->getLog()->setLevel(1);
}

// must be require_once'd after the Slim autoloader is registered
require_once 'phplib/AssetVersionMiddleware.php';

// helper method for returning the selected timezone.
// If set, get the user timezone else get it from the global config
// otherwise default to 'America/New_York'
// returns: string
function getUserTimezone() {
    $config = Configuration::get_configuration();
    $tz = 'America/New_York';

    if ( isset($_SESSION['timezone']) ) {
      $tz = $_SESSION['timezone'];
    } else if ( isset($config['timezone']) ) {
      $tz = $config['timezone'];
    }

    return $tz;
}

// helper method to sort events reverse by starttime
function cmp($first, $second) {
    if ($first['starttime'] == $second['starttime'] ) {
        return 0;
    }
    return ( $first['starttime'] < $second['starttime'] ) ? 1 : -1;
}
// helper method to format the difference between two dates
// param: diff between two dates
// returns: string
function getTimeString($diff) {
    $min = floor($diff / 60 % 60);
    $hours = floor($diff / 60 / 60);
    if ($min == 1) {
        $min = $min." minute";
    } else {
        $min = $min." minutes";
    }
    if ($hours == 1) {
        $hours = $hours." hour";
    } else {
        $hours = $hours." hours";
    }
    return $hours.", ".$min;
}

/**
 * Helper function for default statustime.
 */
function default_status_time() {
    return new DateTime('1970-01-01', new DateTimeZone('UTC'));
}


$app->add(
    new Slim_Middleware_SessionCookie(
        array(
            'expires' => '60 minutes',
            'path' => '/',
            'domain' => null,
            'secure' => false,
            'httponly' => false,
            'name' => 'postmortem',
            'secret' => 'PMS_R_US',
            'cipher' => MCRYPT_RIJNDAEL_256,
            'cipher_mode' => MCRYPT_MODE_CBC
        )
    )
);

$app->add(new AssetVersionMiddleware);



/*
 * Now include all routes and libraries for features before actually running the app
 */
foreach ($config['feature'] as $feature) {
    if ($feature['enabled'] == "on") {
        $app->getLog()->debug("Including Feature {$feature['name']}");
        include  $feature['name'] . '/lib.php';
        include  $feature['name'] . '/routes.php';
    }
}



// set admin info on the environment array
// so it's available to our request handlers
$env = $app->environment();
$env['admin'] = MorgueAuth::get_auth_data();

$app->get('/', function() use ($app) {
    $content = 'content/frontpage';
    $show_sidebar = true;

    $selected_tags = trim($app->request()->get('tags'));
    if (strlen($selected_tags) > 0) {
        $selected_tags = explode(",", $selected_tags);
        $selected_tags = array_map('trim', $selected_tags);
        $events = Postmortem::get_events_for_tags($selected_tags);
    } else {
        $selected_tags = null;
        $events = Postmortem::get_all_events();
    }

    if ($events["status"] == Postmortem::OK) {
        $events = $events["values"];
    } else {
        $app->response()->status(500);
        echo json_encode($events["error"]);
        return;
    }

    uasort($events, 'cmp');

    $tags = Postmortem::get_tags();

    if ($tags["status"] == Postmortem::OK) {
        $tags = $tags["values"];
    } else {
        $tags = array();
    }


    include 'views/page.php';
});

$app->post('/timezone', function () use ($app) {
    $_SESSION['timezone'] = $app->request()->post('timezone');
    $app->redirect($app->request()->getReferrer());
});

$app->post('/events', function () use ($app) {
    $title = $app->request()->post('title');
    $start_date = $app->request()->post('start_date');
    $start_time = $app->request()->post('start_time');
    $end_date = $app->request()->post('end_date');
    $end_time = $app->request()->post('end_time');
    $detect_date = $app->request()->post('detect_date');
    $detect_time = $app->request()->post('detect_time');
    $status_date = $app->request()->post('status_date');
    $status_time = $app->request()->post('status_time');
    $timezone = $app->request()->post('timezone');
    $severity = $app->request()->post('severity');
    $contact = $app->request()->post('contact');
    $gcal = $app->request()->post('gcal');
    $startdate = new DateTime($start_date." ".$start_time, new DateTimeZone($timezone));
    $enddate = new DateTime($end_date." ".$end_time, new DateTimeZone($timezone));
    $detectdate = new DateTime($detect_date." ".$detect_time, new DateTimeZone($timezone));
    if (!$status_date || !$status_time) {
        $statusdate = default_status_time();
    } else {
        $statusdate = new DateTime("$status_date $status_time", new DateTimeZone($timezone));
    }


    $event = array(
        "title" => $title,
        "summary" => "",
        "starttime" => $startdate->getTimeStamp(),
        "endtime" => $enddate->getTimeStamp(),
        "detecttime" => $detectdate->getTimeStamp(),
        "statustime" => $statusdate->getTimeStamp(),
        "severity" => $severity,
        "contact" => $contact,
        "gcal" => $gcal,
    );

    $event = Postmortem::save_event($event);
    $app->redirect('/events/'.$event["id"]);
});

$app->get('/events/:id', function($id) use ($app) {

    $event = Postmortem::get_event($id);
    if (is_null($event["id"])) {
        echo "loooool";
        $app->response()->status(404);
        return;
    }

    $page_title = sprintf("%s | Morgue", $event['title']);

    $starttime = $event["starttime"];
    $endtime = $event["endtime"];
    $detect_time = $event["detecttime"];
    $status_time = $event["statustime"];
    $timezone = getUserTimezone();
    $severity = $event["severity"];
    $gcal = $event["gcal"];
    $contact = $event["contact"];
    $summary = $event["summary"];

    $tz = new DateTimeZone($timezone);
    $start_datetime = new DateTime("@$starttime");
    $start_datetime->setTimezone($tz);
    $end_datetime = new DateTime("@$endtime");
    $end_datetime->setTimezone($tz);
    if ($status_time) {
        $status_datetime = new DateTime("@$status_time");
        $status_datetime->setTimezone($tz);
    } else {
        $status_datetime = false;
    }
    $detect_datetime = new DateTime("@$detect_time");
    $detect_datetime->setTimezone($tz);
    $impacttime = getTimeString($endtime - $starttime);
    $resolvetime = getTimeString($endtime - $detect_time);
    $undetecttime = getTimeString($detect_time - $starttime);

    $content = 'content/edit';

    $curl_client = new CurlClient();

    $show_sidebar = false;
    include 'views/page.php';
});

$app->delete('/events/:id', function($id) use ($app) {
    header("Content-Type: application/json");
    $res = Postmortem::delete_event($id);
    if ($res["status"] == Postmortem::ERROR) {
        $app->response()->status(500);
        echo json_encode($res["error"]);
    } else {
        $app->response()->status(204);
    }
});

$app->get('/events/:id/undelete', function($id) use ($app) {
    $res = Postmortem::undelete_event($id);
    if ($res["status"] == Postmortem::ERROR) {
        $app->response()->status(500);
        $app->response()->body($res["error"]);
    } else {
        $app->redirect("/events/$id");
    }
});

$app->get('/events/:id/summary', function($id) use ($app) {
    $event = Postmortem::get_event($id);

    if (is_null($event["id"])) {
        $app->response()->status(404);
        return;
    }
    header("Content-Type: application/json");
    echo json_encode(array("summary" => $event["summary"]));

});

$app->put('/events/:id', function ($id) use ($app) {
    // get the base event data
    $event = Postmortem::get_event($id);
    if (is_null($event["id"])) {
        $app->response()->status(500);
        return;
    }

    $params = $app->request()->params();
    foreach ($params as $key => $value) {
        switch($key) {
        case "title":
            $event["title"] = $value;
            break;
        case "summary":
            $event["summary"] = $value;
            break;
        case "start_date":
        case "start_time":
            if (!isset($params["timezone"])) {
                $app->response()->status(400);
                return;
            }
            $timezone = new DateTimeZone($params["timezone"]);
            $starttime = $event["starttime"];
            $edate = new DateTime("@$starttime");
            $edate->setTimezone($timezone);
            $new_date = date_parse($value);
            if ($key == "start_time") {
                $edate->setTime($new_date["hour"], $new_date["minute"]);
            } elseif ($key == "start_date") {
                $edate->setDate($new_date["year"], $new_date["month"], $new_date["day"]);
            }
            $event["starttime"] = $edate->getTimeStamp();
            break;
        case "end_date":
        case "end_time":
            if (! isset($params["timezone"])) {
                $app->response()->status(400);
                return;
            }
            $timezone = new DateTimeZone($params["timezone"]);
            $endtime = $event["endtime"];
            $edate = new DateTime("@$endtime");
            $edate->setTimezone($timezone);
            $new_date = date_parse($value);
            if ($key == "end_time") {
                $edate->setTime($new_date["hour"], $new_date["minute"]);
            } elseif ($key == "end_date") {
                $edate->setDate($new_date["year"], $new_date["month"], $new_date["day"]);
            }
            $event["endtime"] = $edate->getTimeStamp();
            break;
        case "detect_date":
        case "detect_time":
            if (! isset($params["timezone"])) {
                $app->response()->status(400);
                return;
            }
            $timezone = new DateTimeZone($params["timezone"]);
            $detecttime = $event["detecttime"];
            $edate = new DateTime("@$detecttime");
            $edate->setTimezone($timezone);
            $new_date = date_parse($value);
            if ($key == "detect_time") {
                $edate->setTime($new_date["hour"], $new_date["minute"]);
            } elseif ($key == "detect_date") {
                $edate->setDate($new_date["year"], $new_date["month"], $new_date["day"]);
            }
            $event["detecttime"] = $edate->getTimeStamp();
            break;
        case "status_datetime":
            if (!$value) {
                $event["statustime"] = 0;
                break;
            }

            if (! isset($params["timezone"])) {
                $app->response()->status(400);
                return;
            }
            $timezone = new DateTimeZone($params["timezone"]);
            $statustime = $event["statustime"];
            $edate = new DateTime("@$statustime");
            $edate->setTimezone($timezone);
            $new_date = date_parse($value);
            $edate->setTime($new_date["hour"], $new_date["minute"]);
            $edate->setDate($new_date["year"], $new_date["month"], $new_date["day"]);
            $event["statustime"] = $edate->getTimeStamp();
            break;
        case "severity":
            $event["severity"] = $value;
            break;
        case "contact":
            $event["contact"] = $value;
            break;
        case "gcal":
            $event["gcal"] = $value;
            break;
        }
    }
    $event = Postmortem::save_event($event);
    if (is_null($event["id"])) {
        $app->response()->status(500);
        return;
    }

    $app->redirect('/events/'.$event["id"], 201);
});

$app->post('/events/:id/tags', function($id) use ($app) {
    header("Content-Type: application/json");
    $tags = $app->request()->post('tags');
    $tags = explode(",", $tags);
    $tags = array_map('trim', $tags);
    $tags = array_map('strtolower', $tags);
    $res = Postmortem::save_tags_for_event($id, $tags);
    if ($res["status"] == Postmortem::ERROR) {
        $app->response()->status(400);
    } else {
        $app->response()->status(201);
        $tags = Postmortem::get_tags_for_event($id);
        if ($tags["status"] == Postmortem::ERROR) {
            $app->response()->status(404);
            return;
        } else {
            $output = json_encode($tags["values"]);
            echo str_replace("\\/", "/", $output);
        }
    }
});
$app->delete('/events/:event_id/tags/:tag_id', function($event_id, $tag_id) use ($app) {
    header("Content-Type: application/json");
    $res = Postmortem::delete_tag($tag_id, $event_id);
    if ($res["status"] == Postmortem::ERROR) {
        $app->response()->status(500);
        echo json_encode($res["error"]);
    } else {
        $app->response()->status(204);
    }

});


$app->get('/ping', function () use ($app) {
    header("Content-Type: application/json");
    echo json_encode(array('status' => 'ok'));
});



$app->run();
