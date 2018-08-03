<?php

include 'slack/lib/message_formatter.php';
include 'slack/lib/date_time_util.php';

class Slack {

    /** constants mapped from Persistence class */
    const OK = Persistence::OK;
    const ERROR = Persistence::ERROR;
    const MAX_FETCH_LIMIT = 20;

    private static $user_list;

    private $oAuthToken;
    private $curlClient;


    function __construct(CurlClient $curlClient) {
        $this->curlClient = $curlClient;

        $slack_config = Configuration::get_configuration("slack");
        $this->oAuthToken = array_key_exists('oAuth_token', $slack_config) ? $slack_config['oAuth_token']: '';
    }

    /**
     * get a list of the Slack Users
     *
     * @returns array of Slack_ user list
     */
    function get_user_list() {

        // Load user list once for a request (process)
        if(isset(self::$user_list)) {
            return self::$user_list;
        }

        $apiResult = $this->curlClient->get('https://etsy.slack.com/api/users.list', array('token' => $this->oAuthToken));
        $userListData = json_decode($apiResult);

        $userList = [];
        if($userListData->ok != true ) {
            return $userList;
        }

        foreach ($userListData->members as $userInfo) {
            $id = $userInfo->id;
            $userList[$id] = $userInfo;
        }
        self::$user_list=$userList;
        return self::$user_list;
    }


    /**
     * get a list of the Slack channels that can be selected for a given postmortem
     * if the 'morgue_get_slack_channels_list' exists, call it and return
     * its results - otherwise, lookup the config file for ['Slack_']['channels']
     *
     * @returns array of Slack_ channels strings
     */
    function get_slack_channels_list() {
        if (function_exists("morgue_get_slack_channels_list")) {
            return morgue_get_slack_channels_list();
        } else {
            $queryParam = array(
                'token'             => $this->oAuthToken,
                'exclude_archived'  => 1,
                'exclude_members'   => 1
            );

            $result = $this->curlClient->get('https://slack.com/api/channels.list', $queryParam);

            // If the API is JSON, use json_decode.
            $data = json_decode($result);
            $channelList = [];
            if($data->ok == true ) {
                foreach ($data->channels as $channelInfo) {
                    $channelId  = $channelInfo->id;
                    $channelName= $channelInfo->name;
                    $channelList[$channelId] = $channelName;
                }
            }
            return $channelList;
        }
    }

    /**
     * @param $starttime
     * @param $endtime
     * @param $channel_id
     * @return string
     */
    function get_channel_messages_for_datetime_range($starttime, $endtime, $channel_id) {

        $startTimeInUtcFormat = DateTimeUtil::convertDateTimeToUtcTimezoneTimestamp($starttime);
        $endTimeInUtcFormat   = DateTimeUtil::convertDateTimeToUtcTimezoneTimestamp($endtime);

        $queryParam = array(
            'token'    => $this->oAuthToken,
            'channel'  => $channel_id,
            'oldest'   => $startTimeInUtcFormat,
            'latest'   => $endTimeInUtcFormat,
            'inclusive'=> true,
            'limit'    => 100
        );


        $allMessages = [];

        // Fetch while we get all result
        $fetch = true;
        $fetchCounter = 1;
        while($fetch) {

            // If fetch counter limit exceds max fetch limit, terminate loop
            if($fetchCounter > self::MAX_FETCH_LIMIT) {
                $fetch = false;
            }
            $fetchCounter++;

            $apiResult = $this->curlClient->get('https://etsy.slack.com/api/conversations.history', $queryParam);
            $data = json_decode($apiResult);

            // Set fetch flag to false and continue. If unable to fetch records or has no more data to fetch
            if($data->ok != true) {
                $fetch = false;
                continue;
            }

            if ($data->has_more == true) {
                $queryParam['cursor'] = $data->response_metadata->next_cursor;
            }else{
                $fetch = false;
            }

            $sortedMessage = array_reverse($data->messages);
            array_push($allMessages , $sortedMessage);
        }

        $allMessages = array_reverse($allMessages);

        $slackMessageFormatter = new SlackMessageFormatter($this->get_user_list());

        $result = '';
        foreach ($allMessages  as $messages ) {

            foreach ($messages as $message) {

                // SKIP; If message is not user typed OR subtype is set to message
                if( (!isset($message->user))
                 || ($message->text == '')
                ) {
                    continue;
                }

                $messageDateTime = DateTimeUtil::convertTimestampToMorgueTimezoneDateTime($message->ts);

                $slackMessageFormatter->setMessage($message->text);
                $tmpMessage = $slackMessageFormatter->userName()->channelName()->teamName()->code()->anchorTag()->getMessage();

                $result.= $slackMessageFormatter->messageDiv($message->user, $tmpMessage, $messageDateTime);
            }

        }

        return $result;

    }


