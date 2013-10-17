$("#event-severity").popover({
    trigger:"none",
    html: true,
    title: "<a href='https://jira.etsycorp.com/confluence/display/auto/How+we+prioritize+bugs+at+Etsy' target='_new'>service tiers</a> and <a href='https://jira.etsycorp.com/confluence/display/SYS/Outage+Severity+Levels' target='_new'>severity levels</a>",
    content: "1. Complete outage or degradation so severe that core functionality is unusable <br>"
            +"2. Functional degradation for a subset of members or loss of some core functionality for all members <br>"
            +"3. Noticeable degradation or loss of minor functionality <br>"
            +"4. No member-visible impact; loss of redundancy or capacity <br> "
            +"5. Anything worth mentioning not in the above levels"
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
