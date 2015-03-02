var severity_levels = $.map($("#severity-select option"),function(val,i) {
    return (i+1) + ". " + $(val).attr('description');
});

$("#event-severity").popover({
    trigger:"none",
    html: true,
    title: $("#severity-select").attr("title"),
    content: severity_levels.join('<br/>')
});

$("#event-severity").click(function(event) {
    event.preventDefault();
    event.stopPropagation();
    $("#event-severity").popover('show');
});

// its supposed to handle this...
$(':not(#event-severity)').click(function(){
   $('#event-severity').popover('hide');
});
