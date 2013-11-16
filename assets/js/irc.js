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

function get_channel_data(url, params, channels, success_callback, error_callback) {
  $.get(url, params, function(data) {
    data = JSON.parse(data);
    if (data.length === 0) {
      success_callback(channels);
    } else {
      channels.push.apply(channels, data);
      // API fetches 20 at a time
      params.offset += 20;
      get_channel_data(url, params, channels, success_callback, error_callback);
    }
  })
  .fail(error_callback);
}

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

$('#ircchannels').on('click', 'a.ircshow', function() {
  var channel = ($(this)).html();
  var startdate = $("#event-start-input-date").attr("value");
  var enddate = $("#event-end-input-date").attr("value");
  var starttime = $("#event-start-input-time").attr("value");
  var endtime = $("#event-end-input-time").attr("value");
  var topic = channel + " - ";
  topic += " from " + startdate + " " + starttime;
  topic += " to " +enddate + " " + endtime;
  var url = "/ircsearch";
  var params = {
    start_date: startdate,
    start_time: starttime,
    end_date: enddate,
    end_time: endtime,
    channel: channel.replace("#",""),
    timezone: "America/New_York",
    offset: 0
  };
  $('#irc-modal-headline').html(topic);
  $('#irc-modal-body').empty();
  $('#irc-loader').show();
  $('#ircmodal').modal('toggle');

  get_channel_data(url, params, [], display_irc_channels_data, display_irc_not_implemented);
});

function display_irc_channels_data(channels) {
  var html="";
  for (var i in channels) {
    var el = channels[i];
    html += "<p>[" +el.time +"] &lt " +el.nick+"&gt: " +el.message+"</p>";
  }
  $('#irc-loader').hide();
  $('#irc-modal-body').html(html);
}

function display_irc_not_implemented(jqXHR, textStatus, errorThrown) {
  var html='<div class="alert alert-danger">';
  if (jqXHR.status == "404") {
      html += 'IRC history feature not implemented';
  } else {
      html += 'The server encountered an error while retrieving IRC logs';
  }
  html += '</div>';
  $('#irc-loader').hide();
  $('#irc-modal-body').html(html);
}

$("#ircmodal").on("click", ".irc_paste", function () {
    var channel_name = $("#irc-modal-headline").html();
    var content = "\n\n#### " + channel_name + "\n";
    content += $("#ircmodal").find("#irc-modal-body").text();
    // place on separate lines
    content = content.replace(/\[(\d)/g, "\n\n [$1");
    // create links
    content = content.replace(/(https?:\/\/\S*)/g, "<$1>");
    // highlight time
    content = content.replace(/(\[\d{2}:\d{2}:\d{2} [A|P]M\])/g, "*$1*");
    // highlight speaker name
    content = content.replace(/(< \S*>)/g, "**$1**");
    make_summary_editable(content);
    $("#summaryeditbutton").html("Save");
    $("#summarycancelbutton").show();
});
