<?php

// load ZabbixApi
require 'zabbix/ZabbixApiAbstract.class.php';
require 'zabbix/ZabbixApi.class.php';

class Zabbix {

    /** constants mapped from Persistence class */
    const OK = Persistence::OK;
    const ERROR = Persistence::ERROR;

	function __construct($config = null) {
        $config = is_null($config) ? Configuration::get_configuration("zabbix") : $config;
        $this->zabbix_base_url = $config['baseurl'];
        $this->username = $config['username'];
        $this->password = $config['password'];
      }
	  
	public function get_zabbix_hosts($name="") {
		$hostsGraphs=array();
		$searchArray= array(
					'output' => 'extend',
					'selectGraphs' => 'extend'
					);
		if( $name != "" ) {
			$searchArray['search'] = array('name' => "$name");
			}		
		try {
			// connect to Zabbix API
			$api = new ZabbixApi($this->zabbix_base_url, $this->username, $this->password);

			$hosts = $api->hostGet($searchArray);
			
			foreach($hosts as $host) {
				$hostsGraphs[$host->hostid]=array();
				$hostsGraphs[$host->hostid]["name"]=$host->host;
				$hostsGraphs[$host->hostid]["graphs"]=$host->graphs;
			}	
		} catch(Exception $e) {
			// Exception in ZabbixApi catched
			echo $e->getMessage();
			return NULL;
		}
		return $hostsGraphs;
	}
	
	public function get_zabbix_host_trigger($idhost, $from, $to) {
		try {
			// connect to Zabbix API
			$api = new ZabbixApi($this->zabbix_base_url, $this->username, $this->password);
			$searchArray= array(
					'output' => 'extend',
					'selectTriggers' => 'extend',
					'selectHosts' => 'extend',
					'time_from' => $from,
					'time_till' => $to,
					'sortfield' => 'clock',
					'hostids' => $idhost
					);
			$triggers = $api->eventGet($searchArray);
			/*array(
			'output' => 'extend',
			'history' => 0,
			'itemids' => ["100100000041664"],
			"sortfield" => "clock",
			'time_from' => 1370090264,
			'time_till' => 1375361397
			/*'filter' => array('itemids' => ["100100000041664"])*/
		} catch(Exception $e) {
			// Exception in ZabbixApi catched
			echo $e->getMessage();
			return NULL;
		}
		return $triggers;
	
	}
	
	
	 /**
     * save zabbix triggers belonging to a certain event to the database
     *
     * @param $event_id - numeric ID of the event to store for
     * @param $triggers - array of triggers to store
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK ) on success
     * or ( "status" => self::ERROR, "error" => "an error message" ) on failure
     */
    static function save_zabbix_triggers_for_event($event_id, $triggers, $conn = null) {
        $conn = $conn ?: Persistence::get_database_object();
        $table_name = 'zabbixtriggers';
        $assoc_column = 'eventid';
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        try {
            foreach ($triggers as $value) {
                $select_sql = 'SELECT postmortem_id' .
                              ' FROM ' . $table_name .
                              ' WHERE postmortem_id = :postmortem_id AND ' . $assoc_column . ' = :value LIMIT 1';
                $stmt = $conn->prepare($select_sql);
                $stmt->execute(array('postmortem_id' => $event_id, 'value' => $value->eventid));
                $target_row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (empty($target_row)) {
                    $insert_sql = 'INSERT INTO ' . $table_name .
                        ' (postmortem_id, ' . $assoc_column . ', status, host, clock, description)' .
                        ' VALUES (:postmortem_id,:value,:status, :host, :clock, :description)';
                    $stmt = $conn->prepare($insert_sql);
                    $stmt->execute(array('postmortem_id' => $event_id, 'value' => $value->eventid, 'status' => $value->value, 
						'host' => $value->hosts[0]->host, 'clock' => $value->clock, 'description' => $value->triggers[0]->description));
					
                }
            }
        } catch(PDOException $e) {
            return array('status' => self::ERROR, 'error' => $e->getMessage());
        }
        return array( 'status' => self::OK );
    }
	
	 /**
     * get all zabbix triggers associated with an event. The ticket maps have the
     * keys "id" and "ticket"
     *
     * @param $event_id - the numeric event id
     * @param $conn - a PDO connection object
     *
     * @returns ( "status" => self::OK, "error" => "", "values" => array(triggers) ) on success
     * and ( "status" => self::ERROR, "error" => "message", "values" => array() ) on failure
     */
	static function get_zabbix_triggers_for_event($event_id, $conn = null) {
		$conn = $conn ?: Persistence::get_database_object();
        $table_name = 'zabbixtriggers';
        $assoc_column = 'eventid';
        if (is_null($conn)) {
            return array("status" => self::ERROR,
                "error" => "Couldn't get connection object.");
        }
        try {
                $select_sql = 'SELECT *' .
                              ' FROM ' . $table_name .
                              ' WHERE postmortem_id = :postmortem_id order by clock asc';
                $stmt = $conn->prepare($select_sql);
                $stmt->execute(array('postmortem_id' => $event_id));
				$ret = array();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					array_push($ret, $row);
				}	
                return array( 'status' => self::OK, "values" => $ret );
        } catch(PDOException $e) {
            return array('status' => self::ERROR, 'error' => $e->getMessage());
        }
        
    }

}


?>
