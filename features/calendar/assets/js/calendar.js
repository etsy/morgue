var cal = {};

function generateEvent() {
    var event = {
        'summary' : $('#eventtitle').val(),
        'description' : 'PM for ' + window.location.href
    };
    var date = new Date();
    // Start off with the date a week in the future.
    // This is because the event gets created immediately.
    // We'd like you to have time to modify it before it spams everyones calendar.
    date.setDate(date.getDate()+7);
    event.start = {};
    event.start.dateTime = date.toISOString();
    event.start.timeZone = cal.timezone;
    event.id = cal.eventId;

    event.end = {};
    date.setHours(date.getHours()+1);
    event.end.dateTime = date.toISOString();
    event.end.timeZone = cal.timezone;

    event.attendees = [];
    for (var i = 0, len = cal.attendees.length; i < len; i++) {
        event.attendees.push({'email': cal.attendees[i]});
    }
    event.status = 'confirmed';

    return event;
}


function showEventLink(event)
{
    var link = $('#calendar-link');
    link.css('float', 'right');

    if (event) {
        link.text('A Post Mortem is scheduled!');
        link.addClass('eventLink');
    } else {
        link.text('Schedule a Post Mortem for this event!');
        if(cal.override_calendar_link) {
            $('#override_calendar_link_description').html(cal.override_calendar_link_description);
            $('#override_calendar_link_description').css('display', 'block');
        }
    }
}


function showFacilitator()
{
    if(cal.facilitatorFeature) {
        $('#facilitator-link').on('click', function() {
                $.get('/calendar/facilitators/request/' + get_current_event_id()).fail(
                    function(data) {
                        console.log(data);
                        alert("An error occured.");
                    }).done(
                    function(data) {
                        $('#facilitator-link').replaceWith("Requested!");
                    });
        });

        $.get("/calendar/facilitators/" + get_current_event_id(), 
            function(data) {
                  var d = JSON.parse(data);

                  if (d["facilitator"] === "") {
                      $('#facilitator').hide();
                      $('#facilitator-link').show();
                  } else {
                      var link = document.createElement('a');
                      link.setAttribute('href', 'mailto:' + d['facilitator_email']);
                      link.innerHTML = d["facilitator"];
                      $('#facilitator').html(link);
                  }
                  $('#facilitator-div').show();
                
            }).error(function(xhr, status, error) {
                    console.log(xhr);
                    console.log(status);
                    console.log(error);
            });
    }
}

/**
 * Embeds a Google Calendar at the either the current month,
 * or the month and year of the date given.
 * Expects a string with format "yyyy-MM-....."
 **/
function showCalendar(date) {
    var calendar = document.createElement('IFRAME');
    var src = cal.src + cal.id + '&ctz=' + cal.timeZone;

    if (date != null) {
        // create a month long range for calendar view based off event date
        date = date.split('-');
        var dateRange = '' + date[0] + date[1];
        dateRange = dateRange +'01%2F'+ dateRange + '28';
        src += '&dates=' + dateRange;
    }

    calendar.setAttribute('id', 'calendar-view');
    calendar.setAttribute('src', src);
    calendar.setAttribute('scrolling', 'no');
    calendar.setAttribute('frameborder', '0');

    if (cal.inEvent) {
        calendar.setAttribute('class', 'smallCalendar');
    } else {
        calendar.setAttribute('class', 'bigCalendar');
    }

    $('#calendar-div').append(calendar);
}


function showEvent() 
{
    $('#event-title').text(cal.event.summary);

    var start = new Date(cal.event.start.dateTime);
    var end = new Date(cal.event.end.dateTime);
    
    $('#event-date').text(start.toLocaleDateString());

    start = start.toLocaleTimeString();
    end = end.toLocaleTimeString();

    $('#event-time').text(start +' to '+ end);//+ cal.event.end.dateTime);

    var location = cal.event.location;

    if (typeof location === 'undefined') {
        location = 'No location yet!';
    }
    $('#event-location').text(location);

    var creator = document.createElement('a');
    creator.setAttribute('href', 'mailto:'+cal.event.creator.email);
    creator.innerHTML = cal.event.creator.displayName;

    $('#event-creator').text('Created by ');
    $('#event-creator').append(creator);

    var link = document.createElement('a');
    link.setAttribute('href', cal.event.htmlLink);
    link.innerHTML = 'Edit';

    $('#event-link').append(link);
    $('#event-div').show();
}

