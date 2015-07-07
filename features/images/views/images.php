<!-- Images -->
<?php
$images = Images::get_images_for_event($id);
if ($images['status'] == Images::OK) {
    $images = $images['values'];
} else {
    $images = array();
}
?>
<div class="row-fluid">
<legend>Images</legend>
<input type="text" placeholder="Enter image URL" id="image_url" name="image_url" class="input-xxlarge editable_hidden editable" value="<?php echo isset($image_url) ? $image_url : '' ?>" onblur="renderImage()"  style="display:none;"/>
<div id="image" class="image-sizing">
<?php foreach ($images as $image) {
    echo "<div class=\"thumbnail\">";
    echo "<span id=\"image-$image[id]\" class=\"close editable_hidden\" style=\"display:none;\">&times;</span>";
    echo "<a href=\"$image[image_link]\" target=\"new_tab\">";
    echo "<img src=\"$image[image_link]\">";
    echo "</a>";
    echo "</div>";
}
?>
</div>
<div class="row-fluid"><br/></div>
</div>
