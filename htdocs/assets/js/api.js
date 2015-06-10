/**
 * simple helper function to add HTTP DELETE ajax calls to the jQuery object
 */
$.ajax_delete = function(url, callback) {
  $.ajax({url: url, type: "DELETE", success: callback});
};

/**
 * simple helper to get the current event ID
 */
function get_current_event_id() {
  return window.location.pathname.split("/")[2];
}

/**
 * Show a success or failure message on the edit page after an ajax request
 * params: Display name of event that occurred, boolean for success (true if successful. Defaults to false)
 */
function show_save_status(event_type, success) {
    success = typeof success !== 'undefined' ? success : false;
    $saved = $("#saved_feedback");
    if (success) {
        $saved.html(event_type + " has been saved");
        $saved.removeClass("alert-error").addClass("alert-success"); 
    } else {
        $saved.html(event_type + " failed to save");
        $saved.removeClass("alert-success").addClass("alert-error"); 
    }
    $saved.animate({opacity: "100.0"}, 1000, "linear", function () {
            $saved.animate({opacity: "0"}, 1000, "linear"); 
    });
}

function update_title_for_event() {
  if (!$("#eventtitle").val()) {
      show_save_status("Title", false);
      return;
  }

  var url = "/events/" + get_current_event_id();
  $.ajax({
        url: url,
        data: { title: $("#eventtitle").val() },
        type: "PUT",
        success: function () { show_save_status("Title", true);},
        error: function () { show_save_status("Title", false);}
  });
}

/**
 * Delete the current event.
 */
function delete_event(callback) {
  $.ajax_delete('/events/' + get_current_event_id(), callback);
}


/**
 * function to store an image link for an event via the REST API
 *
 * @param id - event ID
 * @param image - the image link to store
 * @param callback - callback function
 *
 */
function store_image_for_event(id, image, callback) {
  var url = "/events/" + id + "/images";
  var data = {"images": image};
  $.post(url, data, callback);
}
/**
 * function to store a ticket id for an event via the REST API
 *
 * @param id - event ID
 * @param ticket - the ticket to store
 * @param callback - callback function
 *
 */
function store_ticket_for_event(id, ticket, callback) {
  var url = "/events/" + id + "/tickets";
  var data = {"tickets": ticket};
  $.post(url, data, callback);
}
/**
 * function to store an IRC channel for an event via the REST API
 *
 * @param id - event ID
 * @param image - the channel name to store
 * @param callback - callback function
 *
 */
function store_channel_for_event(id, channel, callback) {
  var url = "/events/" + id + "/channels";
  var data = {"channels": channel};
  $.post(url, data, callback);
}

/**
 * function to store an forum link for an event via the REST API
 *
 * @param id - event ID
 * @param forum_url - the link to store
 * @param callback - callback function
 *
 */
function store_forum_url_for_event(id, forum_url, comment, callback) {
  var url = "/events/" + id + "/forum_links";
  var data = {
    "forum_link": forum_url,
    "forum_comment": comment
  };
  $.post(url, data, callback);
}

/**
 * function to store tags for an event via the REST API
 *
 * @param id - event ID
 * @param tag - the tag
 * @param callback - callback function
 *
 */
function store_tags_for_event(id, tag, callback) {
  var url = "/events/" + id + "/tags";
  var data = {"tags": tag};
  $.post(url, data, callback);
}

/**
 * function to delete a channel name for an event via the REST API
 *
 * @param id - event ID
 * @param channel_id - the channel id to delete
 * @param callback - callback function
 */
function delete_channel_for_event(event_id, channel_id, callback) {
  var url = "/events/" + event_id + "/channels/"+channel_id;
  $.ajax_delete(url, callback);
}
/**
 * function to delete an image for an event via the REST API
 *
 * @param id - event ID
 * @param image_id - the image id to delete
 * @param callback - callback function
 */
function delete_image_for_event(event_id, image_id, callback) {
  var url = "/events/" + event_id + "/images/"+image_id;
  $.ajax_delete(url, callback);
}
/**
 * function to delete a ticket for an event via the REST API
 *
 * @param id - event ID
 * @param channel_id - the ticket id to delete
 * @param callback - callback function
 */
