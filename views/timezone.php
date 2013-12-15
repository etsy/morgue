<div class="row-fluid">
  <a class="btn btn-large btn-block btn-primary" data-toggle="modal" href="#tz" data-target="#tz">
    <i class="icon-globe icon-white"></i> Change Timezone
  </a>
  <div style="text-align: center"><b>Current:</b> <span id="current_tz"><?php echo getUserTimezone() ?></span></div>
<?php include __DIR__.'/modal/timezone.php' ?>
