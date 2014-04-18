<!-- Zabbix -->
<?php
	$zabbix_client = new Zabbix();
	$triggers = $zabbix_client->get_zabbix_triggers_for_event($id);
	$zabbix_status = array( 0 => 'OK', 1 => "Probleme", 2 => "Unknow");
?>
<div class="row-fluid">
<legend>Zabbix</legend> 

  <input type="text" placeholder="Enter zabbix server name (you need to be logged on Zabbix)" id="zabbix_key_input" name="zabbix_key_input" class="input-xxlarge" onkeyup="addGraph()">

  <div id="graphpreview"></div>
  
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Date</th>
		<th>Host</th>
        <th>Trigger</th>
		<th>Status</th>
      </tr>
    </thead>
    <tbody id="zabbix_triggers_table_body">
      <?php
     foreach ($triggers['values'] as $k => $v) {
        echo "<tr class=\"jira-row\">";
        echo "<td>".date('r', $v['clock'])."</td>";
        echo "<td>".$v['host']."</td>";
        echo "<td>".$v['description']."</td>";
        echo "<td>".$zabbix_status[$v['status']]."</td>";
        echo "</tr>";
      }
      ?>
    </tbody>
  </table>
  
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Host (click to import trigggers)</th>
        <th>Graphs (click to copy/paste the graph)</th>
      </tr>
    </thead>
    <tbody id="zabbix_table_body">
    </tbody>
  </table>
 
</div>

<div class="row-fluid"><br/></div>
