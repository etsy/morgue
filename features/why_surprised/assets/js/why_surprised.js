var old_surprised = null;

/**
 * get the raw markdown summary and display it in a textedit area instead of
 * the rendered HTML
 * param: optional text to append to the textedit area
 */
function make_why_surprised_editable() {
    var $summary = $("#why_surprised");

    // if a textarea already, append to it
    if ($summary.is('textarea')) {
        $summary.val(function(index, value){
                return value;
            });

        // if not a textarea already, create one and replace the original div with it
    } else {
        $.getJSON(
                  "/events/"+get_current_event_id()+"/why_surprised",
                  function(data) {
                      var textarea = $("<textarea></textarea>")
                          .attr({
                                  "id": "why_surprised",
                                  "name": "why_surprised",
                                  "class": "input-xxlarge editable",
                                  "rows": "10"
                              })
                          .val(data.why_surprised);
                      $summary.replaceWith(textarea);
                      $("#why_surprised").on("save", why_surprised_save);
                  },
                  'json' // forces return to be json decoded
                  );
    }
}


/**
 * Depending on the current state either show the editable summary form or
 * save the markdown summary and render as HTML
 */
function why_surprised_save(e, event, history) {
    var new_surprised = $("#why_surprised").val();

    var Diff = new diff_match_patch();
    var diff = Diff.diff_main(old_surprised, new_surprised);
    Diff.diff_cleanupSemantic(diff);
    diff = Diff.diff_prettyHtml(diff);
    history.why_surprised = diff;
    event.why_surprised = new_surprised;

    var html = $("<div></div>");
    html.attr("id", "why_surprised");
    html.attr("name", "why_surprised");
    html.attr("class", "input-xxlarge editable");
    html.attr("rows", "10");
    html.html(markdown.toHTML($("#why_surprised").val()));
    $("#why_surprised").remove();
    $("#why_surprised_wrapper").append(html);
    $("#why_surprised_undobutton").hide();
    $("#why_surprised").on("edit", make_why_surprised_editable);
}

/**
 * just abort editing and display the stored data as rendered HTML
 */
function why_surprised_undo_button() {
    $.getJSON("/events/"+get_current_event_id()+"/why_surprised", function(data) {
            $('#why_surprised').val(data.why_surprised);
        });
}

$("#why_surprised").on("edit", make_why_surprised_editable);
$("#why_surprised_undobutton").on("click", why_surprised_undo_button);
$.getJSON("/events/"+get_current_event_id()+"/why_surprised", function(data) {
        old_surprised = data.why_surprised;
    $("#why_surprised").html(markdown.toHTML(data.why_surprised));
});

