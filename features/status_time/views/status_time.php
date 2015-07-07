<div class="row-fluid">
  <!-- Editable Controls -->
  <form class="form-horizontal">
  <div class="span6">
    <div class="control-group">
     <label class="control-label" id="event-status-time">Status time: </label>
     <div class="controls controls-row">
     <?php if ($status_datetime) { ?>
      <div class="hidden"><a href="#" id="add-status" role="button" class="btn editable_hidden">Add</a></div>
      <div id="event-status-container" class="">
          <input id="event-status-input-date" name="event-status-input-date"
             class="input-small datepicker editable" type="text"
             value="<?php echo $status_datetime->format('m/d/Y'); ?>" disabled>
          <input id="event-status-input-time" name="event-status-input-time"
             class="input-mini timeentry editable" type="text"
             value="<?php echo $status_datetime->format('g:iA'); ?>" disabled>
          <a href="#" id="clear-status" role="button" class="btn editable_hidden">Clear</a>
      </div>
     <?php } else { ?>
      <div class="editable_hidden" style="display:none;"><a href="#" id="add-status" role="button" class="btn">Add</a></div>
      <div id="event-status-container" class="hidden">
          <input id="event-status-input-date" name="event-status-input-date"
             class="input-small datepicker editable" type="text" value="" disabled>
          <input id="event-status-input-time" name="event-status-input-time"
             class="input-mini timeentry editable" type="text" value="" disabled >
          <a href="#" id="clear-status" role="button" class="btn editable_hidden">Clear</a>
      </div>

     <?php } ?>
	       </div>
      </div>
    </div>
  </form>
</div>
