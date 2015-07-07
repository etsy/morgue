<!-- Tags -->
<div class="row-fluid">
  <legend>Tags</legend>
  <input type="text" placeholder="Enter tags, separated by commas" id="tags" name="tags" class="input-xxlarge editable_hidden" style="display:none;" onBlur="addTags()">
  <p style="padding-top:10px" id="tag_paragraph">
    <?php
      foreach($event["tags"] as $tag) {
        echo "<span class=\"label tag\" id=\"tag-$tag[id]\">$tag[title]";
        echo "  <a class=\"editable_hidden\" style=\"display:none;\">&times;</a>";
        echo "</span>";
      }
    ?>
  </p>
</div>

<div class="row-fluid"><br/></div>
