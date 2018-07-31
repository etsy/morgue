<!-- Slack -->
<?php

$channels_for_event = Slack::get_slack_channels_for_event($id);
if ($channels_for_event['status'] == Slack::OK) {
    $channels_for_event = $channels_for_event['values'];
} else {
    $channels_for_event = array();
}

$curlClient = new CurlClient();
$slack = new Slack($curlClient);
$all_slack_channels = $slack->get_slack_channels_list();
?>
<div class="row-fluid">
  <legend for="timeline">Slack Channel(s)</legend>
  <div id="slack_select_div" class="editable_hidden" style="display:none;">
  <select id="slack_channels_select" name="slack_channels[]" data-placeholder="Select Slack channels" multiple="multiple" class="chzn-select input-xxlarge">
    <?php foreach ($all_slack_channels as $value => $display) : ?>
    <option value="<?php echo $value ?>"><?php echo $display ?></option>
    <?php endforeach; ?>
  </select>
  </div>

  <?php if (empty($all_slack_channels)) { ?>
  <div class="alert alert-block">
    <a class="close" data-dismiss="alert">×</a>
    <h4 class="alert-heading">Warning!</h4>
      No Slack channels configured.
  </div>
  <?php } ?>
  <table id="slackchannels" class="table table-striped">
    <thead>
        <tr>
            <th>Channel <span class="editable_hidden" style="display:none;">- <button id="pull-channel-conversations">Pull conversations from slack channels</button></span> </th>
        </tr>
    </thead>
    <tbody id="channel_table_body">
        <?php
        foreach ($channels_for_event as $channel) {
            echo "<tr class=\"channel-row\">";
            echo "<td><a role=\"button\" class=\"btn slackshow\" >".$channel['channel_name']."</a></td>";
            echo "<td><span id=\"channel-$channel[id]\" class=\"close editable_hidden\" style=\"display:none;\">&times;</span></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
      <tbody>
    <tr>
      <td id="channel_messages">
          <?php
          foreach ($channels_for_event as $channel) {
              echo $channel['message'];
          }
          ?>
      </td>
    </tr>
      </tbody>
  </table>
</div>
<input type="hidden" id="starttime" >
 <div id="slackmodal" class="modal hide fade bigModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="slack-modal-headline"></h3>
        <img src="/assets/img/ajax-loader.gif" id="slack-loader"></img>
    </div>
    <div id="slack-modal-body" class="modal-body"></div>
    <div class="modal-footer">
      <button class="btn slack_paste editable_hidden" data-dismiss="modal" aria-hidden="true" style="display:none;">Paste to What Happened</button>
      <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
  </div>


<div class="row-fluid"><br/></div>

