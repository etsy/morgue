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
         echo "cal.clientId='{$cal->clientId}'; cal.apiKey='{$cal->apiKey}'; cal.scopes='{$cal_scopes}'; cal.id='{$cal->id}'; cal.timeZone='{$timeZone}';";
         ?>
        
         cal.scopes = cal.scopes.split(",");
         cal.src = 'https://www.google.com/calendar/embed?src=';
         cal.authorized = false;
         cal.eventId = padNumber(get_current_event_id());
         handleClientLoad(true);
     }

</script>

<div class="row-fluid calendar-view">
     <legend>Post Mortem Calendar</legend>
     <div id="calendar-div">
         <a href="#calendar" id="calendar-link" name="calendar">Login to your Google Account to view/create Post Mortems!</a>
         <div id="event-div" style="display: none;">
             <br/>
             <p>
             <span id="event-title"></span><br/><br/>
             <span id="event-time"></span><br/>
             <span id="event-date"></span><br/><br/>
             <span id="event-location"></span><br/><br/>
             <span id="event-creator"></span><br/><br/>
             <span id="event-link"></span>
             </p>
         </div>
     </div>
</div>
<div class="row-fluid"><br/></div>