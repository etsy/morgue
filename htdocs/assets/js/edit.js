$("#add-status").on("click", show_status_fields);
$("#clear-status").on("click", remove_status_fields);
$("#severity-select").change(update_severity_for_event);
$("#event-detect-input-date").blur(update_detectdate_for_event);
$("#event-detect-input-time").blur(update_detecttime_for_event);
$("#event-status-input-time").blur(update_statusdatetime_for_event);
$("#event-end-input-date").blur(update_enddate_for_event);
$("#event-end-input-time").blur(update_endtime_for_event);
$("#event-start-input-date").blur(update_startdate_for_event);
$("#event-start-input-time").blur(update_starttime_for_event);
$("#eventtitle").blur(update_title_for_event);
$("#gcal").blur(update_gcal_for_event);
$("#contact").blur(update_contact_for_event);
$.getJSON("/events/"+get_current_event_id()+"/summary", function(data) {
    $("#summary").html(markdown.toHTML(data.summary));
});

$('.datepicker')
  .datepicker({
    format: 'mm/dd/yyyy'
  });

$('.timeentry')
  .timeEntry({
    spinnerImage: ''
  });

$('#delete-initial').click(function(ev) {
  ev.preventDefault();
  $(this).hide();
  $("#delete_button_confirmation_container").show();
});

$("#delete-yes").click(function(ev) {
  ev.preventDefault();
  delete_event(function(data, textStatus, jqXHR) {
    if (jqXHR.status == 204) {
      window.location = '/';
    }
  })
});

$("#delete-no").click(function(ev) {
  ev.preventDefault();
  $('#delete-initial').show();
  $("#delete_button_confirmation_container").hide();
});

// Enter key blurs input elements
$(":input").keyup(function(e) {
  if (e.keyCode == 13) {
      e.target.blur();
  }
});
