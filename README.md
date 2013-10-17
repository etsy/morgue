# morgue - a safe place for all your postmortem data

## Overview
This is a PHP based web application to help manage your postmortems.


## Setup
### Apache

```
    <VirtualHost *:80>
      ServerName   morgue.hostname.com

      DocumentRoot /var/www/morgue

      <Directory /var/www/morgue>
        AllowOverride All
      </Directory>

      php_value include_path ".:/usr/share/pear:./features"
    </VirtualHost>
```

Restart apache and hit the servername you defined above.

### MySQL
Create a database named morgue and give access to the morgue user with the
morgue password for the dev environment:
```
CREATE DATABASE morgue;
CREATE USER 'morgue'@'localhost' IDENTIFIED BY 'morgue';
GRANT ALL ON morgue.* TO 'morgue'@'localhost';
```

Then add the schema to the database:
```
mysql -p -u morgue -h localhost morgue < schemas/morgue.sql
```

## Authentication

At the moment, morgue uses `Basic Authentication` to handle authenticating the user. The default username is `default_user`.

You can set a user in `$_SERVER['PHP_AUTH_USER']` from nginx in the location block:

    location ~ \.php {
        // unrelated configuration
        fastcgi_param  PHP_AUTH_USER  derp;
    }

Apache has provisions for specificying variables within the `VirtualHost` directive:

    <VirtualHost>
    SetEnv PHP_AUTH_USER derp
    </VirtualHost>

Note that you can always use Basic Authentication instead of manually specifying a user.

> If you are comfortable with Lua+Nginx, [this gist](https://gist.github.com/lusis/6005442) may be useful for setting up single-sign-on.

## Tests
You can run the unit test suite with:
```
make unittests
```

## Contribute

1. Fork the repository
2. Hack away
3. Add tests so we don't accidentally break something in the future
4. Push the branch up to GitHub (bonus points for topic branches)
5. Send a pull request to the etsy/morgue project.

