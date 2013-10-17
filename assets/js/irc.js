$('#ircchannels').on('click', 'span.close', function () {
  $(this).fadeOut(100);
  var row = $(this).parents('tr');
  var newRow = "<tr><td colspan=\"2\"><div id=\"irc_placeholder\"/></tr></tr>";

  newRow = $(newRow).insertAfter(row);
  var placeholder = newRow.find("#irc_placeholder");

  confirm_delete("Are you sure you want to delete this IRC channel?", placeholder, this, function() {
    var self = $(this);
    var id = $(this).attr("id").split("-")[1];
    delete_channel_for_event(get_current_event_id(), id, function(data) {
       ($(self).parents('.channel-row')).remove();
    });
  }, function() {
    $(this).fadeIn(100);
    newRow.remove();
  });

});

$("#irc_channels_select").chosen().change(function() {
  $("select#irc_channels_select option:selected").each(function () {
    var channel = $(this);
    var channelname = $(this).text();
    store_channel_for_event(get_current_event_id(), channelname, function(data) {
      data = JSON.parse(data);
      for (var i in data) {
        if (data[i].channel == channelname) {
          var entry = "<tr class=\"channel-row\">";
          entry += "<td><a role=\"button\" class=\"btn ircshow\" >"+data[i].channel+"</a></td>";
          entry += "<td><span id=\"channel-"+data[i].id+"\" class=\"close\">&times;</span></td>";
          entry += "</tr>";
          $('#channel_table_body').append(entry);
          channel.prop("selected", false);
          $("#irc_channels_select").trigger("liszt:updated");
        }
      }
    });

  });
});