function delete_tickets_for_event(event_id, ticket_id, callback) {
  var url = "/events/" + event_id + "/tickets/"+ticket_id;
  $.ajax_delete(url, callback);
}

/**
 * function to delete a tag for an event via the REST API
 *
 * @param id - event ID
 * @param tag_id - the tag id to delete
 * @param callback - callback function
 */
function delete_tag_for_event(event_id, tag_id, callback) {
  var url = "/events/" + event_id + "/tags/"+tag_id;
  $.ajax_delete(url, callback);
}

/**
 * function to delete a forum link for an event via the REST API
 *
 * @param id - event ID
 * @param forum_url - the forum url to delete
 * @param callback - callback function
 */
function delete_forum_url_for_event(event_id, forum_url_id, callback) {
  var url = "/events/" + event_id + "/forum_links/"+ forum_url_id;
  $.ajax_delete(url, callback);
}

function update_gcal_for_event() {
  var url = "/events/" + get_current_event_id();
  var gcal = $("#gcal").val();
  // some basic client side validation

  // if the user didn't enter anything don't even do anything 
  if ( gcal != null &&  gcal != ""){

    // make suer it's just the link in case they copied all the markup
    var re = new RegExp('https://[^ "]+');
    var gcal = re.exec(gcal); 

    // in the markup theres two linx, the first one is the one we want
    if( gcal != null && gcal.length > 0 ){
        gcal = gcal[0];

        $.ajax({
                url: url,
                data: { gcal: gcal },
                type: "PUT",
                success: function () { 
                    $("#gcal_anchor").remove();
                    show_save_status("Meeting", true);
                    var node = $("<a>Google Calendar Event</a>");
                    node.attr("href", gcal );
                    node.attr("id", "gcal_anchor");
                    node.attr("target","_new");
                    $("#the_gcal").append(node);
                    $("#gcal").val('');
                },
                error: function () { show_save_status("Meeting", false);}
        });
    } else {
            show_save_status("Invalid Google Calendar URL", false);
    }
  }
}

function update_contact_for_event() {
  var url = "/events/" + get_current_event_id();
  var contact = $("#contact").val();
  if ( contact != '') {
    $.ajax({
            url: url,
            data: { contact: contact },
            type: "PUT",
            success: function () { 
                show_save_status("Contact", true);
                update_contact_div(contact);
            },
            error: function () { show_save_status("Contact", false);}
    });
  }
}

function update_contact_div(contact) {
  $("#contact_anchor").remove();

  var node = $("<span></span>");
  node.attr("id", "contact_anchor");

  var lookup_url = $('input[name="contact_lookup_url"').val();
  if (lookup_url) {
    var href = lookup_url.replace("%s", contact);
    var link = $("<a>"+contact+"</a>");
    link.attr("href", href);
    link.attr("target","_new");
    node.append(link);
  } else {
    node.text = contact;
  }
  $("#the_contact").append(node);
  $("#contact").val('');
}

function update_severity_for_event() {
  var url = "/events/" + get_current_event_id();
  $("select#severity-select option:selected").each(function () {
    $.ajax({
        url: url,
        data: {severity: $.trim($(this).text())}, 
        type: "PUT",
        success: function () { show_save_status("Severity", true);},
        error: function () { show_save_status("Severity", false);}
    });
  });
}

function update_detectdate_for_event() {
  var url = "/events/" + get_current_event_id();
  $.ajax({
        url: url, 
        data: {
               detect_date: $("input#event-detect-input-date").val(),
               timezone: $('#current_tz').text()
               }, 
        type: "PUT",
        success: function () { show_save_status("Detect date", true);},
        error: function () { show_save_status("Detect date", false);}
  });
  update_undetected_time();
  update_resolve_time();
}
function update_detecttime_for_event() {
  var url = "/events/" + get_current_event_id();
  $.ajax({
        url: url, 
        data: {
               detect_time: $("input#event-detect-input-time").val(),
               timezone: $('#current_tz').text()
               }, 
        type: "PUT",
        success: function () { show_save_status("Detect time", true);},
        error: function () { show_save_status("Detect time", false);}
  });
  update_undetected_time();
  update_resolve_time();
}

