var severity_levels = $.map($("#severity-select option"),function(val,i) {
    return (i+1) + ". " + $(val).attr('description');
});

$("#severity-select").popover({
    trigger:"none",
    html: true,
    title: $("#severity-select").attr("title"),
    content: severity_levels.join('<br/>')
});

$("#severity-select").hover(function(event) {
    event.preventDefault();
    event.stopPropagation();
    $("#severity-select").popover('show');
},
function(){
   $('#severity-select').popover('hide');
});
