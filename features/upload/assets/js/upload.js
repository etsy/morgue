// Disabling autoDiscover, otherwise Dropzone will try to attach twice.
Dropzone.autoDiscover = false;
 
$(function() {
  console.log("Loaded upload.js");
  var myDropzone = new Dropzone("#my-dropzone");

  myDropzone.on("success", function (d, e) {
    var url = JSON.parse(e).location;
    console.log("dropzone success with " + url); 
    var img_input = $("#image_url")[0];
    img_input.value = url
    renderImage();
    myDropzone.removeAllFiles(true);

  });


  myDropzone.on("error", function (e) {
  console.log("error");
  });
});
