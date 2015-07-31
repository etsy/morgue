<!-- gcal-->
<div class="row-fluid">
<!-- Editable Controls -->
  <form class="form-horizontal">
  <div class="span6">
    <div class="control-group">
      <label class="control-label" id="event-start-time">Contact: </label>
      <div class="controls controls-row">
         <?php
            $config = Configuration::get_configuration("contact");
            if (isset($config['lookup_url'])) {
                $contact_lookup_url = $config['lookup_url'];
                echo "<input type=\"hidden\" name=\"contact_lookup_url\" value=\"$contact_lookup_url\" />";
            }
         ?>
         <input type="text" placeholder="Enter contact username" id="contact" name="contact" class="input-xxlarge editable editable_hidden" value="" style="display:none;"/>

         <?php
                if (isset($contact) && $contact !="") {
                    $contact_html = Contact::get_html_for_user($contact);
                    echo "<div id=\"contact_anchor\">$contact_html</div>";
                }
            ?>

      </div>
    </div>
    </div>
    </form>

</div>

