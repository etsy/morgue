<div class="row-fluid">
<div class="offset1 span10">

<?php if ($event['deleted']): ?>
    <div class="row-fluid">
        <div class="alert">
            <strong>Heads up!</strong> This postmortem was deleted.
        </div>
    </div>
<?php endif; ?>

<!-- Title -->
<div class="row-fluid">
    <input class="input-headline" id="eventtitle" type="text"
      value="<?php echo $event["title"] ?>" required>
</div>

<!-- Info Saved Notice -->
<div class="alert alert-success" style="opacity:0" id="saved_feedback">
Filler, to keep the same size
</div>

<!-- Small Print -->
<div class="row-fluid">
  <i class="muted"><small>All times are currently shown in <?php echo getUserTimezone() ?> time.</small></i>
</div>
<div class="row-fluid">
  <br/>
</div>

<!-- Time and Severity (in two columns) -->
<div class="row-fluid">
  <!-- Editable Controls -->
  <form class="form-horizontal">
  <div class="span6">
    <div class="control-group">
      <label class="control-label" id="event-start-time">Start time: </label>
      <div class="controls controls-row">
        <input id="event-start-input-date" name="event-start-input-date"
               class="input-small datepicker" type="text"
               value="<?php echo $start_datetime->format('m/d/Y'); ?>" >
        <input id="event-start-input-time" name="event-start-input-time"
                class="input-mini timeentry" type="text"
                value="<?php echo $start_datetime->format('g:iA'); ?>" >
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" id="event-end-time">End time: </label>
      <div class="controls controls-row">
        <input id="event-end-input-date" name="event-end-input-date"
               class="input-small datepicker" type="text"
               value="<?php echo $end_datetime->format('m/d/Y'); ?>" >
        <input id="event-end-input-time" name="event-end-input-time"
               class="input-mini timeentry" type="text"
               value="<?php echo $end_datetime->format('g:iA'); ?>" >
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" id="event-detect-time">Detect time: </label>
      <div class="controls controls-row">
        <input id="event-detect-input-date" name="event-detect-input-date"
               class="input-small datepicker" type="text"
               value="<?php echo $detect_datetime->format('m/d/Y'); ?>" >
        <input id="event-detect-input-time" name="event-detect-input-time"
               class="input-mini timeentry" type="text"
               value="<?php echo $detect_datetime->format('g:iA'); ?>" >
      </div>
    </div>

   <div class="control-group">
     <label class="control-label severity_levels" id="event-severity">Severity: </label>
       <div class="controls controls-row">
        <select id="severity-select" name="severity" class="input" title="
        <?php
           $config = Configuration::get_configuration();
           if (isset($config['severity']) && isset($config['severity']['tooltip_title'])) {
               echo $config['severity']['tooltip_title'];
            } else {
                echo "Severity Levels";
            }
        ?>
        ">

        <?php
        $severity_levels = Postmortem::get_severity_levels();
        foreach ($severity_levels as $level => $desc) {
            $sev_level = $level + 1;
            echo '<option value="' . $sev_level . '" description="' . $desc . '"';
            if ($sev_level == $severity) {
                echo 'selected="true"';
            }
            echo '>' . $sev_level . '. ' . $desc . '</option>';
        }
        ?>
        </select>
      </div>
    </div>
  </div>

  <!-- Calculated Controls -->
  <div class="span6">
    <div class="control-group">
      <label class="control-label"> Total impact time: </label>
      <div class="controls controls-row">
        <input class="input-medium" id="impacttime" type="text"
          value="<?php echo $impacttime; ?>"
          readonly=true/>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" id="time_undetected">Time undetected: </label>
      <div class="controls controls-row">
        <input class="input-medium" id="undetecttime" type="text"
          value="<?php echo $undetecttime; ?>"
          readonly=true/>
      </div>
   </div>

   <div class="control-group">
    <label class="control-label" id="resolve_time">Time to resolve: </label>
    <div class="controls controls-row">
      <input class="input-medium" id="resolvetime" type="text"
        value="<?php echo $resolvetime; ?>"
          readonly=true/>
    </div>
   </div>
  </div>
</form>
</div>