function update_statusdatetime_for_event() {
  var url = "/events/" + get_current_event_id();
  $.ajax({
        url: url, 
        data: {
               status_datetime: $("input#event-status-input-date").val() + ' ' + $("input#event-status-input-time").val(),
               timezone: $('#current_tz').text()
               }, 
        type: "PUT",
        success: function () { show_save_status("Status time", true);},
        error: function () { show_save_status("Status time", false);}
  });
}

function update_enddate_for_event() {
  var url = "/events/" + get_current_event_id();
  $.ajax({
        url: url, 
        data: {
               end_date: $("input#event-end-input-date").val(),
               timezone: $('#current_tz').text()
               }, 
        type: "PUT",
        success: function () { show_save_status("End date", true);},
        error: function () { show_save_status("End date", false);}
  });
  update_impact_time();
  update_resolve_time();
}
function update_endtime_for_event() {
  var url = "/events/" + get_current_event_id();
  $.ajax({
        url: url, 
        data: {
               end_time: $("input#event-end-input-time").val(),
               timezone: $('#current_tz').text()
               }, 
        type: "PUT",
        success: function () { show_save_status("End time", true);},
        error: function () { show_save_status("End time", false);}
  });
  update_impact_time();
  update_resolve_time();
}

function update_startdate_for_event() {
  var url = "/events/" + get_current_event_id();
  $.ajax({
        url: url, 
        data: {
               start_date: $("input#event-start-input-date").val(),
               timezone: $('#current_tz').text()
               }, 
        type: "PUT",
        success: function () { show_save_status("Start date", true);},
        error: function () { show_save_status("Start date", false);}
  });
  update_impact_time();
  update_undetected_time();
}
function update_starttime_for_event() {
  var url = "/events/" + get_current_event_id();
  $.ajax({
        url: url, 
        data: {
               start_time: $("input#event-start-input-time").val(),
               timezone: $('#current_tz').text()
               }, 
        type: "PUT",
        success: function () { show_save_status("Start time", true);},
        error: function () { show_save_status("Start time", false);}
  });
  update_impact_time();
  update_undetected_time();
}

function update_impact_time() {
  var startdate = new Date($("input#event-start-input-date").val());
  var starttime = timeToDate($("input#event-start-input-time").val());
  var enddate = new Date($("input#event-end-input-date").val());
  var endtime = timeToDate($("input#event-end-input-time").val());

  startdate.setHours(starttime.getHours());
  startdate.setMinutes(starttime.getMinutes());
  enddate.setHours(endtime.getHours());
  enddate.setMinutes(endtime.getMinutes());

  $('#impacttime').val(getTimeString(enddate - startdate));
}
function update_undetected_time() {
  var startdate = new Date($("input#event-start-input-date").val());
  var starttime = timeToDate($("input#event-start-input-time").val());
  var enddate = new Date($("input#event-detect-input-date").val());
  var endtime = timeToDate($("input#event-detect-input-time").val());

  startdate.setHours(starttime.getHours());
  startdate.setMinutes(starttime.getMinutes());
  enddate.setHours(endtime.getHours());
  enddate.setMinutes(endtime.getMinutes());

  $('#undetecttime').val(getTimeString(enddate - startdate));
}
function update_resolve_time() {
  var startdate = new Date($("input#event-detect-input-date").val());
  var starttime = timeToDate($("input#event-detect-input-time").val());
  var enddate = new Date($("input#event-end-input-date").val());
  var endtime = timeToDate($("input#event-end-input-time").val());

  startdate.setHours(starttime.getHours());
  startdate.setMinutes(starttime.getMinutes());
  enddate.setHours(endtime.getHours());
  enddate.setMinutes(endtime.getMinutes());

  $('#resolvetime').val(getTimeString(enddate - startdate));
}


/**
 * Displays a confirmation message inserted after the 'insert' point. Pass in the context that is
 * required to execute onConfirm and onDisappear.
 * onConfirm only occurs if 'yes' is tapped.
 * onDisappear occurs when the alert is removed from the screen.
*/

