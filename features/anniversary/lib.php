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
}
