
<?php if ($event['deleted']): ?>
    <div class="row-fluid">
        <div class="alert">
            <strong>Heads up!</strong> This postmortem was deleted.
        </div>
    </div>
<?php endif; ?>


<!-- Edit Status -->
<div class="row-fluid">
    <?php if ($edit_status === Postmortem::EDIT_UNLOCKED): ?>
        <a id="edit_status" href="javascript:void(0)"><div id="edit_div" class="alert alert-info" role="alert">Click here to make changes</div></a>
    <?php endif; ?>
    <?php if ($edit_status === Postmortem::EDIT_LOCKED): ?>
        <div id="edit_status" class="alert alert-danger" role="alert"><strong><?php echo $event["modifier"] ?></strong> is currently editing this page.</div>
    <?php endif; ?>
    <?php if ($edit_status === Postmortem::EDIT_CLOSED): ?>
        <div id="edit_status" class="alert alert-warning" role="alert"><strong>Heads up!</strong> The edit period for this event has expired.</div>
    <?php endif; ?>
</div>

<div class="row-fluid">
<div class="offset1 span10">

<!-- Title -->
<div class="row-fluid">
    <input class="input-headline editable" id="eventtitle" type="text"
      value="<?php echo $event["title"] ?>" required disabled>
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
               class="input-small datepicker editable" type="text"
               value="<?php echo $start_datetime->format('m/d/Y'); ?>" disabled>
        <input id="event-start-input-time" name="event-start-input-time"
                class="input-mini timeentry editable" type="text"
                value="<?php echo $start_datetime->format('g:iA'); ?>"disabled >
        <input type="hidden" id="start-date-time" name="start-date-time" value="<?php echo $starttime;?>" />
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" id="event-end-time">End time: </label>
      <div class="controls controls-row">
        <input id="event-end-input-date" name="event-end-input-date"
               class="input-small datepicker editable" type="text"
               value="<?php echo $end_datetime->format('m/d/Y'); ?>" disabled>
        <input id="event-end-input-time" name="event-end-input-time"
               class="input-mini timeentry editable" type="text"
               value="<?php echo $end_datetime->format('g:iA'); ?>" disabled>
        <input type="hidden" id="end-date-time" name="end-date-time" value="<?php echo $endtime;?>" />
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" id="event-detect-time">Detect time: </label>
      <div class="controls controls-row">
        <input id="event-detect-input-date" name="event-detect-input-date"
               class="input-small datepicker editable" type="text"
               value="<?php echo $detect_datetime->format('m/d/Y'); ?>" disabled>
        <input id="event-detect-input-time" name="event-detect-input-time"
               class="input-mini timeentry editable" type="text"
               value="<?php echo $detect_datetime->format('g:iA'); ?>" disabled>
      </div>
    </div>

   <div class="control-group">
     <label class="control-label severity_levels" id="event-severity">Severity: </label>
       <div class="controls controls-row">
        <select id="severity-select" name="severity" class="input editable" title="
        <?php
           $config = Configuration::get_configuration();
           if (isset($config['severity']) && isset($config['severity']['tooltip_title'])) {
               echo $config['severity']['tooltip_title'];
            } else {
                echo "Severity Levels";
            }
        ?>
        " disabled>

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
      <?php
      if (array_key_exists('problem_type', $config)) {
          $problemTypeConfig= $config['problem_type'];
          $problemTypeTitle = $problemTypeConfig['title'];
          $problemTypeList  = $problemTypeConfig['types'];
          ?>
          <div class="control-group">
              <label class="control-label" for="problem_type"><?php echo $problemTypeTitle;?></label>
              <div class="controls">
                  <select id="problem_type" name="problem_type" class="input-large editable" disabled>
                      <option value="">--SELECT--</option>
                      <?php
                      foreach ($problemTypeList as $problemType) {
                          $selected = ($problem_type == $problemType) ? " selected " : "";
                          ?>
                          <option value="<?php echo $problemType; ?>" <?php echo $selected;?>><?php echo $problemType; ?></option>
                          <?php
                      }
                      ?>
                  </select>
              </div>
          </div>
          <?php
      }
      ?>
      <div class="control-group">
          <label class="control-label" for="subsystem">Subsystem</label>
          <div class="controls">
              <input type="text" placeholder="Subsystem" id="subsystem" name="subsystem" value="<?php echo $subsystem;?>" class="input-xlarge editable" disabled>
          </div>
      </div>
      <div class="control-group">
          <label class="control-label" for="owner_team">Owner team</label>
          <div class="controls">
              <input type="text" placeholder="Owner Team" id="owner_team" name="owner_team" value="<?php echo $owner_team;?>" class="input-xlarge editable" disabled>
          </div>
      </div>
      <?php
      if (array_key_exists('impact_type', $config)) {
          $impactTypeConfig = $config['impact_type'];
          $impactTypeTitle  = $impactTypeConfig['title'];
          $impactTypeList   = $impactTypeConfig['types'];
          ?>
          <div class="control-group">
              <label class="control-label" for="impact_type"><?php echo $impactTypeTitle;?></label>
              <div class="controls">
                  <select id="impact_type" name="impact_type" class="input-large editable" disabled>
                      <option value="">--SELECT--</option>
                      <?php
                      foreach ($impactTypeList as $impactType) {
                          $selected = ($impact_type == $impactType) ? " selected " : "";
                          ?>
                          <option value="<?php echo $impactType; ?>" <?php echo $selected;?>><?php echo $impactType; ?></option>
                          <?php
                      }
                      ?>
                  </select>
              </div>
          </div>
          <?php
      }

      if (array_key_exists('incident_cause', $config)) {
          $incidentCauseConfig  = $config['incident_cause'];
          $incidentCauseTitle   = $incidentCauseConfig['title'];
          $incidentCauseList    = $incidentCauseConfig['causes'];
          ?>
          <div class="control-group">
              <label class="control-label" for="incident_cause"><?php echo $incidentCauseTitle;?></label>
              <div class="controls">
                  <select id="incident_cause" name="incident_cause" class="input-large editable" disabled>
                      <option value="">--SELECT--</option>
                      <?php
                      foreach ($incidentCauseList as $incidentCause) {
                          $selected = ($incident_cause == $incidentCause) ? " selected " : "";
                          ?>
                          <option value="<?php echo $incidentCause; ?>" <?php echo $selected;?>><?php echo $incidentCause; ?></option>
                          <?php
                      }
                      ?>
                  </select>
              </div>
          </div>
          <?php
      }
      ?>
  </div>

  <!-- Calculated Controls -->
  <div class="span6">
    <div class="control-group">
      <label class="control-label"> Total impact time: </label>
      <div class="controls controls-row">
        <input class="input-medium" id="impacttime" type="text"
          value="<?php echo $impacttime; ?>"
          disabled/>
      </div>
    </div>

    <div class="control-group">
      <label class="control-label" id="time_undetected">Time undetected: </label>
      <div class="controls controls-row">
        <input class="input-medium" id="undetecttime" type="text"
          value="<?php echo $undetecttime; ?>"
          disabled/>
      </div>
   </div>

   <div class="control-group">
    <label class="control-label" id="resolve_time">Time to resolve: </label>
    <div class="controls controls-row">
      <input class="input-medium" id="resolvetime" type="text"
        value="<?php echo $resolvetime; ?>"
          disabled/>
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
                $view_file = 'features/' . $feature['name'] . '/views/' . $feature['name'] . '.php';
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
<div class="row-fluid editable_hidden" style="display:none;">
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
<br/>
<br/>
</div>
</div>



<script type="text/javascript" src="/assets/js/bootstrap.js"></script>
<script type="text/javascript" src="/assets/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/assets/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="/assets/js/jquery.timeentry.min.js"></script>
<script type="text/javascript" src="/assets/js/api.js"></script>
<script type="text/javascript" src="/assets/js/diff_match_patch.js"></script>
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
                // check if asset exists on an external domain
                if (strpos($css_file, "https://") === false && strpos($css_file, "http://") === false) {
                    echo "<link rel=\"stylesheet\" href=\"/features/{$feature_name}/css/{$css_file}\" />";
                } else {
                    echo "<link rel=\"stylesheet\" href=\"{$css_file}\" />";
                }
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
                // check if asset exists on an external domain
                if (strpos($js_file, "https://") === false && strpos($js_file, "http://") === false) {
                    echo "<script type=\"text/javascript\" src=\"/features/{$feature_name}/js/{$js_file}\"></script>";
                } else {
                    echo "<script type=\"text/javascript\" src=\"{$js_file}\"></script>";
                }
            }
        }
    }
?>
