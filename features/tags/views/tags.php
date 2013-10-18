<!-- Tags -->
<?php
    $tags = Postmortem::get_tags_for_event($id);
    if ($tags["status"] == Postmortem::OK) {
        $tags = $tags["values"];
    } else {
        $tags = array();
    }
?>

<div class="row-fluid">
<!-- Editable Controls -->
  <form class="form-horizontal">
  <div class="span6">
    <div class="control-group">
      <label class="control-label">Tags: </label>
      <div class="controls controls-row">

         <input type="text" placeholder="Enter Tag(s), separated by commas (i.e. leveldb, memcache)" id="tags" name="tags" class="input-xxlarge" onblur="addTags()" />
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
          <p id="tag_paragraph">
            <?php
            foreach ($tags as $tag) {
              echo "<span class=\"label tag\" id=\"tag-".$tag['id']."\">".$tag['title']."  <a>&times;</a></span>";
            }
            ?>
          </p>
        </div>
    </div>
</form>
</div>
<!-- end -->
<div class="row-fluid"><br/></div>
