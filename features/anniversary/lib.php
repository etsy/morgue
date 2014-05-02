<?php
/**
 * lib for Anniversary
 */

class Anniversary extends Persistence {

    public static function get_ids($in_date = null, $conn = null) {
        if (!$conn) {
            return;
        }
        if (!$in_date) {
            $in_date = "curdate()";
        } else {
            $in_date = "'" . $in_date . "'";
        }
        // This queries a View that this feature expects has been created
        // source anniversary/schemas/anniversary.sql to create the view
        $sql = "SELECT id FROM pm_data WHERE deleted = 0 AND thedate = date_format(" . $in_date. ", '%m-%d')";
        try {
            $ret = array();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($ret, $row);
            }
            return array("status" => self::OK, "error" => "", "values" => $ret);
        } catch(PDOException $e) {
            return array("status" => self::ERROR, "error" => $e->getMessage(),
                         "values" => array());
        }
    }
static function render_tags($tags) {
    $out = '';
    foreach ($tags as $tag) {
        $tag['title'] = ucfirst($tag['title']);
        $out .= "<span class='label tag' id='tag-" .$tag['id'] . "'>{$tag['title']}</span>&nbsp;";
    }
    return $out;
}

static function render_outage($pm) {
    if (empty($pm['title'])) {
        $pm['title'] = "untitled";
    }
    $nice_start = date("r", $pm['starttime']);
    $nice_end= date("r", $pm['endtime']);
    $nice_tags = self::render_tags($pm['tags']);

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


}