function confirm_delete(message, insert, context, onConfirm, onDisappear) {

  var alert = $("<div></div>");
  alert.addClass("alert");
  alert.addClass("alert-warning");

  var paragraph = $("<p></p>");
  paragraph.html(message);

  var yesButton = $("<button></button>");
  yesButton.attr("id", "alert_yes_button");
  yesButton.addClass("btn");
  yesButton.addClass("btn-warning");
  yesButton.html("Yes");

  var noButton = $("<button></button>");
  noButton.attr("id", "alert_no_button");
  noButton.addClass("btn");
  noButton.html("No");

  alert.append(paragraph);
  alert.append(yesButton);
  alert.append(" ");
  alert.append(noButton);

  alert.hide().insertAfter(insert);
  alert.fadeIn(200);

  alert.on("click", "#alert_yes_button", function(ev) {
    ev.preventDefault();
    onConfirm.call(context);
    alert.fadeOut(200, function() {
      alert.remove();
      onDisappear.call(context);
    });
  });

  alert.on("click", "#alert_no_button", function(ev) {
    ev.preventDefault();
    alert.fadeOut(200, function() {
      alert.remove();
      onDisappear.call(context);
    });
  });
}

function show_status_fields() {
    var $fields = $('#event-status-container');
    if($fields.is(':hidden')) {
        $fields.prev().addClass('hidden');
        $fields.removeClass('hidden');
        $('#event-status-input-date').val(
            $.datepicker.formatDate('mm/dd/yy', new Date()))
             .datepicker({format: 'mm/dd/yyyy'});
        $('#event-status-input-time').val(
            timeStringFromDate(new Date()))
            .timeEntry({spinnerImage: ''}).focus();
    }
    return false;
}

function remove_status_fields() {
    var $fields = $('#event-status-container');
    $fields.addClass('hidden').prev().removeClass('hidden');
    $fields.find('input').val('');

    // set the db value back to 0
    var url = "/events/" + get_current_event_id();
    $.ajax({
        url: url,
        data: {
            status_datetime: ''
        },
        type: "PUT",
        success: function () { show_save_status("Status date", true);},
        error: function () { show_save_status("Status date", false);}
    });

    return false;
}

/**
 * get the raw markdown summary and display it in a textedit area instead of
 * the rendered HTML
 * param: optional text to append to the textedit area
 */
function make_summary_editable(text) {
    var $summary = $("#summary");

    if (typeof text === "undefined") {
        text = '';
    } else {
        text = "\n"+text;
    }

    // if a textarea already, append to it
    if ($summary.is('textarea')) {
        $summary.val(function(index, value){
                return value + text;
            });

        // if not a textarea already, create one and replace the original div with it
    } else {
        $.getJSON(
                  "/events/"+get_current_event_id()+"/summary",
                  function(data) {
                      var textarea = $("<textarea></textarea>")
                          .attr({
                                  "id": "summary",
                                  "name": "summary",
                                  "class": "input-xxlarge",
                                  "rows": "10"
                              })
                          .val(data.summary + text);
                      $summary.replaceWith(textarea);
                  },
                  'json' // forces return to be json decoded
                  );
    }
}

function update_summary_for_event() {
    var url = "/events/" + get_current_event_id();
    $.ajax({url: url, data: {summary: $("#summary").val()}, type: "PUT"});
}

/**
 * Depending on the current state either show the editable summary form or
 * save the markdown summary and render as HTML
 */
function summary_edit_save_button() {
    console.log("HERE");
    var button = $("#summaryeditbutton");
    var in_edit = (button.html() == "Save");
    if (in_edit) {
        update_summary_for_event();
        var html = $("<div></div>");
        html.attr("id", "summary");
        html.attr("name", "summary");
        html.attr("class", "input-xxlarge");
        html.attr("rows", "10");
        html.html(markdown.toHTML($("#summary").val()));
        $("#summary").remove();
        $("#summarywrapper").append(html);
        button.html("Edit");
        $("#summarycancelbutton").hide();

    } else {
        make_summary_editable();
        button.html("Save");
        $("#summarycancelbutton").show();
    }
}

/**
 * just abort editing and display the stored data as rendered HTML
 */
function summary_cancel_button() {
    $.getJSON("/events/"+get_current_event_id()+"/summary", function(data) {
            var html = $("<div></div>");
            html.attr("id", "summary");
            html.attr("name", "summary");
            html.attr("class", "input-xxlarge");
            html.attr("rows", "10");
            html.html(markdown.toHTML(data.summary));
            $("#summary").remove();
            $("#summarywrapper").append(html);
            $("#summaryeditbutton").html("Edit");
            $("#summarycancelbutton").hide();
        });
}
