#!/bin/sh

/usr/sbin/nginx
/usr/local/bin/php -d 'include_path=/usr/src/app:/usr/src/app/features' \
              -d 'date.timezone="Europe/London"' \
              -t /usr/src/app/htdocs \
              -S 127.0.0.1:9000
