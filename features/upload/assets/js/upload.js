// Disabling autoDiscover, otherwise Dropzone will try to attach twice.
Dropzone.autoDiscover = false;
 
$(function() {
  console.log("Loaded upload.js");

  var myDropzone = new Dropzone("#my-dropzone");
  myDropzone.on("success", function (d, e) {
    console.log("dropzone success with " + d); 
    console.log(d);
    console.log(JSON.parse(e)[0].image_link);
  });


});
