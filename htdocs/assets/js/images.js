/* Set up Dropzone file upload */

// $("div#dzupload").dropzone({ url: "/file/post" });

/* End of Dropzone file upload setup */


function renderImage() {
    var img = $("#image_url");
    var img_url = img.attr("value");
    if (img_url !== '') {
      store_image_for_event(get_current_event_id(), img_url, function(data) {
        var id = "";
        data = JSON.parse(data);
        for (var i in data) {
          if (data[i].image_link == img_url) {
            id = data[i].id;
          }
        }

        var node = $("<div></div>");
        node.addClass("thumbnail");

        var close = $("<span></span>");
        close.attr("id", "image-"+id);
        close.addClass("close");
        close.html("&times;");

        var anchor = $("<a></a>");
        anchor.attr("href", img_url);
        anchor.attr("target", "new_tab");

        var imgNode = $("<img></img>");
        imgNode.attr("src", img_url);

        node.append(close);
        node.append(anchor);
        anchor.append(imgNode);

        $("#image").append(node);
        img.attr("value", '');
      });
    }
}

$('#image').on('click', 'span.close', function () {
  $(this).fadeTo(100, 0);
  confirm_delete("Are you sure you want to delete this image?", $(this).closest('div'), this, function() {
    var self = $(this);
    var id = $(this).attr("id").split("-")[1];
    delete_image_for_event(get_current_event_id(), id, function(data) {
      var image = self.parents('.thumbnail');
      image.fadeOut(400, function() {
        image.remove();
      });
    });
  }, function() {
    $(this).fadeTo(100, 0.20);
  });
});
