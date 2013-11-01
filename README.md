# morgue [![Build Status](https://travis-ci.org/etsy/morgue.png?branch=master)](https://travis-ci.org/etsy/morgue)
## a safe place for all your postmortem data


## Overview
This is a PHP based web application to help manage your postmortems.


## Setup

### Requirements
- PHP 5.4 or higher
- MySQL 5.5 or higher
- Apache
- mod_rewrite

### Apache
This is a basic example for an Apache vhost. The `MORGUE_ENVIRONMENT` variable
is used to determine which config file to use.

```
    <VirtualHost *:80>
      ServerName   morgue.hostname.com

      DocumentRoot /var/www/morgue

      <Directory /var/www/morgue>
        AllowOverride All
      </Directory>

      SetEnv MORGUE_ENVIRONMENT development

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

Then add the schema's to the database:
```
mysql -p -u morgue -h localhost morgue < schemas/morgue.sql
mysql -p -u morgue -h localhost morgue < schemas/irc.sql
mysql -p -u morgue -h localhost morgue < schemas/jira.sql
mysql -p -u morgue -h localhost morgue < schemas/images.sql
mysql -p -u morgue -h localhost morgue < schemas/links.sql
```

### Start a development server

Using PHP built-in webserver it is possible to start quickly view what morgue does with the following command

```
MORGUE_ENVIRONMENT=development php -d include_path=".:./features" -S localhost:8000
```

Open http://localhost:8000 to view Morgue

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

