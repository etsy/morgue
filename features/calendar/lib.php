<?php

class Calendar extends Persistence{

    function __construct() {
        $config = Configuration::get_configuration("calendar");

        $this->clientId = $config['clientId'];
        $this->apiKey = $config['apiKey'];
        $this->scopes = $config['scopes'];
        $this->id = $config['id'];
        $this->facilitator = $config['facilitator'];
        if (isset($config['attendees_email'])) {
            if (!is_array($config['attendees_email'])) {
                $config['attendees_email'] = array($config['attendees_email']);
            }
            $this->attendees = $config['attendees_email'];
        } else {
            $this->attendees = [];
        }
    }

    public static function get_facilitator($id, $conn = null) {
        if (!$conn) {
            return null;
        }

        $sql = "SELECT facilitator, facilitator_email FROM postmortems WHERE id = " . $id;
        try {
            $ret = array();
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($ret, $row);
            }
            return array("status" => self::OK, "error" => "", "values" => $ret);
        } catch (PDOException $e) {
            return array("status" => self::ERROR, "error" => $e->getMessage(), "values" => array());
        }
    }

    public static function set_facilitator($id, $facilitator, $conn = null) {
        if (!$conn) {
            return null;
        }

        $sql = "UPDATE postmortems SET facilitator = '" . $facilitator['name'] . "', facilitator_email = '" . $facilitator['email'];
        $sql = $sql . "' WHERE id = " . $id;

        try {
            $stmt = $conn->prepare($sql);
            $success = $stmt->execute();
            return null;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

}

?>
