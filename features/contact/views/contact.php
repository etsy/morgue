<!-- gcal-->
<div class="row-fluid">
<!-- Editable Controls -->
  <form class="form-horizontal">
  <div class="span6">
    <div class="control-group">
      <label class="control-label" id="event-start-time">Contact: </label>
      <div class="controls controls-row">
        
         <input type="text" placeholder="Enter LDAP username" id="contact" name="contact" class="input-xxlarge" value=""  />
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
                if (isset($contact) && $contact!="" ){
                    echo"<a id=\"contact_anchor\" href=\"https://atlas.etsycorp.com/staff/$contact\" target=\"_new\">$contact</a>";
                }
            ?>
        </div>
    </div>
</form>
</div>
<!-- end -->
<div class="row-fluid"><br/></div>
