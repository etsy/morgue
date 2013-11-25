<!-- IRC -->
<?php
 $channels = Irc::get_irc_channels_for_event($id);
 if ($channels['status'] == Irc::OK) {
     $channels = $channels['values'];
 } else {
     $channels = array();
 }
 $irc_channels = Irc::get_irc_channels_list();
?>
<div class="row-fluid">
  <legend for="timeline">IRC Channel(s)</legend>
  <select id="irc_channels_select" name="irc_channels[]" data-placeholder="Select IRC channels" multiple="multiple" class="chzn-select input-xxlarge">
    <?php foreach ($irc_channels as $value => $display) : ?>
    <option value="<?php echo $value ?>"><?php echo $display ?></option>
    <?php endforeach; ?>
  </select>

  <?php if (empty($irc_channels)) { ?>
  <div class="alert alert-block">
    <a class="close" data-dismiss="alert">×</a>
    <h4 class="alert-heading">Warning!</h4>
      No irc channels configured.
  </div>
  <?php } ?>
  <table id="ircchannels" class="table table-striped">
    <thead>
      <tr>
        <th>Channel</th>
      </tr>
    </thead>
    <tbody id="channel_table_body">
      <?php
        foreach ($channels as $channel) {
            echo "<tr class=\"channel-row\">";
            echo "<td><a role=\"button\" class=\"btn\" >$channel[channel]</a></td>";
            echo "<td><span id=\"channel-$channel[id]\" class=\"close\">&times;</span></td>";
            echo "</tr>";
        }
      ?>
    </tbody>
  </table>
</div>

<div class="row-fluid"><br/></div>

