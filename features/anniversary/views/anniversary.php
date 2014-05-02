<?php
/**
 * Today in Post Mortem History
 */

$count = count($pms);
if ($count) {
    if ($count === 1) {
        $outage = "an outage";
    } else {
        $outage =  $count . " outages";
    }
    echo "<h3>$human_readable_date is the anniversary of {$outage}.</h3>";
    foreach ($pms as $k => $v) {
        print Anniversary::render_outage($v);
    }
} else {
    echo "<p>Nothing broke on $human_readable_date.</p>";
}
?>
<script type="text/javascript" src="/assets/js/tags.js"></script><script type="text/javascript" src="/assets/js/tags.js"></script>
