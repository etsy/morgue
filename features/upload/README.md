Upload Feature
---

## Overview

We don't want to end up in the business of file management.

We do want to help smooth out the process of adding files to an event.

What we're aiming for is something that will:
- Accept images (drag + drop)
- Pass images from the web-browser to the server.
- Upload image via webDAV to a configured host.  
- Store an accessible URL for the image using Morgue's image feature


## Front End

There are a number of toolkits and frameworks that could be used here.
We just picked Dropzone not based on too much.  If you know of a good
soution to use here, please feel free to contribute.

[Dropzone.js](http://www.dropzonejs.com/)

## The "Back End"

Looking at [sabre/dav](http://sabre.io/dav/) as webdav client

