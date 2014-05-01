<?php
/**
 * Today in Post Mortem History
 */
function render_tags($tags) {
    $out = '';
    foreach ($tags as $tag) {
        $out .= "<span class='label tag' id='tag-" .$tag['id'] . "'>{$tag['title']}</span>";
    }
    return $out;
}
function render_outage($pm) {
    if (empty($pm['title'])) {
        $pm['title'] = "untitled";
    }
    $nice_start = date("r", $pm['starttime']);
    $nice_end= date("r", $pm['endtime']);
    $nice_tags = render_tags($pm['tags']);

    if (!$nice_tags) {
        $nice_tags = "This event has no tags";
    }
    $out = <<<HTML
    <div>
    <strong><a href="/events/{$pm['id']}">{$pm['title']}</a></strong><br />
    {$nice_start} to {$nice_end}  <br />
    Severity: {$pm['severity']} <br />

    <div id='tag_well' class='well well-small'>$nice_tags</div>

    </div>
    <hr />
HTML;
    return $out;
}

$count = count($pms);
if ($count) {
    if ($count === 1) {
        $outage = "an outage";
    } else {
        $outage =  $count . " outages";
    }
    echo "<h3>$human_readable_date is the anniversary of {$outage}.</h3>";
    foreach ($pms as $k => $v) {
        print render_outage($v);
    }
} else {
    echo "<p>Nothing broke on $human_readable_date.</p>";
}
?>
<script type="text/javascript" src="/assets/js/tags.js"></script><script type="text/javascript" src="/assets/js/tags.js"></script>
