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
         <input type="text" placeholder="Enter LDAP username" id="contact" name="contact" class="input-xxlarge" value="" />
      </div>
    </div>
    </div>
    </form>

</div>

<!-- display it -->
<div class="row-fluid">
  <form class="form-horizontal">
  <div class="span6">
        <div class="controls controls-row" id="the_contact">
            <?php
                if (isset($contact) && $contact !="") {
                    $contact_html = Contact::get_html_for_user($contact);
                    echo "<span id=\"contact_anchor\">$contact_html</span>";
                }
            ?>
        </div>
    </div>
</form>
</div>
<!-- end -->
<div class="row-fluid"><br/></div>
