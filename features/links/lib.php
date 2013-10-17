<?php

class Links {

    /** constants mapped from Persistence class */
    const OK = Persistence::OK;
    const ERROR = Persistence::ERROR;

    /**
     * get all forum links associated with an event. The single url maps have
     * the keys "id" and "forum_link"
     *
     * @param $event_id - the numeric event id
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK, "error" => "", "values" => array(links) ) on success
     * and ( "status" => self::ERROR, "error" => "message", "values" => array() ) on failure
     */
    static function get_forum_links_for_event($event_id, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $columns = array('id', 'forum_link', 'comment');
        $table_name = 'forum_links';
        $where = array(
            'postmortem_id' => $event_id,
            'deleted' => 0,
        );
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.",
                "values" => array());
        }
        return Persistence::get_array($columns, $where, $table_name, $conn);
    }


    /**
     * save forum links belonging to a certain event to the database
     *
     * @param $event_id - numeric ID of the event to store for
     * @param $- a map of the forum info with the following keys
     *            -- event_id => postmortem event this belongs to
     *            -- *optional* id => existing forum entry id
     *            -- link => the link to save
     *            -- comment => text describing the linked forum post's contents
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function save_forum_links($forum_links, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        try {
          $forum = Persistence::save_forum($forum_links, $conn);
        } catch (PDOException $e) {
          return array("status" => self::ERROR, "error" => $e->getMessage());
        }
        return array( "status" => self::OK);
    }

    /**
     * delete forum links belonging to a certain event to the database
     *
     * @param $event_id - numeric ID of the event to delete for
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function delete_forum_links_for_event($event_id, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::flag_as_deleted('forum_links', 'postmortem_id', $event_id, $conn);
    }

    /**
     * function to get a forum link from the association table
     *
     * @param $id - ID to get
     * @param $conn - PDO connection object (default: null)
     *
     * @returns ( "status" => self::OK, "value" => $row ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function get_forum_link($theid, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $columns = array('id', 'forum_link', 'comment');
        $table_name = 'forum_links';
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::get_association_by_id($columns, $table_name, $theid, $conn);
    }

    /**
     * function to delete a forum link from the association table
     *
     * @param $id - ID to delete
     * @param $conn - PDO connection object (default: null)
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function delete_forum_link($theid, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::flag_as_deleted('forum_links', 'id', $theid, $conn);
    }

    /**
     * UNdelete forum links belonging to a certain event to the database
     *
     * @param $event_id - numeric ID of the event to undelete
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function undelete_forum_links_for_event($event_id, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $table_name = 'forum_links';
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::flag_as_undeleted($table_name, 'postmortem_id', $event_id, $conn);
    }
}
