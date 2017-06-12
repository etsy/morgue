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
        if (empty($tickets)) {
            return array();
        }

        $jira_tickets = array();

        if (is_null($curl)) {
            $curl = new CurlClient();
        }

        $tickets_ids = array();
        foreach($tickets as $k => $v) {
            array_push($tickets_ids, $v['ticket']);
        }

        $jira_client = new JiraClient($curl);
        $jira_info = $jira_client->getJiraTickets(array_values($tickets_ids));

        foreach ($tickets as $ticket) {
            $key = $ticket['ticket'];
            $id = $ticket['id'];
            if (isset($jira_info[$key])) {
                $ticket_info = $jira_info[$key];
                $ticket_info['id'] = $id;
                $jira_tickets[$key] = $ticket_info;
            }
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
        if (isset($config['proxy'])) {
            $this->proxy = $config['proxy'];
        } else {
            $this->proxy = null;
        }
        $this->additional_fields = array();
        if (isset($config['additional_fields'])) {
            $this->additional_fields = $config['additional_fields'];
        }
    }

    public function getJiraBaseUrl() {
        return $this->jira_base_url;
    }

    public function getAdditionalIssueFields() {
        return $this->additional_fields;
    }

    /**
     * Make a JQL query for all the issue keys passed as input and return the
     * JSON representation sent by the JIRA server
     *
     * @param $jira_key_input ->  an array of trimmed JIRA ticket keys (i.e. 'CORE-1204')
     * @param $field ->  an array of fields to retrieve for each issue
     *
     * @return $jira_api_response, an array of json-decoded issues as returned by JIRA
     */

    function getJiraApiResponse($jira_key_input, $fields) {
        $jira_api_responses = array();

        $tickets_count = count($jira_key_input);
        if ($tickets_count === 0) {
            return $jira_api_responses;
        }

        $params = array(
            'jql' => 'issuekey in ("' .  implode($jira_key_input, '","') . '")',
            'maxResults' => $tickets_count,
            'fields' => implode($fields, ',')
        );

        $response = $this->curl_client->get($this->getJiraBaseUrl() . '/rest/api/2/search' , $params, $this->username . ':' . $this->password, $this->proxy);
        $jira_api_response = json_decode($response, true);

        return $jira_api_response;

    }

    /**
     * given an array of JIRA issues keys, query JIRA for a set of
     * base field + addtional field, flatten each issue field and index
     * the return array by issue key
     *
     * @param $jira_key_input -> same parameter given for getJiraApiResponse, $api_response -> defaults to null
     *
     * @return $jira_tickets an array where key is an jira ticket key (i.e. 'CORE-1204') and val is an array of jira ticket attributes, such as 'ticket_url' => 'https://jira.foo.com/CORE-1204'
     */

    function getJiraTickets($jira_key_input, $api_response = null) {
        $raw_issues = array();
        $jira_tickets = array();

        $fields = array(
            'key'      => 'key',
            'summary'  => 'summary',
            'assignee' => 'assignee',
            'status'   => 'status'
        );

        $fields = $fields + $this->getAdditionalIssueFields();

        if (is_null($api_response)) {
            $api_response = $this->getJiraApiResponse($jira_key_input, $fields);
        }

        if (isset($api_response['issues'])) {
            $raw_issues = $api_response['issues'];
        }

        foreach ($raw_issues as $issue) {
            if (isset($issue['key'])) {
                $key = $issue['key'];
                $jira_tickets[$key] = $this->unpackTicketInfo($issue, $fields);
            }
        }

        return $jira_tickets;
    }

    /**
     * Givent a json JIRA representation of an issue and a set of fields,
     * extract each field from the issue object and return the extracted
     * value as an associative array, along with the ticket_url
     *
     * @param $ticket_info ->  json JIRA representation of an issue
     * @param $field ->  an array of fields to retrieve for each issue
     *
     * @returns array of field->value for the issue
     */
    public function unpackTicketInfo($ticket_info, $fields) {
        $ticket = array();
        foreach($fields as $k => $v) {
            # set a default value
            $ticket[$k] = "";
            if (isset($ticket_info['fields'][$v])) {
                $val = $ticket_info['fields'][$v];
                if (is_string($val)) {
                    $ticket[$k] = $val;
                } elseif (is_array($val) && isset($val['name'])) {
                    $ticket[$k] = $val['name'];
                }
            }
        }
        if (isset($ticket_info['key'])) {
            $ticket['ticket_url'] = $this->getJiraBaseUrl() . '/browse/' . $ticket_info['key'];
        }

        return $ticket;
    }

    /**
     * Given project, summary, description and issuetype, this
     * function creates a jira ticket using that information.
     *
     * @param $project -> project name
     * @param $summary -> summary of the ticket to be created
     * @param $description -> description of the ticket to be created
     * @param $issuetype -> the type of issue
     *
     * @return $jira_api_response, an array of json-decoded issues as returned by JIRA
     */

    public function createJiraTicket($project, $summary, $description, $issuetype) {
        $params = array(
            'fields' => array(
                'project' => array(
                    'key' => $project
                ),
                'summary' => $summary,
                'description' => $description,
                'issuetype' => array(
                    'name' => $issuetype
                )
            )
        );
        $response = $this->curl_client->post($this->getJiraBaseUrl(). '/rest/api/2/issue', $params, $this->username . ':' . $this->password, $this->proxy);
        $jira_api_response = json_decode($response, true);

        return $jira_api_response;
    }
}
?>
