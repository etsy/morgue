<div class="row-fluid">
  <form class="form-horizontal">

<div class="span6">
    <div class="control-group">
      <label class="control-label" id="event-start-time">Meeting: </label>
      <div class="controls controls-row">
         <input type="text" placeholder="Enter Google Calendar event URL" id="gcal" name="gcal" class="input-xxlarge" value=""  />
      </div>
    </div>
</div>
</form>
</div>

<!-- display it -->
<div class="row-fluid">
  <form class="form-horizontal">
  <div class="span6">
        <div class="controls controls-row" id="the_gcal">
            <?php 
                if (isset($gcal) && $gcal!="" ){
                    echo "<a id=\"gcal_anchor\" href=\"$gcal\" target=\"_new\">Google Calendar Event</a>";
                }
            ?>
        </div>
    </div>
</form>
</div>
<!-- end -->
<div class="row-fluid"><br/></div>
