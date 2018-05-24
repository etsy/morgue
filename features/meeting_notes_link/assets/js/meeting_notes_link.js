/**
 * get the raw markdown summary and display it in a textedit area instead of
 * the rendered HTML
 * param: optional text to append to the textedit area
 */
function make_meeting_notes_link_editable() {

    var $meeting_notes_link = $("#meeting_notes_link");
    var $meeting_notes_link_anchor = $("#meeting_notes_link_anchor");

    if($meeting_notes_link_anchor) {
        var meeting_notes_link = $meeting_notes_link.val();
        $meeting_notes_link_anchor.attr('href', meeting_notes_link);
        $meeting_notes_link_anchor.text(meeting_notes_link);
    }

    $meeting_notes_link.toggle();
    $("#meeting_notes_link_span").toggle();
}

function update_meeting_notes_link_for_event(e, event, history) {
    event.meeting_notes_link = $("input#meeting_notes_link").val();
}


$("#meeting_notes_link").on("edit", make_meeting_notes_link_editable);
$("#meeting_notes_link").on("save", make_meeting_notes_link_editable);
$("#meeting_notes_link").on("save", update_meeting_notes_link_for_event);