<?php
        $config = Configuration::get_configuration();
        $edit_page_features = $config['edit_page_features'];

        foreach ($edit_page_features as $feature_name) {
            $feature = Configuration::get_configuration($feature_name);

            if ($feature['enabled'] == "on") {
                $view_file = $feature['name'] . '/views/' . $feature['name'] . '.php';
                // Walk the include path looking for our view file.
                $view_path_exists = stream_resolve_include_path($view_file);
                if ($view_path_exists) {
                    include $view_file;
                } else {
                    $app->log->error('No views found for ' . $feature['name'] . ' feature');
                }
            }
        }
?>

<div class="row-fluid"><br/></div>

<!-- Delete -->
<div class="row-fluid">
  <?php if ($event['deleted']): ?>
    <legend>Restore</legend>
    <div id="undelete_button_container">
    <a class="btn btn-danger" href="/events/<?php echo $event['id'] ?>/undelete">Undelete this Postmortem</a>
    </div>
  <?php else: ?>
    <legend>Delete</legend>
    <div id="delete_button_container">
      <button class="btn btn-danger" id="delete-initial">Delete this Postmortem</button>
      <div id="delete_button_confirmation_container" style="display: none">
        Are you sure? <br/>
        <button class="btn" id="delete-no">No, please don't!</button>
        <button class="btn btn-danger" id="delete-yes">Yes, delete it forever</button>
       </div>
    </div>
  <?php endif; ?>
</div>

</div>
</div>



<script type="text/javascript" src="/assets/js/bootstrap.js"></script>
<script type="text/javascript" src="/assets/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/assets/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="/assets/js/jquery.timeentry.min.js"></script>
<script type="text/javascript" src="/assets/js/api.js"></script>
<script type="text/javascript" src="/assets/js/images.js"></script>
<script type="text/javascript" src="/assets/js/jira.js"></script>
<script type="text/javascript" src="/assets/js/tags.js"></script>
<script type="text/javascript" src="/assets/js/irc.js"></script>
<script type="text/javascript" src="/assets/js/markdown.js"></script>
<script type="text/javascript" src="/assets/js/timehelpers.js"></script>
<script type="text/javascript" src="/assets/js/bootstrap-tooltip.js"></script>
<script type="text/javascript" src="/assets/js/bootstrap-popover.js"></script>
<script type="text/javascript" src="/assets/js/severity_tooltip.js"></script>
<script type="text/javascript" src="/assets/js/forums.js"></script>
<script type="text/javascript" src="/assets/js/edit.js"></script>
<?php
    // Enumerate any custom javascript assets and make them accessible externally.
    $config = Configuration::get_configuration();
    $edit_page_features = $config['edit_page_features'];

	/*
		Build up the path to the appropriate route for this asset.
		The feature's routes.php should include a route that locates and serves 
		the static asset.

		The directory containing custom Morgue features should follow the 
		same structure as the core project, including an 'assets/js/' directory. 
		Doing this, the route declaration can call stream_resolve_include_path()
		to locate the asset via the include_path.
    */
    foreach ($edit_page_features as $feature_name) {
        $feature = Configuration::get_configuration($feature_name);

        if (isset($feature['custom_css_assets'])) {
            // If we are just configured "on" then default to
            // include a css file named after the feature
            if ($feature['custom_css_assets'] === "on") {
                $feature['custom_css_assets'] = array("{$feature_name}.css");
            }

            if (!is_array($feature['custom_css_assets'])) {
                $css_assets = array($feature['custom_css_assets']);
            } else {
                $css_assets = $feature['custom_css_assets'];
            }

            foreach ($css_assets as $css_file) {
                echo "<link rel=\"stylesheet\" href=\"/features/{$feature_name}/css/{$css_file}\" />";
            }
        }

        if (isset($feature['custom_js_assets'])) {
            // If we are just configured "on" then default to
            // include a js file named after the feature
            if ($feature['custom_js_assets'] === "on") {
                $feature['custom_js_assets'] = array("{$feature_name}.js");
            }

            if (!is_array($feature['custom_js_assets'])) {
                $js_assets = array($feature['custom_js_assets']);
            } else {
                $js_assets = $feature['custom_js_assets'];
            }

            foreach ($js_assets as $js_file) {
                echo "<script type=\"text/javascript\" src=\"/features/{$feature_name}/js/{$js_file}\"></script>";
            }
        }
    }
?>
