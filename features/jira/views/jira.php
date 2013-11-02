<!-- Remediation -->
<?php
    $ticket_ids = Jira::get_jira_tickets_for_event($id);
    if ($ticket_ids['status'] == Jira::OK) {
        $ticket_ids = $ticket_ids['values'];
    } else {
        $ticket_ids = array();
    }
    $jira_client = new JiraClient($curl_client);
    $jira_tickets = Jira::merge_jira_tickets($ticket_ids);
    $jira_keys = array_keys($jira_tickets);
?>
<div class="row-fluid">
<legend>Remediation</legend> <div id="jira-link"><a href="<?php echo $jira_client->getJiraBaseUrl(); ?>/secure/CreateIssue!default.jspa" target="_new">Create New Issue</a></div>

  <input type="text" placeholder="Enter JIRA key(s), separated by commas (i.e. CORE-2024, OPS-1453)" id="jira_key_input" name="jira_key_input" class="input-xxlarge" onblur="addTicket()">

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Key</th>
        <th>Summary</th>
        <th>Assignee</th>
        <th>Due Date</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="jira_table_body">
      <?php
      foreach ($jira_tickets as $ticket_key => $ticket_attributes) {
        $style = "jira_" . str_replace(" ", "_", strtolower($ticket_attributes['status']));
        echo "<tr class=\"jira-row\">";
        echo "<td><a href=$ticket_attributes[ticket_url] class=\"$style\">$ticket_key</a></td>";
        echo "<td>$ticket_attributes[summary]</td>";
        echo "<td>$ticket_attributes[assignee]</td>";
        echo "<td>$ticket_attributes[due_date]</td>";
        echo "<td>$ticket_attributes[status]</td>";
        echo "<td><span id=\"jira-$ticket_attributes[id]\" class='close'>&times;</span></td>";
        echo "</tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<div class="row-fluid"><br/></div>
