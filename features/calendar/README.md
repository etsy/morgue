## Setup

### Get access to Google Calendar API
1. Signup for a [Google Developer Account](https://developers.google.com/)
2. Go to the [developers console](https://console.developers.google.com/project) and create a new project.
3. Select 'APIs & auth' -> 'Credentials' and create both a new Client ID and API key.

### Create a calendar to scheduele Post Mortems
1. Go to https://www.google.com/calendar and select the dropdown next 'My calendars' to create a new calendar.
2. Make sure your Morgue users will have the ability to edit this calendar. 
3. Go to the settings for this calendar and note the Calendar ID under the Calendar Address section.

### Edit your config.json file
Add calendar to the edit page features
```
"edit_page_features" : [
    "calendar",
    ...
```

Add calendar to features array, replacing CLIENTID, APIKEY, and CALENDARID. 
```
"features" : [
    {   "name": "calendar",
        "enabled": "on",
        "navbar" : "on",
        "custom_css_assets" : ["calendar.css"],
        "custom_js_assets" : ["calendar.js", "https://apis.google.com/js/client.js?onload=loadCal"],
        "clientId" : "CLIENTID",
        "apiKey" : "APIKEY",
        "scopes" : ["https://www.googleapis.com/auth/calendar"],
        "id" : "CALENDARID"
    },
    ...
```
