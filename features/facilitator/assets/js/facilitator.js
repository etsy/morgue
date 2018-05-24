function update_facilitator_for_event(e, event, history) {
    event.facilitator = $("input#facilitator").val();
}

$("#facilitator").on("save", update_facilitator_for_event);
