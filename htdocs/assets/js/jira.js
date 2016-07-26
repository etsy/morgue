function createTicket() {
    var jira_project_name = $("#jira_project_name").attr("value");
    var jira_summary = $("#jira_summary").attr("value");
    var jira_description = $("#jira_description").attr("value");
    var jira_issuetype = $("#jira_issuetype").attr("value");

    var jira_pn = $("jira_project_name");
    create_ticket_for_event(get_current_event_id(), jira_project_name,
                            jira_summary, jira_description, jira_issuetype,
                            function(data) {
                                data = JSON.parse(data);
                                var ticket_num = data["key"];
                                store_ticket_for_event(get_current_event_id(), ticket_num, function(data) {
                                    data = JSON.parse(data);
                                    var keys = $.map(ticket_num.split(","), function(n,i){return ($.trim(n)).toUpperCase();});
                                    for (var i in data) {
                                        // add entries
                                        if ($.inArray(i, keys) !== -1) {
                                            var style = "jira_" +  data[i].status.toLowerCase().replace(" ", "_");

                                            var entry = "<tr class=\"jira-row\">";
                                            entry += "<td><a href=\""+data[i].ticket_url+"\" class=\""+ style + "\">"+i+"</a></td>";
                                            entry += "<td>"+data[i].summary+"</td>";
                                            entry += "<td>"+data[i].assignee+"</td>";
                                            $('th.jira_addition_field').each(function(index, value){
                                                field = $(value).text();
                                                entry += "<td>"+(data[i][field] || "" )+"</td>";
                                            });
                                            entry += "<td><span id=\"jira-"+data[i].id+"\" class='close'>&times;</span></td>";
                                            entry += "</tr>";

                                            $('#jira_table_body').append(entry);
                                            addTooltip($("tr[class=jira-row] a[class="+style+"]"));
                                        }
                                    }
                                });

                            }
                           )
}

function addTicket() {
    var jira_input = $("#jira_key_input");
    var jira_keys = (jira_input.attr("value"));
    if (jira_keys !== "") {
        store_ticket_for_event(get_current_event_id(), jira_keys, function(data) {
            data = JSON.parse(data);
            var keys = $.map(jira_keys.split(","), function(n,i){return ($.trim(n)).toUpperCase();});
            for (var i in data) {
                // add entries
                if ($.inArray(i, keys) !== -1) {
                    var style = "jira_" +  data[i].status.toLowerCase().replace(" ", "_");

                    var entry = "<tr class=\"jira-row\">";
                    entry += "<td><a href=\""+data[i].ticket_url+"\" class=\""+ style + "\">"+i+"</a></td>";
                    entry += "<td>"+data[i].summary+"</td>";
                    entry += "<td>"+data[i].assignee+"</td>";
                    $('th.jira_addition_field').each(function(index, value){
                        field = $(value).text();
                        entry += "<td>"+(data[i][field] || "" )+"</td>";
                    });
                    entry += "<td><span id=\"jira-"+data[i].id+"\" class='close'>&times;</span></td>";
                    entry += "</tr>";

                    $('#jira_table_body').append(entry);
                    addTooltip($("tr[class=jira-row] a[class="+style+"]"));
                }
            }
            jira_input.attr("value", "");
        });
    }
}

$("#jira_table_body").on('click', '.close', function() {
    $(this).fadeOut(100);
    var row = $(this).parents('.jira-row');
    var newRow = "<tr><td colspan=\"5\"><div id=\"jira_placeholder\"></div></td></tr>";

    newRow = $(newRow).insertAfter(row);
    var placeholder = newRow.find('#jira_placeholder');

    confirm_delete("Are you sure you want to delete this ticket?", placeholder, this, function() {
        var self = $(this);
        var id = $(this).attr("id").split("-")[1];
        delete_tickets_for_event(get_current_event_id(), id, function(data) {
            ($(self).parents('.jira-row')).remove();
        });
    }, function() {
        $(this).fadeIn(100);
        newRow.remove();
    });

});

function addTooltip(entry) {
    var statusType = entry.attr("class");
    //Turn "jira_in_progress" into "In Progress"
    statusType = statusType.replace("jira_", "").replace("_", " ").replace(/(^|\s)[a-z]/g, function(letter) { return letter.toUpperCase() });
    entry.tooltip({trigger:"hover", html: true, title:"Status: " + statusType, delay: {show: 500, hide: 100}});
}

$("tr[class=jira-row] a[class^=jira_]").each(function () {
    addTooltip($(this));
});
