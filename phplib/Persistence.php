<?php

require_once "Configuration.php";

/**
 * implements the actual DB access methods
 */
class Persistence {

    /** return codes */
    const OK = 0;
    const ERROR = 1;

    /**
     * Save a postmortem to the database. If an id is given, the existing
     * postmortem is updated, if not a new one is created. The event will be
     * storted in the postmortems table and all properties given as arrays
     * are stored in the accompanying junction table.
     *
     * @param $postmortem - map of a postmortem with the following keys
     *                    - title => the title of the event
     *                    - summary => the summary of the post mortem
     *                    - starttime => start time as unix timestamp
     *                    - endtime   => end time as unix timestamp
     *                    - etsystatustime => etsystatus time as unix timestamp
     *                    - detecttime  => detect time as unix timestamp
     *                    - severity  => severity level
     *                    - channels => array of ircchannel names
     *                    - tickets => array of jira ticket numbers
     *                    - images => array of image URLs
     *                    - forums => array of forum URLs
     * @param $conn - PDO connection object, will be newly instantiated when
     *                null (default: null)
     *
     * @returns the event map including an "id" field on success and a map of the
     * form ( "id" => null, "error" => "an error message" ) on failure
     */
    static function save_event($postmortem, $conn = null) {
        $values = array("title", "summary", "starttime", "endtime",
                        "detecttime","severity");

        try {
            if (isset($postmortem["id"])) {
                array_push($values, "id");
                $sql = "UPDATE postmortems SET title=:title,summary=:summary,
                    starttime=:starttime,endtime=:endtime,
                    detecttime=:detecttime, severity=:severity";

                if( isset($postmortem['gcal']) ) {
                    $sql.= ",gcal=:gcal";
                    array_push($values,"gcal");
                }
                if ( isset($postmortem['contact']) ) {
                    $sql.= ",contact=:contact";
                    array_push($values,"contact");
                }
                if ( isset( $postmortem['etsystatustime"'] ) ){
                    $sql.= ",etsystatustime=:=etsystatustime";
                    array_push($values,"etsystatustime");
                }
                $sql.=" WHERE id=:id LIMIT 1";


            } else {
                array_push($values,"etsystatustime");

                $sql = "INSERT INTO postmortems (title,summary,starttime,endtime,
                    etsystatustime,detecttime,severity) VALUES (:title, :summary,:starttime,
                    :endtime,:etsystatustime,:detecttime,:severity)";
            }
            $stmt = $conn->prepare($sql);
            $stmt->execute(array_intersect_key($postmortem, array_flip($values)));
            if (!array_key_exists("id", $postmortem)) {
                $postmortem["id"] = $conn->lastInsertId();
            }
            return $postmortem;

        } catch(PDOException $e) {
            return array("id" => null, "error" => $e->getMessage());
        }
    }

    /**
     * Get a postmortem from the database
     *
     * @param $postmortem_id - id of the postmortem to get
     * @param $conn          - PDO connection object, will be newly instantiated when
     *                         null (default: null)
     *
     * @returns a postmortem map including an "id" field on success or a map of the
     * form ( "id" => null, "error" => "an error message" ) on failure
     */
    static function get_postmortem($postmortem_id, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();

        try {
            $sql = "SELECT id,title,summary,starttime,endtime,etsystatustime,
                detecttime,severity, contact, gcal, deleted FROM postmortems WHERE id = :id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute(array('id' => $postmortem_id));
            $postmortem = $stmt->fetch(PDO::FETCH_ASSOC);
            return $postmortem;

        } catch(PDOException $e) {
            return array("id" => null, "error" => $e->getMessage());
        }
    }

    /**
     * Save a forum entry to the database.
     *
     * @param $forum - map of a forum with the following keys
     *               - postmortem_id => id of associated event
     *               - *optional* id => id of existing forum entry
     *               - link => link of the forum
     *               - comment => comment for the forum
     * @param $conn - PDO connection object, will be newly instantiated when
     *                null (default: null)
     *
     * @returns the postmortem map including an "id" field on success and a map of the
     * form ( "id" => null, "error" => "an error message" ) on failure
     */
    static function save_forum($forum, $conn = null) {
        $values = array("link", "comment", "event_id");

        try {
            if (array_key_exists("id", $forum)) {
                array_push($values, "id");
                $sql = "UPDATE forum_links SET forum_link=:forum_link, comment=:comment
                    WHERE id=:id LIMIT 1;";
            } else {
                $sql = "INSERT INTO forum_links (postmortem_id, forum_link, comment)
                        VALUES (:event_id, :link, :comment);";
            }
            $stmt = $conn->prepare($sql);
            $stmt->execute(array_intersect_key($forum, array_flip($values)));
            if (!array_key_exists("id", $forum)) {
                $forum["id"] = $conn->lastInsertId();
            }
            return $forum;

        } catch(PDOException $e) {
            return array("id" => null, "error" => $e->getMessage());
        }
    }

    /**
     * helper function to get an array of values from a junction table. This
     * function is very opinionated about input format. It expects a SQL
     * statement to get data from the respective table and the corresponding
     * params. If the $multiple flag is set, rows are returned as an
     * associative array instead of only with a single value.
     *
     * @param $columns    - array of the columns you want data from
     * @param $table_name - name of table to get data from
     * @param $conn       - PDO connection object
     *
     * @returns ( "status" => self::OK, "error" => "", "values" => array(values) ) on success
     * and ( "status" => self::ERROR, "error" => "message", "values" => array() ) on failure
     */
    static function get_array($columns, $where = array(), $table_name, $conn = null) {
        if (!$where) {
            $where = array();
        }

        if (!isset($where['deleted'])) {
            $where['deleted'] = 0;
        }

        if ($table_name == 'tags' && $columns == array('id', 'title')) {
            if (isset($where['postmortem_id'])) {
                $get_sql = 'SELECT id, title FROM tags
                            INNER JOIN (postmortem_referenced_tags pmt) on
                            (pmt.tag_id = tags.id)
                            WHERE postmortem_id=:postmortem_id
                                AND pmt.deleted=:deleted';
            } else {
                $get_sql = 'SELECT id, title FROM tags WHERE deleted = :deleted';
            }
        } elseif ($table_name == 'tags') {
            if (!isset($where['tag_ids'])) {
                trigger_error('Call to get_array() for "tags" table was missing tag_ids', E_USER_ERROR);
            }

            $tags = array();
            foreach ($where['tag_ids'] as $tag_id) {
                $tags[] = intval($tag_id);
            }
            $tags = implode(', ', $tags);
            unset($where['tag_ids']);

            $get_sql = "SELECT DISTINCT id, title, starttime, endtime, severity
                        FROM postmortems pm INNER JOIN (postmortem_referenced_tags pmt)
                        on (pmt.postmortem_id = pm.id)
                        WHERE tag_id IN ($tags) AND pmt.deleted = :deleted";
        } else {
            $get_sql = 'SELECT ' . implode(',', $columns) . ' FROM ' . $table_name;

            if ($where) {
                $placeholders = array();

                array_walk($where, function($value, $column) use (&$placeholders) {
                    $placeholders[] = sprintf('%1$s = :%1$s', $column);
                });

                $get_sql .= ' WHERE ' . implode(' AND ', $placeholders);
            }
        }
        try {
            $ret = array();
            $stmt = $conn->prepare($get_sql);
            $stmt->execute($where);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($ret, $row);
            }
            return array("status" => self::OK, "error" => "", "values" => $ret);
        } catch(PDOException $e) {
            return array("status" => self::ERROR, "error" => $e->getMessage(),
                         "values" => array());
        }
    }

    /**
     * helper function to store array values in a junction table. This function
     * is very opinionated about input format. It expects two SQL statements,
     * one for testing the existence of a row and the other one to insert the
     * row if it doesn't exist. The postmortem ID will be inserted in the :id
     * field and the array values into the :value field. So the passed in SQL
     * statements have to reflect that.
     *
     * @param $table_name    - name of table to store/update data
     * @param $assoc_column  - column in remote table
     * @param $values        - array of values to insert
     * @param $postmortem_id - ID of the event to insert for
     * @param $conn          - PDO connection object
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function store_array($table_name, $assoc_column, $values, $postmortem_id, $conn) {
        try {
            foreach ($values as $value) {
                $select_sql = 'SELECT postmortem_id, id ' .
                              ' FROM ' . $table_name .
                              ' WHERE postmortem_id = :postmortem_id AND ' . $assoc_column . ' = :value LIMIT 1';
                $stmt = $conn->prepare($select_sql);
                $stmt->execute(array('postmortem_id' => $postmortem_id, 'value' => $value));
                $row_exists = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($row_exists === false) {
                    $insert_sql = 'INSERT INTO ' . $table_name .
                        ' (postmortem_id, ' . $assoc_column . ')' .
                        ' VALUES (:postmortem_id,:value)';
                    $stmt = $conn->prepare($insert_sql);
                    $stmt->execute(array('postmortem_id' => $postmortem_id, 'value' => $value));
                } elseif ($row_exists && $postmortem = self::get_postmortem($postmortem_id) && $postmortem['deleted'] == '1') {
                    $update_sql = 'UPDATE ' . $table_name . ' SET deleted=1 WHERE id=:row_id';
                    $stmt = $conn->prepare($update_sql);
                    $stmt->execute(array('row_id' => $row_exists['id'], 'value' => $value));
                }
            }
        } catch(PDOException $e) {
            return array('status' => self::ERROR, 'error' => $e->getMessage());
        }
        return array( 'status' => self::OK );
    }

    /**
     * function to get a single association by id (like images, channels,
     * tickets)
     *
     * @param $columns    - array of columns you want to get the values from
     * @param $table_name - table to get the data from
     * @param $pk         - the primary key to fetch for
     * @param $conn       - PDO connection object
     *
     * @returns ( "status" => self::OK, "value" => $row ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function get_association_by_id($columns, $table_name, $pk, $conn) {
        $sql = "SELECT " . (implode(',', $columns)) . " FROM " . $table_name;
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(array('id' => $pk));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return array("status" => self::ERROR, "error" => $e->getMessage());
        }
        return array( "status" => self::OK, "value" => $row );
    }

    /**
     * function to flag (by id) a single association as deleted
     *
     * @param $sql  - the sql to execute
     * @param $pk   - the primary key to delete for
     * @param $conn - PDO connection object
     *
     * @returns ( 'status' => self::OK) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function flag_as_deleted($table_name, $pk_column, $pk, $conn) {
        try {
            $sql = "UPDATE $table_name SET deleted=1 WHERE $pk_column = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(array('id' => $pk));
        } catch(PDOException $e) {
            return array("status" => self::ERROR, "error" => $e->getMessage());
        }
        return array( "status" => self::OK );
    }

    /**
     * function to flag (by id) a single association as UNdeleted
     *
     * @param $sql  - the sql to execute
     * @param $pk   - the primary key to delete for
     * @param $conn - PDO connection object
     *
     * @returns ( 'status' => self::OK) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function flag_as_undeleted($table_name, $pk_column, $pk, $conn) {
        try {
            $sql = "UPDATE $table_name SET deleted=0 WHERE $pk_column = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(array('id' => $pk));
        } catch(PDOException $e) {
            return array("status" => self::ERROR, "error" => $e->getMessage());
        }
        return array( "status" => self::OK );
    }

    /**
     * Get database object to operate on. This reads in the config file depending
     * on the environment we are running in (determined by the env var
     * MORGUE_ENVIRONMENT).
     *
     * @returns a database PDO object or null on error
     */
    static function get_database_object() {
        $config = Configuration::get_configuration();
        $host = $config['database']['mysqlhost'];
        $port = $config['database']['mysqlport'];
        $adb  = $config['database']['database'];
        $user = $config['database']['username'];
        $pass = $config['database']['password'];
        try {
            $conn = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$adb, $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            return null;
        }
    }

}

