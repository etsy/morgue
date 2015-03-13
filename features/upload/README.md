Upload Feature
---

### Overview

- We don't want to end up in the business of file management.
- We do want to smooth out the process of associating files to events.

What we're aiming for is something that will:
- Accept images (drag + drop)
- Pass images from the web-browser to the web-server.
- Upload image via webDAV or other from the web-server to a remote host.  
- Store an accessible URL for the image using Morgue's image feature
- Update the current page to show the image .. and hide the preview in the Dropzone.

### Issues
- Sabre/dav requires PHP 5.4 or higher.  Morgue current supports PHP 5.3.


### Front End
>>>>>>> split upload composer to its own json; change UI DnD anyhwere now; fix

There are a number of toolkits and frameworks that could be used here.
We just picked Dropzone not based on too much.  If you know of a good
soution to use here, please feel free to contribute.

[Dropzone.js](http://www.dropzonejs.com/)

Nicely, Dropzone lets us hook into events.
We hook inot the "success" event.  Its second argument is the server's 
response, which is how we'll hand off from php to js.


### The "Back End"

The back end "driver" gets passed the uploads_driver_options object as
described in the example config below.

The driver needs to implement a method called ```send($file_path, $event_id)```
which does the work of sending the file.  send is expected to return an array:

```
return array(
	"location"	=> "http://where.i.can.see/the/uploaded.image
	"status"	=> 204
);
```

As a first and default implementaion of upload, we've got WebDAV.  We're using
sabre/dav from composer.  It requires php 5.4 and above.  To use it you need to
add ```"sabre/dav": "~2.1.1",``` in a composer.json that gets included.  For now
its in THE composer.json.


### The other back end

We also need to save the association of the image url with the event in the database.
We can use Morgue's Image feature  ```Images::save_images_for_event($id, Array)```.

Even better, by calling ```renderImage()`` in javascript, we trigger the whole save and
display behavior we need anyhow!

### Config example

Add "upload" to the ```edit_page_features``` array.  Adding it right after the
```images``` feature works well.

Also Add a ```upload_dir``` key to your config.  That is where this feature will
upload files to locally before sending them to a new home in a cloud.

TODO: Cron up deleting these uploaded files; or find a way to not save the file and
just re-upload the tmp file.

```
{   "name": "upload",
    "enabled": "on",
    "custom_js_assets": ["dropzone.min.js", "upload.js"],
    "custom_css_assets": ["dropzone.min.css"],
    "upload_driver_options": {
        "url": "http://my.server.home/",
        "username": "webdav",
        "password": "webdav",
        "proxy": false 
    },
    "upload_driver": "webdav"
}
```
