# morgue [![Build Status](https://travis-ci.org/etsy/morgue.png?branch=master)](https://travis-ci.org/etsy/morgue) [![Code Climate](https://codeclimate.com/github/etsy/morgue.png)](https://codeclimate.com/github/etsy/morgue)
## a safe place for all your postmortem data


## Overview
This is a PHP based web application to help manage your postmortems. It has a
pluggable feature system and can pull in related information from IRC and JIRA
as well as storing relevant links and graphs. This [talk][1] from DevOpsDays NYC
2013 gives an introduction and shows some of its features.

You can also join `#morgue` on Freenode IRC if you have questions.

## Morgue tour

### Index page
![Morgue index page](assets/img/screenshots/morgue_index.png)

### Creating a new post mortem
![Creating a new Post Mortem](assets/img/screenshots/morgue_create.png)

### Live edit page
![Editing a Post Mortem](assets/img/screenshots/morgue_edit.png)

![Timeline of events](assets/img/screenshots/morgue_timeline.png)

![Remediations items](assets/img/screenshots/morgue_remediation.png)

![History tracking](assets/img/screenshots/morgue_history.png)


## Setup

### Requirements
- PHP 5.3 or higher
- MySQL 5.5 or higher
- Apache
- mod_rewrite

### Create a morgue configuration file

In the cloned repo, use the **example.json** file as a template to create your 
own configuration file.

```
cp config/example.json config/development.json
``` 

### Apache
This is a basic example for an Apache vhost. The `MORGUE_ENVIRONMENT` variable
is used to determine which config file to use (see above step).

```
    <VirtualHost *:80>
      ServerName   morgue.hostname.com

      DocumentRoot /var/www/morgue/htdocs

      <Directory /var/www/morgue/htdocs>
        AllowOverride All
      </Directory>

      SetEnv MORGUE_ENVIRONMENT development

      php_value include_path "/var/www/morgue:/var/www/morgue/features"
    </VirtualHost>
```

Restart apache and hit the servername you defined above.

### MySQL
Create a database named morgue and give access to the morgue user with the
morgue password defined in the config file you created at step 1
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

Using PHP built-in webserver it is possible to start quickly view what morgue does with the following command run from the document root (www).

```
cd www
MORGUE_ENVIRONMENT=development php -d include_path=".:$(dirname `pwd`):$(dirname `pwd`)/features" -S localhost:8000
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

### IRC Feature

When enabled, the irc features allow the reporting of the IRC channels 
discussions were happening during the event. The postmortem tools tries to 
retrieve the list of channels in 2 ways:
 - calling the **'morgue_get_irc_channels_list'** if it exits (this function is
 expected to return an array of strings)
 - retrieving the "channels" array from the 'irc' feature config stanza

### IRC Log feature

When the IRC feature is enabled, the channels listed for a given postmortem will
 be clickable buttons that attempt to retrieve the IRC log history. In order to
 view that history in Morgue, you need to implement the irclogs endpoint.
You can do so by:

1. Create a new feature

mkdir features/irclogs
touch features/irclogs/lib.php
touch features/irclogs/routes.php

**Note** : morgue expects both a lib.php and routes.php file in your feature.

2. Add the new feature to your config file (in the features array)


```
    {   "name": "irclogs",
        "enabled": "on",
        "endpoint": "https://path.to.irseach.endpoint"
    }
```

3. Implement the irclogs route

The irclogs route receives parameters in a get request. Morgue will query the 
irclogs endpoint with an increasing offset of **20** until it receives no data.
Regardless of how you implement that endpoint, you need to return an empty 
response when you no longer have data to feed.

The expected response from the *irclogs* endpoint is a JSon array with the 3 
elements: nick, time and message.
```
[
  {'nick':'foo','time':'10:30:03 PM', 'message':'I see foo'},
  {'nick':'bar','time':'10:35:00 PM', 'message':'Oh, I see bar'},
  {'nick':'foo','time':'10:37:34 PM', 'message':'turned out it was baz'}
]
```

 A dummy implementation could look like (content of features/irclogs/routes.php)

```
<?php

/** irclog enpoint - return IRC logs paginated by 20 entries */
$app->get('/irclogs', function () use ($app) {
    header("Content-Type: application/json");
    $start_date = $app->request()->get('start_date');
    $start_time = $app->request()->get('start_time');
    $end_date = $app->request()->get('end_date');
    $end_time = $app->request()->get('end_time');
    $timezone = $app->request()->get('timezone');
    $channel = $app->request()->get('channel');
    $offset = $app->request()->get('offset');

    if ($offset == 0) {
        $results = array(
            array('nick' => 'foo','time' => '10:55 PM', 'message' => 'bar'),
        );
    } else {
        $results = array();
    }
    echo json_encode($results);
});
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

## FAQ

### When I visit a detail event page, I just see a "loooool"

You may have created your schemas before the [pull request 19](https://github.com/etsy/morgue/pull/19) which introduced breaking schema changes.
Simply run the migration command to update your schemas:

```
    alter table postmortems change etsystatustime statustime int(11) UNSIGNED NOT NULL;
```

[1]: http://vimeo.com/77206751

