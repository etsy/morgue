# morgue [![Build Status](https://travis-ci.org/etsy/morgue.png?branch=master)](https://travis-ci.org/etsy/morgue)
## a safe place for all your postmortem data


## Overview
This is a PHP based web application to help manage your postmortems. It has a
pluggable feature system and can pull in related information from IRC and JIRA
as well as storing relevant links and graphs. This [talk][1] from DevOpsDays NYC
2013 gives an introduction and shows some of its features.

## Morgue tour

![Morgue index page](https://raw.github.com/etsy/morgue/master/assets/img/screenshots/morgue_index.png)

![Creating a new Post Mortem](https://raw.github.com/etsy/morgue/master/assets/img/screenshots/morgue_create.png)

![Editing a Post Mortem](https://raw.github.com/etsy/morgue/master/assets/img/screenshots/morgue_edit.png)

![Timeline of events](https://raw.github.com/etsy/morgue/master/assets/img/screenshots/morgue_timeline.png)

![Remediations items](https://raw.github.com/etsy/morgue/master/assets/img/screenshots/morgue_remediation.png)

![History tracking](https://raw.github.com/etsy/morgue/master/assets/img/screenshots/morgue_history.png)


## Setup

### Requirements
- PHP 5.3 or higher
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
morgue password for the dev environment (defined in config/development.json):
```
CREATE DATABASE morgue;
CREATE USER 'morgue'@'localhost' IDENTIFIED BY 'morgue';
GRANT ALL ON morgue.* TO 'morgue'@'localhost';
```

Then add the schema to the database:
```
mysql -p -u morgue -h localhost morgue < schemas/postmortems.sql
```

Note : add any additional schemas you may use:
```
mysql -p -u morgue -h localhost morgue < schemas/images.sql
mysql -p -u morgue -h localhost morgue < schemas/jira.sql
mysql -p -u morgue -h localhost morgue < schemas/links.sql
mysql -p -u morgue -h localhost morgue < schemas/irc.sql
```

### Start a development server

Using PHP built-in webserver it is possible to start quickly view what morgue does with the following command

```
MORGUE_ENVIRONMENT=development php -d include_path=".:./features" -S localhost:8000
```

Open http://localhost:8000 to view Morgue

## Configuration

### JIRA feature

**baseurl** the base URL to your jira installation (**use https** if you are using a secured JIRA installation)  
**username** username for a user with viewing credentials  
**password** password for a user with viewing credentials  
**additional_fields** mapping of fields to display in morgue (other than key, summay, assignee, status)  

```
    {   "name": "jira",
        "enabled": "on",
        "baseurl": "https://jira.foo.com",
        "username": "jira_morgue",
        "password": "jira_morgue",
        "additional_fields" : {
            "Due Date" : "duedate",
            "Some Custom Field" : "customfield_1234"
        }
    },
```

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




[1]: http://vimeo.com/77206751

