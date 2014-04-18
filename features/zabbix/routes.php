<?php

$app->get('/events/:id/zabbixhost/:name', function($id, $name) use ($app) {
    header("Content-Type: application/json");
    $zabbix = new Zabbix();
	$hosts = $zabbix->get_zabbix_hosts($name);
    if ($hosts == array()) {
        $app->response()->status(404);
        return;
    } else {
        echo json_encode($hosts);
    }
});

$app->get('/events/:id/zabbixtrigger/:idhost/:from/:to', function($id, $idhost, $from, $to) use ($app) {
    header("Content-Type: application/json");
	$triggers = array();
    $zabbix = new Zabbix();
	$triggers = $zabbix->get_zabbix_host_trigger($idhost, $from, $to);
	if ($triggers == array()) {
        $app->response()->status(404);
        return;
    } else {
		$zabbix->save_zabbix_triggers_for_event($id, $triggers);
        echo json_encode($triggers);
    }	
});

