<div class="row-fluid">
  <!-- Editable Controls -->
  <form class="form-horizontal">
  <div class="span6">
    <div class="control-group">
     <label class="control-label" id="event-status-time">Status time: </label>
     <div class="controls controls-row">
     <?php if ($status_datetime) { ?>
      <div class="hidden"><a href="#" id="add-status" role="button" class="btn">Add</a></div>
      <div id="event-status-container" class="">
          <input id="event-status-input-date" name="event-status-input-date"
             class="input-small datepicker" type="text"
             value="<?php echo $status_datetime->format('m/d/Y'); ?>" >
          <input id="event-status-input-time" name="event-status-input-time"
             class="input-mini timeentry" type="text"
             value="<?php echo $status_datetime->format('g:iA'); ?>" >
          <a href="#" id="clear-status" role="button" class="btn">Clear</a>
      </div>
     <?php } else { ?>
      <div class=""><a href="#" id="add-status" role="button" class="btn">Add</a></div>
      <div id="event-status-container" class="hidden">
          <input id="event-status-input-date" name="event-status-input-date"
             class="input-small datepicker" type="text" value="" >
          <input id="event-status-input-time" name="event-status-input-time"
             class="input-mini timeentry" type="text" value="" >
          <a href="#" id="clear-status" role="button" class="btn">Clear</a>
      </div>

     <?php } ?>
	       </div>
      </div>
    </div>
  </form>
</div>
