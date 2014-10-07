<div class="row-fluid">
	<div class="span12">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Title</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Severity</th>
          </tr>
        </thead>
      <?php

      $date_format = getDateformat();
      $time_format = getTimeformat();
      $datetime_format = $date_format . ' ' . $time_format;

      $tz = new DateTimeZone( getUserTimezone() );
      foreach ($events as $event) {
        $start = new DateTime("@".$event["starttime"]);
        $start->setTimezone($tz);
        $start = $start->format($datetime_format);
        $end = new DateTime("@".$event["endtime"]);
        $end->setTimezone($tz);
        $end = $end->format($datetime_format);
        echo "<tr>";
        echo "<td><a href=/events/$event[id]>$event[title]</a></td>";
        echo "<td>$start</td>";
        echo "<td>$end</td>";
        echo "<td>$event[severity]</td>";
        echo "</tr>";
      }
      ?>
      </table>
  </div>
</div>
