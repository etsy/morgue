var EDIT_UNLOCKED = 0;
var EDIT_LOCKED = 1;
var EDIT_CLOSED = 2;
// update the lock every 60 seconds
var EDIT_TIME = 10000;
var edit_lock;


$("#add-status").on("click", show_status_fields);
$("#clear-status").on("click", remove_status_fields);
$("#severity-select").on("save", update_severity_for_event);
$("#event-detect-input-date").on("save", update_detectdate_for_event);
$("#event-detect-input-time").on("save", update_detecttime_for_event);
$("#event-status-input-time").on("save", update_statusdatetime_for_event);
$("#event-end-input-date").on("save", update_enddate_for_event);
$("#event-end-input-time").on("save", update_endtime_for_event);
$("#event-start-input-date").on("save", update_startdate_for_event);
$("#event-start-input-time").on("save", update_starttime_for_event);
$("#eventtitle").on("save", update_title_for_event);
$("#gcal").on("save", update_gcal_for_event);
$("#contact").on("save", update_contact_for_event);

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

function update_lock() {
    $.getJSON("/events/"+ get_current_event_id() +"/lock", function(data) {
           return;
        });
}

function make_editable() {
    $.getJSON("/events/"+ get_current_event_id() +"/lock", function(data) {
            var edit_div = $("<div></div>");

            if(data.status === EDIT_UNLOCKED) {
                $(".editable").removeAttr("disabled");
                $(".editable_hidden").show();
                $(".editable").trigger("edit");

                edit_div.attr({
                            "id" : "edit_div",
                            "class" : "alert alert-success",
                            "role" : "alert"   
                            });
                edit_div.html("Save Changes");
                edit_lock = setInterval(update_lock, EDIT_TIME);
            } else if(data.status === EDIT_LOCKED) {
                edit_div.attr({
                            "id" : "edit_div",
                            "class" : "alert alert-danger",
                            "role" : "alert"   
                            });
                edit_div.html("<strong>"+data.modifier+"</strong> is currently editing this page.");               
                $('#edit_status').off('click');
                $('#edit_status').on('click', function() {location.reload();});
            } else {
                edit_div.attr({
                            "id" : "edit_div",
                            "class" : "alert alert-warning",
                            "role" : "alert"   
                            });
                edit_div.html("The edit period for this event has expired");
                $('#edit_status').off('click');
            }
            $("#edit_div").replaceWith(edit_div);
        });
}

function save_page() {
    clearInterval(edit_lock);
    event = {};
    hist = {};
    hist.action = 'edit';

    $(".editable").trigger("save", [event, hist]);

    $(".editable_hidden").hide();
    $("input.editable").prop("disabled", true);
    $("select.editable").prop("disabled", true);

    update_history(hist);
    update_event(event);

    var edit_div = $("<div></div>");
    edit_div.attr({
                "id" : "edit_div",
                "class" : "alert alert-info",
                "role" : "alert"   
        });
    edit_div.html("Click here to make changes");
    $("#edit_div").replaceWith(edit_div);
}

$("#edit_status").click(function(ev) {
        var in_edit = ($('#edit_div').html() == "Save Changes");

        if(in_edit) {
            save_page();
        } else {
            make_editable();
        }
});

var edit_window = $(window);
var edit_status = $('#edit_status');
var edit_top = edit_status.offset().top;

edit_window.scroll(function() {
        edit_status.toggleClass('sticky', edit_window.scrollTop() > edit_top);
});
