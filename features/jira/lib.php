<?php

class Jira {

    /** constants mapped from Persistence class */
    const OK = Persistence::OK;
    const ERROR = Persistence::ERROR;

    /**
     * get all jira tickets associated with an event. The ticket maps have the
     * keys "id" and "ticket"
     *
     * @param $event_id - the numeric event id
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK, "error" => "", "values" => array(tickets) ) on success
     * and ( "status" => self::ERROR, "error" => "message", "values" => array() ) on failure
     */
    static function get_jira_tickets_for_event($event_id, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $columns = array('id', 'ticket');
        $table = 'jira';
        $where = array(
            'postmortem_id' => $event_id,
            'deleted' => 0,
        );
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.",
                "values" => array());
        }
        return Persistence::get_array($columns, $where, $table, $conn);
    }

    /**
     * save tickets belonging to a certain event to the database
     *
     * @param $event_id - numeric ID of the event to store for
     * @param $tickets - array of ticket URLs to store
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function save_jira_tickets_for_event($event_id, $tickets, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $table_name = 'jira';
        $assoc_column = 'ticket';
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::store_array($table_name, $assoc_column, $tickets,
                                        $event_id, $conn);
    }

    /**
     * delete jira tickets belonging to a certain event from the database
     *
     * @param $event_id - numeric ID of the event to delete for
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function delete_jira_tickets_for_event($event_id, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::flag_as_deleted('jira', 'postmortem_id', $event_id, $conn);
    }

    /**
     * function to get a ticket from the association table
     *
     * @param $id - ID to get
     * @param $conn - PDO connection object (default: null)
     *
     * @returns ( "status" => self::OK, "value" => $row ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function get_ticket($theid, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $columns = array('id', 'ticket');
        $table_name = 'jira';
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::get_association_by_id($columns, $table_name, $theid, $conn);
    }

    /**
     * function to delete a ticket from the association table
     *
     * @param $id - ID to delete
     * @param $conn - PDO connection object (default: null)
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function delete_ticket($theid, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::flag_as_deleted('jira', 'id', $theid, $conn);
    }

    /**
     * function to UNdelete a ticket from the association table
     *
     * @param $id - ID to undelete
     * @param $conn - PDO connection object (default: null)
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function undelete_ticket($theid, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $table_name = 'tickets';
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::flag_as_undeleted($table_name, 'postmortem_id', $theid, $conn);
    }

    /**
    * function to merge JIRA ticket info from the database with info from the
    * JIRA API
    *
    * @param $tickets - array of JIRA ticket objects from the DB
    * @param $curl - curl client to use (default: null)
    *
    * @returns array of merged JIRA tickets
    */
    static function merge_jira_tickets($tickets, $curl = null) {
        $jira_tickets = array();
        if (is_null($curl)) {
            $curl = new CurlClient();
        }
        $jira_client = new JiraClient($curl);

        foreach ($tickets as $ticket) {
            $jira_info = $jira_client->getJiraTickets( array($ticket["ticket"]) );
            $ticket_keys = array_keys($jira_info);
            $new_ticket_id = $ticket_keys[0];
            $the_ticket = $jira_info[$new_ticket_id];
            $the_ticket["id"] = $ticket["id"];
            $jira_tickets[$new_ticket_id] = $the_ticket;
        }

        return $jira_tickets;

    }


}


class JiraClient {

    function __construct($curl_client, $config = null) {
        $this->curl_client = $curl_client;
        $config = is_null($config) ? Configuration::get_configuration("jira") : $config;
        $this->jira_base_url = $config['baseurl'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function getJiraBaseUrl() {
        return $this->jira_base_url;
    }

    /**
     * iterates through array of jira keys and requests the JIRA API response for each one, adding each response to the array $jira__api_responses
     *
     * @param $jira_key_input ->  an array of trimmed JIRA ticket keys (i.e. 'CORE-1204')
     *
     * @return $jira__api_responses, an indexed array of json-decoded JIRA response
     */

    function getJiraApiResponse($jira_key_input) {
        $jira_api_responses = array();
        foreach ($jira_key_input as $i => $jira_ticket) {
            $jira_response = json_decode($this->curl_client->get($this->jira_base_url.'/rest/api/2/issue/'.$jira_ticket, array(), $this->username.':'.$this->password), true);
            if (!empty($jira_response)) {
                array_push($jira_api_responses, $jira_response);
            }
        }
        return $jira_api_responses;
    }

    /**
     * creates an empty array called $jira_tickets
     * iterates through the array given as the parameter, creating an empty array called $jira_ticket_attributes
     * iterates through the values of array given as the parameter, checking that keys match table column values in view, and adding the values to $jira_tickets
     *
     * @param $jira_key_input -> same parameter given for getJiraApiResponse, $api_response -> defaults to null
     *
     * @return $jira_tickets an array where key is an jira ticket key (i.e. 'CORE-1204') and val is an array of jira ticket attributes, such as 'ticket_url' => 'https://jira.etsycorp.com/CORE-1204'
     */

    function getJiraTickets($jira_key_input, $api_response = null) {
        if (is_null($api_response)) {
            $api_response = $this->getJiraApiResponse($jira_key_input);
        }
        $jira_tickets = array();
        foreach ($api_response as $i => $jira_ticket) {
            $ticket_attributes = array();
            if (isset($jira_ticket['key'])) {
                $ticket_key = $jira_ticket['key'];
                $ticket_attributes['ticket_url'] = $this->jira_base_url.'/browse/'.$ticket_key;
                $ticket_attributes['summary'] = $jira_ticket['fields']['summary'];
                $ticket_attributes['assignee'] = $jira_ticket['fields']['assignee']['name'];
                $ticket_attributes['status'] = $jira_ticket['fields']['status']['name'];
                if(isset($jira_ticket['fields']['due_date'])) {
                  $ticket_attributes['due_date'] = $jira_ticket['fields']['due_date'];
                }else{
                  $ticket_attributes['due_date'] = "none";
                }
                $jira_tickets[$ticket_key] = $ticket_attributes;
            }
        }
        return $jira_tickets;
    }
}
?>