/**
 * Check if current user has authorized this application.
 * 
 * @param boolean inEvent - true if the calendar is being loaded on an event page. 
 */
function handleClientLoad(inEvent)
{
    cal.inEvent = inEvent;

    $("#calendar-link").click(function() {
        if(cal.override_calendar_link) {
            window.open(cal.override_calendar_link_href);
        }else{
            calendarLinkHandler();
        }
    });

    gapi.client.setApiKey(cal.apiKey);
    window.setTimeout(checkAuth, 1);
}


function checkAuth() 
{
    gapi.auth.authorize({
            client_id: cal.clientId,
            scope: cal.scopes,
            immediate: true
            }, handleAuthResult);
}


/**
 * Handle response from authorization server.
 *
 * @param {Object} authResult Authorization result.
 */
function handleAuthResult(authResult) 
{
    if (authResult && !authResult.error) {
        cal.authorized = true;
        if (cal.inEvent) {
            checkEventExists();
        }
        else {
            $('#calendar-link').remove();
            showCalendar(null);
        }
    }
}


/**
 * Check if calendar event exists for morgue entry
 */
function checkEventExists() 
{
    gapi.client.load('calendar', 'v3', function() {
            var request = gapi.client.calendar.events.get({
                'calendarId': cal.id,
                'eventId': cal.eventId
            });
            request.execute(function(event) {
                if (event.hasOwnProperty('error')) {
                    cal.event = null;
                    showEventLink(false);
                    showCalendar(null);
                } else {
                    cal.event = event;
                    if (event.status == 'cancelled') {
                        showEventLink(false);
                        showCalendar(null);
                    } else {
                        cal.event = event;
                        showEventLink(true);
                        showCalendar(event.start.dateTime);
                        showEvent();
                        showFacilitator();
                    }
                }
            });
    });
}


function createEvent()
{
    var event = generateEvent();

    var is_confirmed = confirm("About to create an event and invite all postmortem email subscribers. Proceed?");

    if (!is_confirmed) {
	return;
    }

    var params = {
        'calendarId' : cal.id,
        'resource' : event,
        'sendNotifications': false
    };
    var request;
    if (cal.event == null) {
        request = gapi.client.calendar.events.insert(params);
    } else {
        params['eventId'] = event.id;
        request = gapi.client.calendar.events.update(params);
    }

    gapi.client.load('calendar', 'v3', function() {
        request.execute(function(event) {
                    if (event.hasOwnProperty('error')) {
                        console.log(event);
                    } else {
                        window.open(event.htmlLink, '_blank');
                        reloadOnReturn();
                    }
            });
        });
}

function reloadOnReturn() {
    var isVisible = (function(){
        var stateKey, eventKey, keys = {
            hidden: "visibilitychange",
            webkitHidden: "webkitvisibilitychange",
            mozHidden: "mozvisibilitychange",
            msHidden: "msvisibilitychange"
        };
        for (stateKey in keys) {
            if (stateKey in document) {
                eventKey = keys[stateKey];
                break;
            }
        }
        return function(c) {
            if (c) document.addEventListener(eventKey, c);
                return !document[stateKey];
        }
    })();
    isVisible(function() {
        isVisible() ? location.reload() : null;
    });
}


function calendarLinkHandler()
{
    if (cal.authorized) {
        if(cal.event === null || cal.event.status == 'cancelled') {
            createEvent();
        } else {
            window.open(cal.event.htmlLink);
        }
    } else {
        gapi.auth.authorize({
            client_id: cal.clientId,
            scope: cal.scopes,
            immediate: false
            }, handleAuthResult);
    }
}


