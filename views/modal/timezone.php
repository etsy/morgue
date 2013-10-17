<div class="modal fade" id="tz" tabindex="-1" role="dialog" aria-labelledby="model_label" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 id="model_label">Change Timezone</h3>
  </div>
  <div class="modal-body">
    <form class="form-horizontal" method="post" action="/timezone">
      <div class="control-group">
        <label class="control-label" for="timezone">Timezone</label>
    </form>
        <div class="controls">
          <select id="change_timezone" name="timezone" class="input-large">
            <?php $timezones = DateTimeZone::listIdentifiers(); ?>
            <?php foreach ($timezones as $timezone) : ?>
              <option value="<?php echo $timezone ?>"><?php echo $timezone ?></option>
            <?php endforeach ?>
          </select>
        </div>
      </div>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-primary" type="submit" id="tzupdatebtn" >Update</button>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    $('#change_timezone').val('<?php echo getUserTimezone() ?>');
});
</script>
