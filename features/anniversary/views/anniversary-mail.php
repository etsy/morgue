<?php
/**
 * Version of the anniversay page suitble to email
 */

print <<<CSS
    <base href="http://{$_SERVER['SERVER_NAME']}" />
    <style>
        .label.tag {
            margin-right: 20px;
            background-color: #999;
            padding:5px;
            color: white;
        }
        #tag_well {
            margin-top:10px;
        }
    </style>
CSS;

$count = count($pms);
if ($count) {
    if ($count === 1) {
        $outage = "an outage";
    } else {
        $outage =  $count . " outages";
    }
    $subject = "$human_readable_date is the anniversary of {$outage}.";
    echo "<h3>${subject}</h3>";
    foreach ($pms as $k => $v) {
        print Anniversary::render_outage($v);
    }
}