    /**
     * get all slack channels associated with an event. The single channel
     * maps have the keys "id" and "channel".
     *
     * @param $event_id - the numeric event id
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK, "error" => "", "values" => array(channels) ) on success
     * and ( "status" => self::ERROR, "error" => "message", "values" => array() ) on failure
     */
    static function get_slack_channels_for_event($event_id, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $columns    = array('id', 'channel_id', 'channel_name', 'message');
        $table_name = 'slack';
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
     * save images belonging to a certain event to the database
     *
     * @param $event_id - numeric ID of the event to store for
     * @param $images - array of image URLs to store
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function save_slack_channels_for_event($event_id, $channel_id, $channel_name, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }

        try {
            $select_sql = 'SELECT postmortem_id, id, deleted 
                            FROM slack
                            WHERE postmortem_id = :postmortem_id AND channel_id = :channel_id LIMIT 1';
            $stmt = $conn->prepare($select_sql);
            $stmt->execute(array('postmortem_id' => $event_id, 'channel_id' => $channel_id));
            $target_row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($target_row)) {
                $insert_sql = 'INSERT INTO slack (postmortem_id, channel_id, channel_name) VALUES (:postmortem_id,:channel_id,:channel_name)';
                $stmt = $conn->prepare($insert_sql);
                $stmt->execute(array('postmortem_id' => $event_id, 'channel_id' => $channel_id, 'channel_name' => $channel_name));
            } else {
                if ($target_row['deleted'] == '1') {
                    Persistence::flag_as_undeleted('slack', "id", $target_row['id'], $conn);
                }
            }
        } catch(PDOException $e) {
            return array('status' => self::ERROR, 'error' => $e->getMessage());
        }
        return array( 'status' => self::OK );

    }


    static function update_slack_channel_message($channel_id, $message, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }

        try {
            $update_sql = 'UPDATE slack SET message=:message WHERE channel_id=:channel_id ';
            $stmt = $conn->prepare($update_sql);
            $stmt->execute(array('channel_id' => $channel_id, 'message' => $message));
        } catch(PDOException $e) {
            return array('status' => self::ERROR, 'error' => $e->getMessage());
        }
        return array( 'status' => self::OK );

    }

    /**
     * delete slack channels belonging to a certain event from the database
     *
     * @param $event_id - numeric ID of the event to store for
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function delete_slack_channels_for_event($event_id, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::flag_as_deleted('slack', 'postmortem_id', $event_id, $conn);
    }

    /**
     * function to get an slack channel from the association table
     *
     * @param $id - ID to get
     * @param $conn - PDO connection object (default: null)
     *
     * @returns ( "status" => self::OK, "value" => $row ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function get_channel($theid, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $columns = array('id', 'channel');
        $table_name = 'slack';
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::get_association_by_id($columns, $table_name, $theid, $conn);
    }

    /**
     * function to delete a channel from the association table
     *
     * @param $id - ID to delete
     * @param $conn - PDO connection object (default: null)
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function delete_channel($theid, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::flag_as_deleted('slack', 'id', $theid, $conn);
    }

    /**
     * function to UNdelete a channel from the association table
     *
     * @param $id - ID to delete
     * @param $conn - PDO connection object (default: null)
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function undelete_channel($theid, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $table_name = 'slack';
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        return Persistence::flag_as_undeleted($table_name, 'postmortem_id', $theid, $conn);
    }

}

