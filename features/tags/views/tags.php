<!-- Tags -->
<div class="row-fluid">
  <legend>Tags</legend>
  <input type="text" placeholder="Enter tags, separated by commas" id="tags" name="tags" class="input-xxlarge" onBlur="addTags()">
  <p style="padding-top:10px" id="tag_paragraph">
    <?php
      foreach($event["tags"] as $tag) {
        echo "<span class=\"label tag\" id=\"tag-$tag[id]\">$tag[title]";
        echo "  <a>&times;</a>";
        echo "</span>";
      }
    ?>
  </p>
</div>

<div class="row-fluid"><br/></div>
