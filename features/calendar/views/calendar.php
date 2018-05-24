<!-- Calendar -->

<script type="text/javascript">
     var cal = {};

     // google calendar eventIds must >= 5 digits. Helper function to pad eventIds.
     function padNumber(n) { 
         if (n <= 99999) {
             n = ("0000"+n).slice(-5);
         }
         return n;
     }

     function loadCal() {
     
         <?php
         $cal = new Calendar;
         $timeZone = getUserTimezone();
         $cal_scopes = implode(",", $cal->scopes);
         echo "cal.clientId='{$cal->clientId}'; cal.apiKey='{$cal->apiKey}'; cal.scopes='{$cal_scopes}'; cal.id='{$cal->id}'; cal.timeZone='{$timeZone}'; cal.facilitatorFeature={$cal->facilitator};";
         echo "cal.attendees=[";
         foreach ($cal->attendees as $attendee_email) {
             echo "'{$attendee_email}',";
         }
         echo "];"
         ?>
        
         cal.scopes = cal.scopes.split(",");
         cal.src = 'https://www.google.com/calendar/embed?src=';
         cal.authorized = false;
         cal.eventId = padNumber(get_current_event_id());
         handleClientLoad(true);
     }

</script>

<div class="row-fluid calendar-view">
     <legend>Post Mortem</legend>
     <div id="calendar-div">
         <a name="calendar"></a>
         <a href="#calendar" id="calendar-link" >Login to your Google Account to view/create Post Mortems!</a>
         <div id="pm-slots-description" class="eventDiv" style="display:none;">
             To claim a slot, please
             <ul>
                 <li>Create an entry in go/morgue</li>
                 <li>Fill in the yellow columns on the sheet on a row that works for you</li>
                 <li>Reach out to the facilitator to share context and double-confirm availability</li>
                 <li>Send out a calendar invite to participants and postmortems@etsy.com (manually fill in the room in location because it is reserved in another event)</li>
         </div>
         <div id="event-div" class="eventDiv" style="display: none;">
             <h6 id="event-title"></h6>
             <p>
             <span id="event-time"></span><br/>
             <span id="event-date"></span><br/><br/>
             <span id="event-location"></span><br/><br/>
             <span id="event-creator"></span><br/><br/>
             <span id="event-link"></span>
             </p>
         </div>
         <div id="facilitator-div" class="eventDiv" style="display: none;">
             <h6> Facilitator </h6>
             <p>
             <span id="facilitator"></span>
             <a href="#calendar" id="facilitator-link" style="display: none;">Request one</a>
             </p>
         </div>
     </div>
</div>
<div class="row-fluid"><br/></div>
