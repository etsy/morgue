
function make_why_surprised_editable() {
    $.getJSON("/events/"+get_current_event_id()+"/why_surprised",
              function(data) {
                  var textarea = $("<textarea></textarea>")
                      .attr({
                              "id": "why_surprised",
                              "name": "why_surprised",
                              "class": "input-large",
                              "rows": "5"
                          })
                      .val(data.why_surprised);
                  $("#why_surprised").replaceWith(textarea);
              },
              'json'
              );
}


function update_why_surprised_for_event() {
  var url = "/events/" + get_current_event_id();
  $.ajax({
        url: url,
        data: {
               why_surprised: $("textarea#why_surprised").val(),
               }, 
        type: "PUT"
  });
}


function why_surprised_edit_save_button() {
    var button = $("#why_surprised_editbtn");
    var in_edit = (button.html() == "Save");
    if (in_edit) {
        update_why_surprised_for_event();
        var html = $("<div></div>")
            .attr({
                    "id": "why_surprised",
                    "name": "why_surprised",
                    "class": "input-large",
                    "rows" : "5"
                });
        html.html(markdown.toHTML($('#why_surprised').val()));
        $('#why_surprised').remove();
        $('#why_surprised_wrapper').append(html);
        button.html("Edit");
        $('#why_surprised_cancelbtn').hide();
    } else {
        make_why_surprised_editable();
        button.html("Save");
        $('#why_surprised_cancelbtn').show();
    }
}


function why_surprised_cancel_button() {
    $.getJSON("/events/"+get_current_event_id()+"/why_surprised",
              function(data) {
                  var html = $("<div></div>")
                      .attr({
                              "id": "summary",
                              "name": "summary",
                              "class": "input-xxlarge",
                              "rows": "10"
                            });
                  html.html(markdown.toHTML(data.why_surprised));
                  $("#why_surprised").remove();
                  $("#why_surprised_wrapper").append(html);
                  $("#why_surprised_editbtn").html("Edit");
                  $("#why_surprised_cancelbtn").hide();
        });
}    

$("#why_surprised_editbtn").on("click", why_surprised_edit_save_button);
$("#why_surprised_cancelbtn").on("click", why_surprised_cancel_button);

$.getJSON("/events/"+get_current_event_id()+"/why_surprised", function(data) {
        $("#why_surprised").html(markdown.toHTML(data.why_surprised));
});
